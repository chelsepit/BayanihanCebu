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

echo "Recording donation {$trackingCode} to blockchain...\n";
$result = $donation->recordToBlockchain();

echo "Result:\n";
print_r($result);

if ($result['success']) {
    echo "\n✅ Successfully recorded to blockchain!\n";
    echo "TX Hash: {$result['tx_hash']}\n";
} else {
    echo "\n❌ Failed to record to blockchain\n";
    echo "Error: {$result['error']}\n";
}
