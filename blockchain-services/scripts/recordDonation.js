// blockchain-services/scripts/recordDonation.js

const { ethers } = require("ethers");
const path = require('path');
require("dotenv").config({ path: path.join(__dirname, '../.env') });

// Complete ABI for DonationRecorder contract
const CONTRACT_ABI = [
    {
        inputs: [],
        stateMutability: "nonpayable",
        type: "constructor",
    },
    {
        anonymous: false,
        inputs: [
            {
                indexed: true,
                internalType: "address",
                name: "admin",
                type: "address",
            },
        ],
        name: "AdminAdded",
        type: "event",
    },
    {
        anonymous: false,
        inputs: [
            {
                indexed: true,
                internalType: "address",
                name: "admin",
                type: "address",
            },
        ],
        name: "AdminRemoved",
        type: "event",
    },
    {
        anonymous: false,
        inputs: [
            {
                indexed: false,
                internalType: "string",
                name: "trackingCode",
                type: "string",
            },
            {
                indexed: false,
                internalType: "uint256",
                name: "amount",
                type: "uint256",
            },
            {
                indexed: false,
                internalType: "string",
                name: "barangayId",
                type: "string",
            },
            {
                indexed: false,
                internalType: "enum DonationRecorder.DonationType",
                name: "donationType",
                type: "uint8",
            },
            {
                indexed: false,
                internalType: "uint256",
                name: "timestamp",
                type: "uint256",
            },
        ],
        name: "DonationRecorded",
        type: "event",
    },
    {
        anonymous: false,
        inputs: [
            {
                indexed: true,
                internalType: "address",
                name: "previousOwner",
                type: "address",
            },
            {
                indexed: true,
                internalType: "address",
                name: "newOwner",
                type: "address",
            },
        ],
        name: "OwnershipTransferred",
        type: "event",
    },
    {
        anonymous: false,
        inputs: [
            {
                indexed: false,
                internalType: "bool",
                name: "isPaused",
                type: "bool",
            },
        ],
        name: "PauseToggled",
        type: "event",
    },
    {
        inputs: [{ internalType: "address", name: "admin", type: "address" }],
        name: "addAdmin",
        outputs: [],
        stateMutability: "nonpayable",
        type: "function",
    },
    {
        inputs: [{ internalType: "address", name: "", type: "address" }],
        name: "admins",
        outputs: [{ internalType: "bool", name: "", type: "bool" }],
        stateMutability: "view",
        type: "function",
    },
    {
        inputs: [],
        name: "owner",
        outputs: [{ internalType: "address", name: "", type: "address" }],
        stateMutability: "view",
        type: "function",
    },
    {
        inputs: [],
        name: "paused",
        outputs: [{ internalType: "bool", name: "", type: "bool" }],
        stateMutability: "view",
        type: "function",
    },
    {
        inputs: [
            { internalType: "string", name: "trackingCode", type: "string" },
            { internalType: "uint256", name: "amount", type: "uint256" },
            { internalType: "string", name: "barangayId", type: "string" },
            {
                internalType: "enum DonationRecorder.DonationType",
                name: "donationType",
                type: "uint8",
            },
        ],
        name: "recordDonation",
        outputs: [],
        stateMutability: "nonpayable",
        type: "function",
    },
    {
        inputs: [{ internalType: "address", name: "admin", type: "address" }],
        name: "removeAdmin",
        outputs: [],
        stateMutability: "nonpayable",
        type: "function",
    },
    {
        inputs: [],
        name: "togglePause",
        outputs: [],
        stateMutability: "nonpayable",
        type: "function",
    },
    {
        inputs: [
            { internalType: "address", name: "newOwner", type: "address" },
        ],
        name: "transferOwnership",
        outputs: [],
        stateMutability: "nonpayable",
        type: "function",
    },
];

