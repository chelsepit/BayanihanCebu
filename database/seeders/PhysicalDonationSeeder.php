<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhysicalDonation;

class PhysicalDonationSeeder extends Seeder
{
    public function run(): void
    {
        $donations = [
            [
                'barangay_id' => 'CC001', // Changed from B001
                'tracking_code' => 'CC001-2025-00001',
                'donor_name' => 'Juan Dela Cruz',
                'donor_contact' => '09171234567',
                'donor_email' => 'juan@example.com',
                'donor_address' => '123 Main St, Lahug, Cebu City',
                'category' => 'food',
                'items_description' => '10 sacks of rice (25kg each), 50 canned goods, 20 packs of noodles',
                'quantity' => '10 sacks + assorted goods',
                'estimated_value' => 15000.00,
                'intended_recipients' => 'Flood-affected families',
                'notes' => 'Delivered by donor personally',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U003', // BDRRMC for CC001
                'recorded_at' => now()->subDays(2),
            ],
            [
                'barangay_id' => 'CC001',
                'tracking_code' => 'CC001-2025-00002',
                'donor_name' => 'Maria Santos',
                'donor_contact' => '09187654321',
                'donor_email' => 'maria@example.com',
                'donor_address' => '456 Oak Ave, Lahug, Cebu City',
                'category' => 'water',
                'items_description' => '50 gallons of purified water in 5-gallon containers',
                'quantity' => '10 containers (5 gallons each)',
                'estimated_value' => 2500.00,
                'intended_recipients' => 'All affected residents',
                'notes' => 'Water is sealed and safe for drinking',
                'distribution_status' => 'partially_distributed',
                'recorded_by' => 'U003',
                'recorded_at' => now()->subDays(1),
            ],
            [
                'barangay_id' => 'CC001',
                'tracking_code' => 'CC001-2025-00003',
                'donor_name' => 'Cebu Medical Clinic',
                'donor_contact' => '09191112222',
                'donor_email' => 'info@cebumedical.com',
                'donor_address' => '789 Medical Plaza, Cebu City',
                'category' => 'medical',
                'items_description' => '15 complete first aid kits with bandages, antiseptics, pain relievers',
                'quantity' => '15 kits',
                'estimated_value' => 7500.00,
                'intended_recipients' => 'Health center and affected families',
                'notes' => 'Includes instructions for use',
                'distribution_status' => 'fully_distributed',
                'recorded_by' => 'U003',
                'recorded_at' => now()->subDays(3),
            ],
            [
                'barangay_id' => 'CC002',
                'tracking_code' => 'CC002-2025-00001',
                'donor_name' => 'Anonymous Donor',
                'donor_contact' => '09123456789',
                'donor_email' => null,
                'donor_address' => 'Withheld',
                'category' => 'clothing',
                'items_description' => 'Assorted clean clothing for men, women, and children',
                'quantity' => '150 pieces',
                'estimated_value' => 12000.00,
                'intended_recipients' => 'Families who lost belongings in flood',
                'notes' => 'Donor wishes to remain anonymous',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U004', // BDRRMC for CC002
                'recorded_at' => now(),
            ],
        ];

        foreach ($donations as $donation) {
            PhysicalDonation::create($donation);
        }
    }
}