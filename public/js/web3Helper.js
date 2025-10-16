const LISK_CONFIG = {
    chainId: '0x106a',
    chainName: 'Lisk Sepolia Testnet',
    rpcUrls: ['https://rpc.sepolia-api.lisk.com'],
    blockExplorerUrls: ['https://sepolia-blockscout.lisk.com'],
    nativeCurrency: {
        name: 'Ethereum',
        symbol: 'ETH',
        decimals: 18
    }
};

const BAYANIHAN_WALLET = '0x33D2C101f9b48347DD1E955152704898DE9D3c9C';
const PHP_TO_ETH_RATE = 0.00002;

function isMetaMaskInstalled() {
    return typeof window.ethereum !== 'undefined';
}

async function connectWallet() {
    try {
        if (!isMetaMaskInstalled()) {
            return {
                success: false,
                error: 'Please install MetaMask extension first!'
            };
        }

        const accounts = await window.ethereum.request({
            method: 'eth_requestAccounts'
        });

        if (accounts.length === 0) {
            return {
                success: false,
                error: 'No accounts found'
            };
        }

        const chainId = await window.ethereum.request({
            method: 'eth_chainId'
        });

        if (chainId !== LISK_CONFIG.chainId) {
            const switched = await switchToLiskSepolia();
            if (!switched.success) {
                return switched;
            }
        }

        return {
            success: true,
            address: accounts[0]
        };

    } catch (error) {
        return {
            success: false,
            error: error.message
        };
    }
}

async function switchToLiskSepolia() {
    try {
        await window.ethereum.request({
            method: 'wallet_switchEthereumChain',
            params: [{ chainId: LISK_CONFIG.chainId }]
        });
        return { success: true };
    } catch (switchError) {
        if (switchError.code === 4902) {
            try {
                await window.ethereum.request({
                    method: 'wallet_addEthereumChain',
                    params: [LISK_CONFIG]
                });
                return { success: true };
            } catch (addError) {
                return {
                    success: false,
                    error: 'Failed to add Lisk Sepolia network'
                };
            }
        }
        return {
            success: false,
            error: 'Failed to switch network'
        };
    }
}

async function sendDonation(amountInPHP, barangayName) {
    try {
        const connection = await connectWallet();
        if (!connection.success) {
            return connection;
        }

        const ethAmount = amountInPHP * PHP_TO_ETH_RATE;
        const valueInWei = '0x' + Math.floor(ethAmount * 1e18).toString(16);

        const metadata = JSON.stringify({
            platform: 'BayanihanCebu',
            amount_php: amountInPHP,
            barangay: barangayName,
            timestamp: Date.now()
        });

        const dataHex = '0x' + Array.from(
            new TextEncoder().encode(metadata)
        ).map(b => b.toString(16).padStart(2, '0')).join('');

        const txHash = await window.ethereum.request({
            method: 'eth_sendTransaction',
            params: [{
                from: connection.address,
                to: BAYANIHAN_WALLET,
                value: valueInWei,
                data: dataHex
            }]
        });

        let attempts = 0;
        let receipt = null;

        while (attempts < 30 && !receipt) {
            await new Promise(resolve => setTimeout(resolve, 1000));
            receipt = await window.ethereum.request({
                method: 'eth_getTransactionReceipt',
                params: [txHash]
            });
            attempts++;
        }

        return {
            success: true,
            txHash: txHash,
            explorerUrl: `${LISK_CONFIG.blockExplorerUrls[0]}/tx/${txHash}`,
            donorAddress: connection.address,
            amountEth: ethAmount.toFixed(8),
            amountPhp: amountInPHP
        };

    } catch (error) {
        if (error.code === 4001) {
            return {
                success: false,
                error: 'Transaction rejected by user'
            };
        }

        if (error.message.includes('insufficient funds')) {
            return {
                success: false,
                error: 'Insufficient ETH balance'
            };
        }

        return {
            success: false,
            error: error.message || 'Transaction failed'
        };
    }
}

async function getBalance(address) {
    try {
        if (!address) {
            const connection = await connectWallet();
            if (!connection.success) return connection;
            address = connection.address;
        }

        const balance = await window.ethereum.request({
            method: 'eth_getBalance',
            params: [address, 'latest']
        });

        const balanceInWei = parseInt(balance, 16);
        const balanceInEth = balanceInWei / 1e18;

        return {
            success: true,
            balance: balanceInEth.toFixed(6)
        };
    } catch (error) {
        return {
            success: false,
            error: error.message
        };
    }
}

function formatAddress(address) {
    if (!address) return '';
    return `${address.substring(0, 6)}...${address.substring(38)}`;
}
