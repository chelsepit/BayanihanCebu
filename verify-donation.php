<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PhysicalDonation;

$trackingCode = $argv[1] ?? 'CC001-2025-00009';

$donation = PhysicalDonation::where('tracking_code', $trackingCode)->first();

if (!$donation) {
    echo "Donation not found: {$trackingCode}\n";
    exit(1);
}

echo "Verifying donation {$trackingCode} from blockchain...\n";
echo "Offchain hash: {$donation->offchain_hash}\n";
echo "Blockchain TX: {$donation->blockchain_tx_hash}\n\n";

$result = $donation->verifyBlockchainIntegrity();

echo "Result:\n";
print_r($result);

if ($result['success']) {
    echo "\n✅ Verification Status: {$result['status']}\n";
    if (isset($result['onchain_hash'])) {
        echo "Onchain Hash: {$result['onchain_hash']}\n";
        echo "Offchain Hash: {$donation->offchain_hash}\n";
        echo "Match: " . ($result['onchain_hash'] === $donation->offchain_hash ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "\n❌ Verification failed\n";
    echo "Error: {$result['error']}\n";
}
