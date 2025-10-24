<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find all donations that are paid but stuck in pending blockchain status
$donations = App\Models\Donation::where('payment_status', 'paid')
    ->where(function($q) {
        $q->where('blockchain_status', 'pending')
          ->orWhereNull('blockchain_status');
    })
    ->get();

echo "Found " . $donations->count() . " stuck donations\n\n";

foreach ($donations as $donation) {
    echo "Re-queuing: {$donation->id} - {$donation->tracking_code} - PHP {$donation->amount}\n";

    // Dispatch the job
    App\Jobs\RecordDonationOnChain::dispatch($donation);
}

echo "\n✅ All stuck donations have been re-queued!\n";
echo "The queue worker will process them shortly.\n";
