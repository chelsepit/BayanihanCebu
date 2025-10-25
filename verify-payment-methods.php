<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\Donation;

echo "=== Verifying Payment Methods ===\n\n";

$donations = Donation::latest()->limit(4)->get();

foreach ($donations as $d) {
    echo "ID: {$d->id} | Donor: {$d->donor_name} | Amount: ₱{$d->amount} | Payment Method: " . ($d->payment_method ?? 'NULL') . "\n";
}
