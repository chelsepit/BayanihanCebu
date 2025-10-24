const { ethers } = require("ethers");
const fs = require("fs");
const path = require("path");
require("dotenv").config({ path: path.join(__dirname, '../.env') });

const trackingCode = process.argv[2];

if (!trackingCode) {
    console.error("Usage: node checkOnchainHash.js <trackingCode>");
    process.exit(1);
}

async function checkHash() {
    try {
        const CONTRACT_ADDRESS = process.env.DONATION_RECORDER_CONTRACT_ADDRESS || process.env.CONTRACT_ADDRESS;
        const RPC_URL = process.env.WEB3_PROVIDER_URL || process.env.SEPOLIA_RPC_URL;
        const CONTRACT_ABI_PATH = process.env.CONTRACT_ABI_PATH;

        const provider = new ethers.providers.JsonRpcProvider(RPC_URL);
        const abiPath = path.join(__dirname, '..', CONTRACT_ABI_PATH);
        const abi = JSON.parse(fs.readFileSync(abiPath, "utf8"));
        const contract = new ethers.Contract(CONTRACT_ADDRESS, abi, provider);

        const donation = await contract.getDonation(trackingCode);

        console.log("=== BLOCKCHAIN DATA ===");
        console.log("Tracking Code:", donation.trackingCode);
        console.log("Type:", donation.donationType === 0 ? "monetary" : "goods");
        console.log("Amount:", donation.amount.toString());
        console.log("Barangay:", donation.barangay);
        console.log("Timestamp:", donation.timestamp.toString());
        console.log("Off-Chain Hash:", donation.offChainHash);
        console.log("\n=== HASH BYTES ===");
        console.log("Length:", donation.offChainHash.length);
        console.log("First 10 chars:", donation.offChainHash.substring(0, 10));

    } catch (error) {
        console.error("Error:", error.message);
        process.exit(1);
    }
}

checkHash();
