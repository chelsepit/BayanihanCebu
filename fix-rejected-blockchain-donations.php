<?php

/**
 * Fix Rejected Donations That Are Blockchain Verified
 *
 * This script fixes the inconsistency where donations have:
 * - verification_status = 'rejected'
 * - blockchain_status = 'confirmed' (blockchain verified)
 *
 * Since blockchain is immutable proof of payment, these should be marked as verified.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Donation;
use Illuminate\Support\Facades\Log;

echo "=== Fixing Rejected Donations with Blockchain Verification ===\n\n";

// Find all donations that are:
// 1. Marked as rejected (verification_status = 'rejected')
// 2. But blockchain verified (blockchain_status = 'confirmed')
$inconsistentDonations = Donation::where('verification_status', 'rejected')
    ->where('blockchain_status', 'confirmed')
    ->whereNotNull('blockchain_tx_hash')
    ->get();

echo "Found {$inconsistentDonations->count()} donations with inconsistent status\n\n";

if ($inconsistentDonations->isEmpty()) {
    echo "✅ No inconsistent donations found. All donations are correctly marked!\n";
    exit(0);
}

echo "Donations to fix:\n";
foreach ($inconsistentDonations as $donation) {
    echo "  - ID: {$donation->id}\n";
    echo "    Tracking Code: {$donation->tracking_code}\n";
    echo "    Amount: ₱{$donation->amount}\n";
    echo "    Current Status: {$donation->verification_status}\n";
    echo "    Blockchain: {$donation->blockchain_status}\n";
    echo "    TX Hash: {$donation->blockchain_tx_hash}\n";
    echo "\n";
}

echo "Do you want to fix these donations? (yes/no): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$response = trim(strtolower($line));
fclose($handle);

if ($response !== 'yes' && $response !== 'y') {
    echo "❌ Operation cancelled. No changes made.\n";
    exit(0);
}

echo "\n🔧 Fixing donations...\n\n";

$fixedCount = 0;
foreach ($inconsistentDonations as $donation) {
    try {
        $donation->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'rejection_reason' => null, // Clear rejection reason
        ]);

        echo "✅ Fixed donation #{$donation->id} ({$donation->tracking_code})\n";
        $fixedCount++;

        Log::info('Fixed rejected donation with blockchain verification', [
            'donation_id' => $donation->id,
            'tracking_code' => $donation->tracking_code,
            'blockchain_tx_hash' => $donation->blockchain_tx_hash,
        ]);

    } catch (\Exception $e) {
        echo "❌ Error fixing donation #{$donation->id}: {$e->getMessage()}\n";
        Log::error('Error fixing rejected donation', [
            'donation_id' => $donation->id,
            'error' => $e->getMessage(),
        ]);
    }
}

echo "\n=== Summary ===\n";
echo "Total donations fixed: {$fixedCount}/{$inconsistentDonations->count()}\n";
echo "✅ Done!\n";
