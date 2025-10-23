const { ethers } = require("ethers");
require("dotenv").config();

const TX_HASH =
    process.argv[2] ||
    "0x74b79ba4862f0df40c243e816fa638bcc3164e9a4de8f40982d412139e6f6c37";

async function verifyDonation() {
    console.log("=== Verifying Donation on Blockchain ===");
    console.log("Transaction Hash:", TX_HASH);
    console.log("");

    try {
        const provider = new ethers.providers.JsonRpcProvider(
            process.env.LISK_RPC_URL,
        );

        const receipt = await provider.getTransactionReceipt(TX_HASH);

        if (!receipt) {
            console.log("❌ Transaction not found");
            return;
        }

        console.log("✓ Transaction Confirmed");
        console.log("Block Number:", receipt.blockNumber);
        console.log(
            "Status:",
            receipt.status === 1 ? "✅ Success" : "❌ Failed",
        );
        console.log("Gas Used:", receipt.gasUsed.toString());
        console.log("");

        const contractABI = [
            "event DonationRecorded(string trackingCode, uint256 amount, string barangayId, uint8 donationType, uint256 timestamp)",
        ];

        const iface = new ethers.utils.Interface(contractABI);

        console.log("=== Donation Details from Blockchain ===");
        let found = false;
        receipt.logs.forEach((log) => {
            try {
                const parsed = iface.parseLog(log);
                if (parsed && parsed.name === "DonationRecorded") {
                    found = true;
                    console.log("📋 Tracking Code:", parsed.args.trackingCode);
                    console.log(
                        "💰 Amount (PHP):",
                        parsed.args.amount.toString(),
                    );
                    console.log("🏘️  Barangay ID:", parsed.args.barangayId);
                    console.log(
                        "📦 Type:",
                        parsed.args.donationType === 0 ? "Monetary" : "In-Kind",
                    );
                    console.log(
                        "📅 Timestamp:",
                        new Date(
                            Number(parsed.args.timestamp) * 1000,
                        ).toLocaleString(),
                    );
                }
            } catch (e) {}
        });

        if (!found) {
            console.log("⚠️  No DonationRecorded event found in transaction");
        }

        console.log("");
        console.log(
            "🔗 Explorer:",
            `https://sepolia-blockscout.lisk.com/tx/${TX_HASH}`,
        );
        console.log("");
        console.log(
            found
                ? "✅ Donation is VERIFIED on blockchain"
                : "⚠️  Could not parse donation data",
        );
    } catch (error) {
        console.error("❌ Error:", error.message);
    }
}

verifyDonation();
