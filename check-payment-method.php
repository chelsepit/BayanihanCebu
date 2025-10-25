<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\Donation;
$donations = Donation::latest()->limit(4)->get();
echo "=== Checking Payment Method Field ===\n\n";
foreach ($donations as $d) {
    echo "ID: {$d->id}\n";
    echo "  Tracking: {$d->tracking_code}\n";
    echo "  Amount: ₱{$d->amount}\n";
    echo "  Payment Method: " . ($d->payment_method ?? 'NULL') . "\n";
    echo "  Payment Status: {$d->payment_status}\n";
    echo "  Payment Session ID: " . ($d->payment_session_id ? 'Yes' : 'No') . "\n";
    echo "\n";
}
