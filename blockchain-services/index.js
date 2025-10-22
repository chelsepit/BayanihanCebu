require("dotenv").config();
const { ethers } = require("ethers");
const mysql = require("mysql2/promise");
const axios = require("axios");
const FormData = require("form-data");
const fs = require("fs");

// ============================================
// CONFIGURATION
// ============================================
const CONFIG = {
    // Lisk Network
    RPC_URL: process.env.LISK_RPC_URL || "https://rpc.sepolia-api.lisk.com",
    CHAIN_ID: 4202,

    // Smart Contract
    CONTRACT_ADDRESS: process.env.CONTRACT_ADDRESS,
    PRIVATE_KEY: process.env.SYSTEM_PRIVATE_KEY,

    // Database
    DB_HOST: process.env.DB_HOST || "localhost",
    DB_USER: process.env.DB_USERNAME || "root",
    DB_PASSWORD: process.env.DB_PASSWORD || "",
    DB_NAME: process.env.DB_DATABASE || "bayanihancebu",

    // IPFS (Pinata)
    PINATA_API_KEY: process.env.PINATA_API_KEY,
    PINATA_SECRET_KEY: process.env.PINATA_SECRET_KEY,

    // Processing Interval
    INTERVAL_MS: 60000, // Run every 1 minute
};

// Smart Contract ABI (simplified)
const CONTRACT_ABI = [
    "function recordPhysicalDonation(uint256 _id, string memory _trackingCode, string memory _barangayId, string memory _donorName, string memory _category, string memory _quantity, uint256 _estimatedValue, string memory _ipfsHash) public",
    "function recordOnlineDonation(uint256 _id, string memory _trackingCode, string memory _barangayId, string memory _donorName, uint256 _amount, string memory _paymentMethod, string memory _txHash) public",
    "function recordDistribution(uint256 _id, uint256 _donationId, string memory _donationType, string memory _distributedTo, string memory _quantityDistributed, string memory _ipfsHash) public",
    "function recordResourceNeed(uint256 _id, string memory _barangayId, string memory _category, string memory _description, string memory _quantity, string memory _urgency) public",
];

// ============================================
// SETUP
// ============================================
let provider, wallet, contract, db;

async function initialize() {
    console.log("🚀 Initializing Blockchain Service...");

    // Setup Ethereum Provider
    provider = new ethers.providers.JsonRpcProvider(CONFIG.RPC_URL);
    wallet = new ethers.Wallet(CONFIG.PRIVATE_KEY, provider);
    contract = new ethers.Contract(
        CONFIG.CONTRACT_ADDRESS,
        CONTRACT_ABI,
        wallet,
    );

    // Setup MySQL Connection
    db = await mysql.createConnection({
        host: CONFIG.DB_HOST,
        user: CONFIG.DB_USER,
        password: CONFIG.DB_PASSWORD,
        database: CONFIG.DB_NAME,
    });

    console.log("✅ Connected to Lisk Network");
    console.log("✅ Connected to MySQL Database");
    console.log("📝 System Wallet:", wallet.address);
    console.log("📝 Contract Address:", CONFIG.CONTRACT_ADDRESS);
}

// ============================================
// IPFS UPLOAD (PINATA)
// ============================================
async function uploadToIPFS(base64String, filename) {
    try {
        // Convert base64 to buffer
        const buffer = Buffer.from(base64String.split(",")[1], "base64");

        const formData = new FormData();
        formData.append("file", buffer, filename);

        const response = await axios.post(
            "https://api.pinata.cloud/pinning/pinFileToIPFS",
            formData,
            {
                headers: {
                    pinata_api_key: CONFIG.PINATA_API_KEY,
                    pinata_secret_api_key: CONFIG.PINATA_SECRET_KEY,
                    ...formData.getHeaders(),
                },
            },
        );

        return response.data.IpfsHash;
    } catch (error) {
        console.error("❌ IPFS Upload Error:", error.message);
        return null;
    }
}

async function uploadMultipleToIPFS(base64Array) {
    const hashes = [];
    for (let i = 0; i < base64Array.length; i++) {
        const hash = await uploadToIPFS(base64Array[i], `photo_${i}.jpg`);
        if (hash) hashes.push(hash);
    }
    return hashes.join(",");
}

