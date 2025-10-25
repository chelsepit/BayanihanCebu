const { ethers } = require("ethers");
const fs = require("fs");
const path = require("path");
require("dotenv").config({ path: path.join(__dirname, '../.env'), override: true });

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

        // Validate env variables (ABI path is optional due to fallback)
        if (!CONTRACT_ADDRESS || !RPC_URL) {
            throw new Error("Missing environment variables (CONTRACT_ADDRESS or RPC_URL)");
        }

        // Connect to provider
        const provider = new ethers.providers.JsonRpcProvider(RPC_URL);

        // Load ABI with safe resolution and fallback
        let abiPath = path.join(__dirname, '..', CONTRACT_ABI_PATH || 'abi/DonationRecorder.json');
        if (!fs.existsSync(abiPath)) {
            // Do not log here; PHP expects pure JSON output
            abiPath = path.join(__dirname, '..', 'abi', 'DonationRecorder.json');
        }
        const abiData = JSON.parse(fs.readFileSync(abiPath, "utf8"));
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
