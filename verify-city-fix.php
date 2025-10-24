<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Barangay;
use App\Models\Donation;
use App\Models\PhysicalDonation;

echo "=== City Overview - Fixed Statistics ===\n\n";

// Total Donations (blockchain-verified)
$totalOnline = Donation::where('blockchain_status', 'confirmed')
    ->whereNotNull('blockchain_tx_hash')
    ->sum('amount');

$totalPhysical = PhysicalDonation::where('blockchain_status', 'confirmed')
    ->whereNotNull('blockchain_tx_hash')
    ->sum('estimated_value');

$totalDonations = $totalOnline + $totalPhysical;

// Affected Families
$totalFamilies = Barangay::sum('affected_families');

// Barangays Affected
$affectedBarangays = Barangay::whereIn('donation_status', ['pending', 'in_progress'])->count();

// Donors (count of blockchain-verified donations)
$totalDonors = Donation::where('blockchain_status', 'confirmed')->whereNotNull('blockchain_tx_hash')->count() +
               PhysicalDonation::where('blockchain_status', 'confirmed')->whereNotNull('blockchain_tx_hash')->count();

echo "Total Donations: ₱" . number_format($totalDonations, 2) . "\n";
echo "Affected Families: " . number_format($totalFamilies) . "\n";
echo "Barangays Affected: {$affectedBarangays}\n";
echo "Donors: {$totalDonors}\n";
