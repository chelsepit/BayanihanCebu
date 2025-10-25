const { ethers } = require("ethers");
const path = require('path');
const fs = require('fs');
require("dotenv").config({ path: path.join(__dirname, '../.env'), override: true });

const [,, trackingCode, amount, barangay, donationTypeInput, offChainHashInput] = process.argv;

if (!trackingCode || !amount || !barangay || !donationTypeInput) {
  console.error("❌ Usage: node recordDonation.js <trackingCode> <amount> <barangay> <donationType> [offChainHash]");
  console.error("   Example: node recordDonation.js 'DON-123' '1000' 'BRG001' 'monetary' '0x123...'");
  process.exit(1);
}

const donationType = donationTypeInput.toLowerCase() === "monetary" ? 0 : 1;

async function main() {
  try {
    const CONTRACT_ADDRESS = process.env.DONATION_RECORDER_CONTRACT_ADDRESS || process.env.CONTRACT_ADDRESS;
    const PRIVATE_KEY = process.env.BLOCKCHAIN_ADMIN_PRIVATE_KEY || process.env.METAMASK_PRIVATE_KEY;
    const RPC_URL = process.env.WEB3_PROVIDER_URL || process.env.SEPOLIA_RPC_URL;
    const CONTRACT_ABI_PATH = process.env.CONTRACT_ABI_PATH;

    // Validate env variables
    if (!CONTRACT_ADDRESS || !PRIVATE_KEY || !RPC_URL) {
      console.error("❌ Missing environment variables in .env file!");
      process.exit(1);
    }

    console.log("🔗 Connecting to Ethereum Sepolia...");
    console.log(`   RPC: ${RPC_URL}`);
    console.log(`   Contract: ${CONTRACT_ADDRESS}`);

    // FIXED: Use ethers v5 syntax
    const provider = new ethers.providers.JsonRpcProvider(RPC_URL);
    const wallet = new ethers.Wallet(PRIVATE_KEY, provider);
    
    console.log(`   Wallet: ${wallet.address}`);

    // Check balance
    const balance = await provider.getBalance(wallet.address);
    console.log(`💰 Balance: ${ethers.utils.formatEther(balance)} ETH`);

    if (balance.isZero()) {
      console.error("❌ Wallet has no ETH! Get testnet ETH from:");
      console.error("   - https://sepoliafaucet.com/");
      console.error("   - https://www.alchemy.com/faucets/ethereum-sepolia");
      process.exit(1);
    }

    // Resolve ABI path relative to the blockchain-services directory, with fallback to default ABI
    let abiPath = path.join(__dirname, '..', CONTRACT_ABI_PATH || 'abi/DonationRecorder.json');
    if (!fs.existsSync(abiPath)) {
      // Fallback to known default location
      const fallback = path.join(__dirname, '..', 'abi', 'DonationRecorder.json');
      console.warn(`⚠️ ABI not found at ${abiPath}. Falling back to: ${fallback}`);
      abiPath = fallback;
    }
    console.log(`📄 Loading ABI from: ${abiPath}`);
    const abi = JSON.parse(fs.readFileSync(abiPath, "utf8"));
    console.log(`✅ Loaded ABI with ${abi.length} functions`);
    const contract = new ethers.Contract(CONTRACT_ADDRESS, abi, wallet);

    // Use provided hash or generate one
    const offChainHash = offChainHashInput || ethers.utils.keccak256(
      ethers.utils.toUtf8Bytes(`${trackingCode}-${Date.now()}`)
    );


    console.log("\n📦 Recording donation...");
    console.log(`   Tracking Code: ${trackingCode}`);
    console.log(`   Amount: ${amount}`);
    console.log(`   Barangay: ${barangay}`);
    console.log(`   Type: ${donationTypeInput} (${donationType})`);
    console.log(`   Off-chain Hash: ${offChainHash.substring(0, 20)}...`);
    console.log(`   Hash Source: ${offChainHashInput ? 'Provided' : 'Generated'}`);

    // Send transaction
    console.log("\n⚡ Sending transaction...");

    // Convert amount to integer (remove decimals)
    const amountInt = Math.floor(parseFloat(amount));

    const tx = await contract.recordDonation(
      trackingCode,
      donationType,
      offChainHash,
      amountInt,
      barangay,
      {
        gasLimit: 300000
      }
    );

    console.log(`✅ Transaction sent!`);
    console.log(`   TX Hash: ${tx.hash}`);
    console.log(`   View on Etherscan: https://sepolia-blockscout.lisk.com/tx/${tx.hash}`);
    console.log("\n⏳ Waiting for confirmation...");

    const receipt = await tx.wait();

    // Check if transaction was successful
    if (receipt.status === 0) {
      console.error("\n❌ Transaction FAILED!");
      console.error(`   TX Hash: ${tx.hash}`);
      console.error(`   Block: ${receipt.blockNumber}`);
      console.error(`   Gas Used: ${receipt.gasUsed.toString()}`);
      console.error("   Possible reasons:");
      console.error("   - Tracking code already exists (duplicate)");
      console.error("   - Contract validation failed");
      process.exit(1);
    }

    console.log("\n🎉 Donation recorded successfully!");
    console.log(`   Block: ${receipt.blockNumber}`);
    console.log(`   Gas Used: ${receipt.gasUsed.toString()}`);

  } catch (err) {
    console.error("\n❌ Error:", err.message);

    if (err.code === "CALL_EXCEPTION") {
      console.error("   Possible reasons:");
      console.error("   - Wallet not added as admin (use addAdmin in Remix)");
      console.error("   - Contract is paused");
      console.error("   - Invalid parameters");
      console.error("   - Duplicate tracking code (already recorded)");
    }

    if (err.error?.message) {
      console.error("   Details:", err.error.message);
    }

    process.exit(1);
  }
}

main();