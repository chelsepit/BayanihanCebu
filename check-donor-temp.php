<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\Donation;
$donations = Donation::latest()->limit(4)->get();
foreach ($donations as $d) {
    echo "ID: {$d->id}\n";
    echo "  Donor Name: " . ($d->donor_name ?? 'NULL') . "\n";
    echo "  Donor Email: " . ($d->donor_email ?? 'NULL') . "\n";
    echo "  Is Anonymous: " . ($d->is_anonymous ? 'Yes' : 'No') . "\n";
    echo "  User ID: " . ($d->user_id ?? 'NULL') . "\n";
    echo "\n";
}
