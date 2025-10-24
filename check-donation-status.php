<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$trackingCodes = ['DON-B47A9836', 'DON-13F07F6E', 'DON-E6899092', 'DON-CE880823', 'DON-6A6268A9', 'DON-EE29A19B'];

$donations = App\Models\Donation::whereIn('tracking_code', $trackingCodes)
    ->get(['tracking_code', 'blockchain_status', 'blockchain_tx_hash']);

echo "Checking donation statuses:\n\n";

foreach ($donations as $d) {
    echo "Code: " . $d->tracking_code . "\n";
    echo "  Status: " . ($d->blockchain_status ?? 'NULL') . "\n";
    echo "  TX Hash: " . ($d->blockchain_tx_hash ? substr($d->blockchain_tx_hash, 0, 20) . '...' : 'NONE') . "\n\n";
}