// ============================================
// PROCESS PHYSICAL DONATIONS
// ============================================
async function processPhysicalDonations() {
    try {
        const [rows] = await db.query(`
            SELECT * FROM physical_donations
            WHERE blockchain_status = 'pending'
            AND blockchain_tx_hash IS NULL
            LIMIT 5
        `);

        console.log(`📦 Found ${rows.length} physical donations to process`);

        for (const donation of rows) {
            try {
                // Upload photos to IPFS
                let ipfsHash = "";
                if (donation.photo_urls) {
                    const photos = JSON.parse(donation.photo_urls);
                    ipfsHash = await uploadMultipleToIPFS(photos);
                }

                // Call smart contract
                const tx = await contract.recordPhysicalDonation(
                    donation.id,
                    donation.tracking_code,
                    donation.barangay_id,
                    donation.donor_name,
                    donation.category,
                    donation.quantity,
                    ethers.utils.parseUnits(
                        (donation.estimated_value || 0).toString(),
                        0,
                    ),
                    ipfsHash,
                );

                console.log(
                    `⏳ TX sent for donation #${donation.id}: ${tx.hash}`,
                );

                // Wait for confirmation
                const receipt = await tx.wait();

                // Update database
                await db.query(
                    `
                    UPDATE physical_donations
                    SET blockchain_tx_hash = ?,
                        ipfs_hash = ?,
                        blockchain_status = 'confirmed',
                        blockchain_recorded_at = NOW()
                    WHERE id = ?
                `,
                    [tx.hash, ipfsHash, donation.id],
                );

                // Log transaction
                await db.query(
                    `
                    INSERT INTO blockchain_transaction_logs
                    (tx_hash, transaction_type, reference_id, from_address, contract_address,
                     function_called, gas_used, block_number, status, ipfs_hash, confirmed_at, created_at)
                    VALUES (?, 'physical_donation', ?, ?, ?, 'recordPhysicalDonation', ?, ?, 'confirmed', ?, NOW(), NOW())
                `,
                    [
                        tx.hash,
                        donation.id,
                        wallet.address,
                        CONFIG.CONTRACT_ADDRESS,
                        receipt.gasUsed.toString(),
                        receipt.blockNumber.toString(),
                        ipfsHash,
                    ],
                );

                console.log(
                    `✅ Physical donation #${donation.id} recorded on blockchain!`,
                );
            } catch (error) {
                console.error(
                    `❌ Error processing donation #${donation.id}:`,
                    error.message,
                );

                await db.query(
                    `
                    UPDATE physical_donations
                    SET blockchain_status = 'failed',
                        blockchain_error = ?
                    WHERE id = ?
                `,
                    [error.message, donation.id],
                );
            }
        }
    } catch (error) {
        console.error("❌ Error in processPhysicalDonations:", error.message);
    }
}

// ============================================
// PROCESS ONLINE DONATIONS
// ============================================
async function processOnlineDonations() {
    try {
        const [rows] = await db.query(`
            SELECT * FROM online_donations
            WHERE verification_status = 'verified'
            AND blockchain_status = 'pending'
            AND blockchain_tx_hash IS NULL
            LIMIT 5
        `);

        console.log(`💰 Found ${rows.length} online donations to process`);

        for (const donation of rows) {
            try {
                // Call smart contract
                const tx = await contract.recordOnlineDonation(
                    donation.id,
                    donation.tracking_code,
                    donation.target_barangay_id,
                    donation.is_anonymous ? "Anonymous" : donation.donor_name,
                    ethers.utils.parseUnits(donation.amount.toString(), 0), // Convert to smallest unit
                    donation.payment_method,
                    donation.tx_hash || "",
                );

                console.log(
                    `⏳ TX sent for online donation #${donation.id}: ${tx.hash}`,
                );

                const receipt = await tx.wait();

                // Update database
                await db.query(
                    `
                    UPDATE online_donations
                    SET blockchain_tx_hash = ?,
                        blockchain_status = 'confirmed',
                        blockchain_recorded_at = NOW()
                    WHERE id = ?
                `,
                    [tx.hash, donation.id],
                );

                // Log transaction
                await db.query(
                    `
                    INSERT INTO blockchain_transaction_logs
                    (tx_hash, transaction_type, reference_id, from_address, contract_address,
                     function_called, gas_used, block_number, status, confirmed_at, created_at)
                    VALUES (?, 'online_donation', ?, ?, ?, 'recordOnlineDonation', ?, ?, 'confirmed', NOW(), NOW())
                `,
                    [
                        tx.hash,
                        donation.id,
                        wallet.address,
                        CONFIG.CONTRACT_ADDRESS,
                        receipt.gasUsed.toString(),
                        receipt.blockNumber.toString(),
                    ],
                );

                console.log(
                    `✅ Online donation #${donation.id} recorded on blockchain!`,
                );
            } catch (error) {
                console.error(
                    `❌ Error processing online donation #${donation.id}:`,
                    error.message,
                );

                await db.query(
                    `
                    UPDATE online_donations
                    SET blockchain_status = 'failed'
                    WHERE id = ?
                `,
                    [donation.id],
                );
            }
        }
    } catch (error) {
        console.error("❌ Error in processOnlineDonations:", error.message);
    }
}

