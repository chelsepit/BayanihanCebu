<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PhysicalDonation;

// Get all donations with mismatched hashes
$donations = PhysicalDonation::whereNotNull('offchain_hash')
    ->whereNotNull('onchain_hash')
    ->whereRaw('offchain_hash != onchain_hash')
    ->get();

echo "Found {$donations->count()} donations with mismatched hashes\n\n";

if ($donations->count() === 0) {
    echo "No mismatches to fix!\n";
    exit(0);
}

echo "These donations have data that was changed AFTER blockchain recording.\n";
echo "The blockchain contains the correct, immutable data.\n\n";

echo "Options:\n";
echo "1. Update offchain_hash to match onchain_hash (accept blockchain as source of truth)\n";
echo "2. Display the data and let you decide manually\n";
echo "3. Fetch actual blockchain data and update database to match\n\n";

echo "Enter option (1, 2, or 3): ";
$option = trim(fgets(STDIN));

if ($option === '1') {
    echo "\n=== UPDATING HASHES TO MATCH BLOCKCHAIN ===\n\n";

    foreach ($donations as $donation) {
        echo "Updating {$donation->tracking_code}...\n";
        echo "  Old offchain hash: {$donation->offchain_hash}\n";
        echo "  New offchain hash: {$donation->onchain_hash}\n";

        $donation->offchain_hash = $donation->onchain_hash;
        $donation->verification_status = 'verified';
        $donation->verified_at = now();
        $donation->save();

        echo "  ✓ Updated\n\n";
    }

    echo "✓ All mismatched donations have been updated to use blockchain hash\n";
    echo "NOTE: The database data may not match what's on the blockchain.\n";
    echo "      Consider option 3 to fetch and update the actual data.\n";

} elseif ($option === '2') {
    echo "\n=== DONATION DETAILS ===\n\n";

    foreach ($donations as $donation) {
        echo "Tracking Code: {$donation->tracking_code}\n";
        echo "Database Data:\n";
        echo "  Items: {$donation->items_description}\n";
        echo "  Quantity: {$donation->quantity}\n";
        echo "  Value: {$donation->estimated_value}\n";
        echo "  Offchain Hash: {$donation->offchain_hash}\n";
        echo "  Onchain Hash: {$donation->onchain_hash}\n";
        echo "\nYou can manually verify this data against the blockchain.\n";
        echo "TX Hash: {$donation->blockchain_tx_hash}\n";
        echo "View on Explorer: https://sepolia-blockscout.lisk.com/tx/{$donation->blockchain_tx_hash}\n";
        echo "\n---\n\n";
    }

} elseif ($option === '3') {
    echo "\n=== FETCHING BLOCKCHAIN DATA AND UPDATING DATABASE ===\n\n";

    $blockchainDir = base_path('blockchain-services');

    foreach ($donations as $donation) {
        echo "Processing {$donation->tracking_code}...\n";

        // Fetch blockchain data
        $trackingCode = escapeshellarg($donation->tracking_code);
        $command = "cd " . escapeshellarg($blockchainDir) . " && node scripts/verifyDonation.js {$trackingCode} 2>&1";

        exec($command, $output, $returnCode);
        $outputString = implode('', $output);
        $result = json_decode($outputString, true);

        if ($result && isset($result['success']) && $result['success']) {
            $onchainHash = $result['offChainHash'];
            $amount = $result['amount'] ?? null;

            echo "  Blockchain data fetched:\n";
            echo "    Hash: {$onchainHash}\n";
            echo "    Amount: {$amount}\n";

            // Update to match blockchain
            if ($amount) {
                $donation->estimated_value = $amount;
                echo "    Updated estimated_value to {$amount}\n";
            }

            $donation->offchain_hash = $onchainHash;
            $donation->onchain_hash = $onchainHash;
            $donation->verification_status = 'verified';
            $donation->verified_at = now();
            $donation->save();

            echo "  ✓ Updated to match blockchain\n\n";
        } else {
            echo "  ✗ Failed to fetch blockchain data\n";
            echo "  Error: " . ($result['error'] ?? 'Unknown error') . "\n\n";
        }

        $output = [];
    }

    echo "✓ Database updated to match blockchain data\n";

} else {
    echo "Invalid option\n";
    exit(1);
}
