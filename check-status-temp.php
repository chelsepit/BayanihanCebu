<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\Donation;
$donations = Donation::latest()->limit(10)->get();
foreach ($donations as $d) {
    echo "ID: {$d->id} | Verification: {$d->verification_status} | Blockchain: {$d->blockchain_status} | TX: " . ($d->blockchain_tx_hash ? 'Yes' : 'No') . "\n";
}
