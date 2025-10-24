const { ethers } = require("ethers");
const fs = require("fs");
require("dotenv").config();

const trackingCode = process.argv[2];

if (!trackingCode) {
    console.error(JSON.stringify({
        success: false,
        error: "Usage: node verifyDonation.js <trackingCode>"
    }));
    process.exit(1);
}

async function verifyDonation() {
    try {
        const CONTRACT_ADDRESS = process.env.DONATION_RECORDER_CONTRACT_ADDRESS || process.env.CONTRACT_ADDRESS;
        const RPC_URL = process.env.WEB3_PROVIDER_URL || process.env.SEPOLIA_RPC_URL;
        const CONTRACT_ABI_PATH = process.env.CONTRACT_ABI_PATH;

        // Validate env variables
        if (!CONTRACT_ADDRESS || !RPC_URL || !CONTRACT_ABI_PATH) {
            throw new Error("Missing environment variables");
        }

        // Connect to provider
        const provider = new ethers.providers.JsonRpcProvider(RPC_URL);

        // Load ABI
        const abiData = JSON.parse(fs.readFileSync(CONTRACT_ABI_PATH, "utf8"));
        const contract = new ethers.Contract(CONTRACT_ADDRESS, abiData, provider);

        // Query donation by tracking code
        const donation = await contract.getDonation(trackingCode);

        // Check if donation exists (trackingCode will be empty string if not found)
        if (!donation.trackingCode || donation.trackingCode === "") {
            console.log(JSON.stringify({
                success: false,
                error: "Donation not found on blockchain",
                trackingCode: trackingCode
            }));
            return;
        }

        // Return donation data with offchain hash
        console.log(JSON.stringify({
            success: true,
            trackingCode: donation.trackingCode,
            donationType: donation.donationType === 0 ? "monetary" : "goods",
            offChainHash: donation.offChainHash,
            amount: donation.amount.toString(),
            barangayId: donation.barangayId,
            timestamp: donation.timestamp.toString(),
            isActive: donation.isActive
        }));

    } catch (error) {
        console.error(JSON.stringify({
            success: false,
            error: error.message,
            trackingCode: trackingCode
        }));
        process.exit(1);
    }
}

verifyDonation();