async function recordDonation() {
    try {
        // Get command line arguments from Laravel Job
        const args = process.argv.slice(2);

        if (args.length < 4) {
            console.error("ERROR: Missing arguments");
            console.error(
                "Usage: node recordDonation.js <trackingCode> <amount> <barangayId> <donationType>",
            );
            console.error(
                'Example: node recordDonation.js "DON-ABC123" "1000" "BRG001" "monetary"',
            );
            process.exit(1);
        }

        const [trackingCode, amountStr, barangayId, donationType] = args;

        // Validate inputs
        if (!trackingCode || trackingCode.trim() === "") {
            throw new Error("Tracking code cannot be empty");
        }
        if (!amountStr || parseFloat(amountStr) <= 0) {
            throw new Error("Amount must be greater than 0");
        }
        if (!barangayId || barangayId.trim() === "") {
            throw new Error("Barangay ID cannot be empty");
        }
        if (!["monetary", "in-kind"].includes(donationType)) {
            throw new Error('Donation type must be "monetary" or "in-kind"');
        }

        console.log("=== Recording Donation on Lisk Blockchain ===");
        console.log("Tracking Code:", trackingCode);
        console.log("Amount (PHP):", amountStr);
        console.log("Barangay:", barangayId);
        console.log("Type:", donationType);
        console.log("");

        // Store PHP amount as whole number (no decimal conversion)
        // Example: 123 PHP -> 123 on blockchain
        // If user enters 123.50, we'll round down to 123
        const amount = Math.floor(parseFloat(amountStr));

        // Map donation type to enum (0 = Money, 1 = Goods)
        const typeEnum = donationType === "monetary" ? 0 : 1;

        // Check environment variables
        if (!process.env.LISK_RPC_URL) {
            throw new Error("LISK_RPC_URL not set in .env file");
        }

        const privateKey =
            process.env.PRIVATE_KEY ||
            process.env.LISK_PRIVATE_KEY ||
            process.env.METAMASK_PRIVATE_KEY;
        if (!privateKey) {
            throw new Error(
                "PRIVATE_KEY (or LISK_PRIVATE_KEY or METAMASK_PRIVATE_KEY) not set in .env file",
            );
        }

        if (!process.env.CONTRACT_ADDRESS) {
            throw new Error("CONTRACT_ADDRESS not set in .env file");
        }

        // Setup provider (Connect to Lisk Sepolia Testnet) with retry logic
        console.log("Connecting to Lisk Sepolia RPC...");

        let provider;
        let connected = false;
        const maxRetries = 5;
        const retryDelays = [2000, 5000, 10000, 15000, 30000]; // 2s, 5s, 10s, 15s, 30s

        for (let attempt = 0; attempt < maxRetries; attempt++) {
            try {
                provider = new ethers.providers.JsonRpcProvider(
                    process.env.LISK_RPC_URL,
                );
                const blockNumber = await provider.getBlockNumber();
                console.log(
                    "✓ Connected to Lisk Sepolia (Block:",
                    blockNumber + ")",
                );
                connected = true;
                break;
            } catch (error) {
                console.log(
                    `⚠️  Connection attempt ${attempt + 1}/${maxRetries} failed:`,
                    error.message,
                );

                if (attempt < maxRetries - 1) {
                    const delay = retryDelays[attempt];
                    console.log(`   Retrying in ${delay / 1000} seconds...`);
                    await new Promise((resolve) => setTimeout(resolve, delay));
                } else {
                    throw new Error(
                        "Failed to connect to Lisk RPC after " +
                            maxRetries +
                            " attempts: " +
                            error.message,
                    );
                }
            }
        }

        if (!connected) {
            throw new Error("Failed to establish connection to Lisk RPC");
        }

        // Create wallet from private key
        const wallet = new ethers.Wallet(privateKey, provider);
        console.log("Wallet Address:", wallet.address);

        // Check wallet balance
        const balance = await provider.getBalance(wallet.address);
        const balanceInEth = ethers.utils.formatEther(balance);
        console.log("Wallet Balance:", balanceInEth, "ETH");

        if (balance.isZero()) {
            console.error("⚠️  WARNING: Wallet has 0 balance!");
            console.error(
                "Get test ETH from: https://sepolia-faucet.lisk.com/",
            );
            throw new Error("Insufficient balance for gas fees");
        }

        // Get contract address
        const contractAddress = process.env.CONTRACT_ADDRESS;
        console.log("Contract Address:", contractAddress);
        console.log("");

        // Create contract instance
        const contract = new ethers.Contract(
            contractAddress,
            CONTRACT_ABI,
            wallet,
        );

        // Validate contract and permissions
        console.log("Validating permissions...");

        try {
            // Check if wallet is an admin
            const isAdmin = await contract.admins(wallet.address);
            const owner = await contract.owner();
            const isOwner =
                owner.toLowerCase() === wallet.address.toLowerCase();

            if (!isAdmin && !isOwner) {
                console.error("❌ ERROR: Wallet is not authorized");
                console.error("");
                console.error("Your wallet:", wallet.address);
                console.error("Contract owner:", owner);
                console.error("");
                console.error("Solution: Add your wallet as admin in Remix:");
                console.error("1. Go to your deployed contract in Remix");
                console.error('2. Find the "addAdmin" function');
                console.error("3. Enter your wallet address:", wallet.address);
                console.error('4. Click "transact"');
                throw new Error("Wallet not authorized as admin or owner");
            }

            console.log(
                "✓ Wallet is authorized",
                isOwner ? "(Owner)" : "(Admin)",
            );

            // Check if contract is paused
            const isPaused = await contract.paused();
            if (isPaused) {
                console.error("❌ Contract is paused");
                console.error(
                    "Solution: Call togglePause() in Remix to unpause",
                );
                throw new Error("Contract is paused");
            }

            console.log("✓ Contract is not paused");
            console.log("");
        } catch (error) {
            if (
                error.message.includes("not authorized") ||
                error.message.includes("paused")
            ) {
                throw error;
            }
            throw new Error("Failed to validate contract: " + error.message);
        }

        // Estimate gas
        console.log("Estimating gas...");
        try {
            const estimatedGas = await contract.recordDonation.estimateGas(
                trackingCode,
                amount,
                barangayId,
                typeEnum,
            );
            console.log("Estimated Gas:", estimatedGas.toString());
        } catch (error) {
            console.error("⚠️  Gas estimation failed:", error.message);
        }

        // Send transaction with retry logic
        console.log("Sending transaction...");

        let tx;
        let receipt;
        const maxTxRetries = 3;

        for (let txAttempt = 0; txAttempt < maxTxRetries; txAttempt++) {
            try {
                tx = await contract.recordDonation(
                    trackingCode,
                    amount,
                    barangayId,
                    typeEnum,
                    {
                        gasLimit: 500000, // Safe gas limit
                    },
                );

                console.log("✓ Transaction sent:", tx.hash);
                console.log("Waiting for confirmation...");

                // Wait for transaction to be mined
                receipt = await tx.wait();
                break; // Success, exit retry loop
            } catch (error) {
                console.log(
                    `⚠️  Transaction attempt ${txAttempt + 1}/${maxTxRetries} failed:`,
                    error.message,
                );

                if (txAttempt < maxTxRetries - 1) {
                    console.log(`   Retrying transaction in 10 seconds...`);
                    await new Promise((resolve) => setTimeout(resolve, 10000));
                } else {
                    throw error; // Re-throw on final attempt
                }
            }
        }

        console.log("");
        console.log("=== ✅ Transaction Confirmed ===");
        console.log("Block Number:", receipt.blockNumber);
        console.log("Gas Used:", receipt.gasUsed.toString());
        console.log("Transaction Hash:", tx.hash);
        console.log(
            "Explorer:",
            `https://sepolia-blockscout.lisk.com/tx/${tx.hash}`,
        );
        console.log("");

        // Output just the hash for Laravel to parse
        console.log(tx.hash);

        process.exit(0);
    } catch (error) {
        console.error("");
        console.error("=== ❌ ERROR ===");
        console.error("Message:", error.message);
        console.error("");

        if (error.code === "CALL_EXCEPTION") {
            console.error("This is a smart contract error. Possible reasons:");
            console.error(
                "1. Wallet is not an admin - Add wallet as admin in Remix",
            );
            console.error(
                "2. Contract is paused - Call togglePause() in Remix",
            );
            console.error("3. Invalid parameters passed to function");
            console.error("4. Contract address is wrong");
        } else if (error.code === "INSUFFICIENT_FUNDS") {
            console.error("Not enough ETH for gas fees.");
            console.error(
                "Get test ETH from: https://sepolia-faucet.lisk.com/",
            );
        } else if (error.code === "NETWORK_ERROR") {
            console.error("Network connection failed.");
            console.error("Check your internet connection and RPC URL.");
        }

        if (error.transaction) {
            console.error("");
            console.error("Transaction details:", error.transaction);
        }

        console.error("");
        console.error("Need help? Check the setup guide or contact support.");

        process.exit(1);
    }
}

// Run the function
recordDonation();
