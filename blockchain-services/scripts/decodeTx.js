const { ethers } = require("ethers");
const fs = require("fs");
const path = require("path");
require("dotenv").config({ path: path.join(__dirname, '../.env') });

const txHash = process.argv[2];

if (!txHash) {
    console.error("Usage: node decodeTx.js <txHash>");
    process.exit(1);
}

async function decodeTx() {
    try {
        const RPC_URL = process.env.WEB3_PROVIDER_URL || process.env.SEPOLIA_RPC_URL;
        const CONTRACT_ABI_PATH = process.env.CONTRACT_ABI_PATH;

        const provider = new ethers.providers.JsonRpcProvider(RPC_URL);
        const abiPath = path.join(__dirname, '..', CONTRACT_ABI_PATH);
        const abi = JSON.parse(fs.readFileSync(abiPath, "utf8"));

        const iface = new ethers.utils.Interface(abi);

        const tx = await provider.getTransaction(txHash);
        const decoded = iface.parseTransaction({ data: tx.data });

        console.log("=== DECODED TRANSACTION ===");
        console.log("Function:", decoded.name);
        console.log("\nParameters:");
        console.log("  trackingCode:", decoded.args.trackingCode);
        console.log("  donationType:", decoded.args.donationType === 0 ? "monetary" : "goods");
        console.log("  offChainHash:", decoded.args.offChainHash);
        console.log("  amount:", decoded.args.amount.toString());
        console.log("  barangay:", decoded.args.barangay);

        // Get the receipt to see if it succeeded
        const receipt = await provider.getTransactionReceipt(txHash);
        console.log("\n=== TRANSACTION STATUS ===");
        console.log("Status:", receipt.status === 1 ? "SUCCESS" : "FAILED");
        console.log("Block:", receipt.blockNumber);
        console.log("Gas Used:", receipt.gasUsed.toString());

    } catch (error) {
        console.error("Error:", error.message);
        process.exit(1);
    }
}

decodeTx();
