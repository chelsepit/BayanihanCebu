<?php

/**
 * Update Payment Methods from PayMongo
 *
 * This script retrieves the payment method used from PayMongo checkout sessions
 * and updates the donations table with the correct payment method.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Donation;
use App\Services\PaymongoService;
use Illuminate\Support\Facades\Log;

echo "=== Updating Payment Methods from PayMongo ===\n\n";

// Find all paid donations that don't have a payment method set
$donations = Donation::whereNull('payment_method')
    ->whereNotNull('payment_session_id')
    ->where('payment_status', 'paid')
    ->get();

echo "Found {$donations->count()} donations without payment method\n\n";

if ($donations->isEmpty()) {
    echo "✅ All donations already have payment methods!\n";
    exit(0);
}

$paymongo = app(PaymongoService::class);
$updatedCount = 0;
$failedCount = 0;

foreach ($donations as $donation) {
    echo "Processing Donation #{$donation->id} ({$donation->tracking_code})...\n";

    try {
        // Fetch the checkout session from PayMongo
        $session = $paymongo->getCheckoutSession($donation->payment_session_id);

        // Get the payment method used
        $paymentMethodUsed = $session['attributes']['payment_method_used'] ?? null;

        if ($paymentMethodUsed) {
            // Update the donation
            $donation->update([
                'payment_method' => $paymentMethodUsed
            ]);

            echo "  ✅ Updated with payment method: {$paymentMethodUsed}\n";
            $updatedCount++;

            Log::info('Updated payment method from PayMongo', [
                'donation_id' => $donation->id,
                'tracking_code' => $donation->tracking_code,
                'payment_method' => $paymentMethodUsed,
            ]);
        } else {
            echo "  ⚠️  No payment method found in session\n";
            $failedCount++;
        }

    } catch (\Exception $e) {
        echo "  ❌ Error: {$e->getMessage()}\n";
        $failedCount++;

        Log::error('Error updating payment method', [
            'donation_id' => $donation->id,
            'error' => $e->getMessage(),
        ]);
    }

    echo "\n";
}

echo "=== Summary ===\n";
echo "Successfully updated: {$updatedCount}\n";
echo "Failed: {$failedCount}\n";
echo "✅ Done!\n";