// ============================================
// PROCESS DISTRIBUTIONS
// ============================================
async function processDistributions() {
    try {
        const [rows] = await db.query(`
            SELECT * FROM distribution_logs
            WHERE blockchain_status = 'pending'
            AND blockchain_tx_hash IS NULL
            LIMIT 5
        `);

        console.log(`📤 Found ${rows.length} distributions to process`);

        for (const dist of rows) {
            try {
                // Upload photos to IPFS
                let ipfsHash = "";
                if (dist.photo_urls) {
                    const photos = JSON.parse(dist.photo_urls);
                    ipfsHash = await uploadMultipleToIPFS(photos);
                }

                // Call smart contract
                const tx = await contract.recordDistribution(
                    dist.id,
                    dist.physical_donation_id,
                    "physical", // or 'online' based on donation type
                    dist.distributed_to,
                    dist.quantity_distributed,
                    ipfsHash,
                );

                console.log(
                    `⏳ TX sent for distribution #${dist.id}: ${tx.hash}`,
                );

                const receipt = await tx.wait();

                // Update database
                await db.query(
                    `
                    UPDATE distribution_logs
                    SET blockchain_tx_hash = ?,
                        ipfs_hash = ?,
                        blockchain_status = 'confirmed',
                        blockchain_recorded_at = NOW()
                    WHERE id = ?
                `,
                    [tx.hash, ipfsHash, dist.id],
                );

                // Log transaction
                await db.query(
                    `
                    INSERT INTO blockchain_transaction_logs
                    (tx_hash, transaction_type, reference_id, from_address, contract_address,
                     function_called, gas_used, block_number, status, ipfs_hash, confirmed_at, created_at)
                    VALUES (?, 'distribution', ?, ?, ?, 'recordDistribution', ?, ?, 'confirmed', ?, NOW(), NOW())
                `,
                    [
                        tx.hash,
                        dist.id,
                        wallet.address,
                        CONFIG.CONTRACT_ADDRESS,
                        receipt.gasUsed.toString(),
                        receipt.blockNumber.toString(),
                        ipfsHash,
                    ],
                );

                console.log(
                    `✅ Distribution #${dist.id} recorded on blockchain!`,
                );
            } catch (error) {
                console.error(
                    `❌ Error processing distribution #${dist.id}:`,
                    error.message,
                );

                await db.query(
                    `
                    UPDATE distribution_logs
                    SET blockchain_status = 'failed'
                    WHERE id = ?
                `,
                    [dist.id],
                );
            }
        }
    } catch (error) {
        console.error("❌ Error in processDistributions:", error.message);
    }
}

// ============================================
// PROCESS RESOURCE NEEDS
// ============================================
async function processResourceNeeds() {
    try {
        const [rows] = await db.query(`
            SELECT * FROM resource_needs
            WHERE blockchain_status = 'pending'
            AND blockchain_tx_hash IS NULL
            LIMIT 5
        `);

        console.log(`📋 Found ${rows.length} resource needs to process`);

        for (const need of rows) {
            try {
                // Call smart contract
                const tx = await contract.recordResourceNeed(
                    need.id,
                    need.barangay_id,
                    need.category,
                    need.description,
                    need.quantity,
                    need.urgency,
                );

                console.log(
                    `⏳ TX sent for resource need #${need.id}: ${tx.hash}`,
                );

                const receipt = await tx.wait();

                // Update database
                await db.query(
                    `
                    UPDATE resource_needs
                    SET blockchain_tx_hash = ?,
                        blockchain_status = 'confirmed',
                        blockchain_recorded_at = NOW()
                    WHERE id = ?
                `,
                    [tx.hash, need.id],
                );

                // Log transaction
                await db.query(
                    `
                    INSERT INTO blockchain_transaction_logs
                    (tx_hash, transaction_type, reference_id, from_address, contract_address,
                     function_called, gas_used, block_number, status, confirmed_at, created_at)
                    VALUES (?, 'resource_need', ?, ?, ?, 'recordResourceNeed', ?, ?, 'confirmed', NOW(), NOW())
                `,
                    [
                        tx.hash,
                        need.id,
                        wallet.address,
                        CONFIG.CONTRACT_ADDRESS,
                        receipt.gasUsed.toString(),
                        receipt.blockNumber.toString(),
                    ],
                );

                console.log(
                    `✅ Resource need #${need.id} recorded on blockchain!`,
                );
            } catch (error) {
                console.error(
                    `❌ Error processing resource need #${need.id}:`,
                    error.message,
                );

                await db.query(
                    `
                    UPDATE resource_needs
                    SET blockchain_status = 'failed'
                    WHERE id = ?
                `,
                    [need.id],
                );
            }
        }
    } catch (error) {
        console.error("❌ Error in processResourceNeeds:", error.message);
    }
}

// ============================================
// MAIN PROCESSING LOOP
// ============================================
async function processAll() {
    console.log("\n🔄 Starting processing cycle...\n");

    await processPhysicalDonations();
    await processOnlineDonations();
    await processDistributions();
    await processResourceNeeds();

    console.log("\n✅ Processing cycle complete!\n");
}

// ============================================
// START SERVICE
// ============================================
async function start() {
    try {
        await initialize();

        // Run immediately
        await processAll();

        // Run every interval
        setInterval(processAll, CONFIG.INTERVAL_MS);

        console.log(
            `\n⏰ Service running every ${CONFIG.INTERVAL_MS / 1000} seconds\n`,
        );
    } catch (error) {
        console.error("❌ Fatal error:", error);
        process.exit(1);
    }
}

// Handle graceful shutdown
process.on("SIGINT", async () => {
    console.log("\n👋 Shutting down gracefully...");
    if (db) await db.end();
    process.exit(0);
});

// Start the service
start();
