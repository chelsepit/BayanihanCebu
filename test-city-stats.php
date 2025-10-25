<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Barangay;
use App\Models\Donation;

echo "=== Testing City Overview Statistics ===\n\n";

// Get all barangays and their totals
$barangays = Barangay::all();

$totalDonations = 0;
$totalFamilies = 0;
$affectedCount = 0;

echo "Barangay Data:\n";
foreach ($barangays as $b) {
    $raised = $b->total_raised ?? 0;
    $families = $b->affected_families ?? 0;
    
    echo "  {$b->name}: ₱" . number_format($raised, 2) . " | {$families} families | Status: {$b->donation_status}\n";
    
    $totalDonations += $raised;
    $totalFamilies += $families;
    
    if (in_array($b->donation_status, ['pending', 'in_progress'])) {
        $affectedCount++;
    }
}

echo "\n=== City Overview Totals ===\n";
echo "Total Donations: ₱" . number_format($totalDonations, 2) . "\n";
echo "Total Affected Families: " . number_format($totalFamilies) . "\n";
echo "Barangays Affected: {$affectedCount}\n";

// Check donors
$totalDonors = Donation::distinct('donor_email')->whereNotNull('donor_email')->count();
echo "Total Donors: {$totalDonors}\n";
