<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Donation;
use App\Models\PhysicalDonation;

echo "=== Checking Donor Emails ===\n\n";

$donations = Donation::latest()->limit(5)->get();
echo "Online Donations:\n";
foreach ($donations as $d) {
    echo "  ID: {$d->id} | Name: {$d->donor_name} | Email: " . ($d->donor_email ?? 'NULL') . "\n";
}

echo "\nTotal Unique Donor Emails: " . Donation::distinct('donor_email')->whereNotNull('donor_email')->count() . "\n";
echo "Total Donations with blockchain: " . Donation::where('blockchain_status', 'confirmed')->count() . "\n";
