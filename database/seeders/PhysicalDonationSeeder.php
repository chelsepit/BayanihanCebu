<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhysicalDonation;

class PhysicalDonationSeeder extends Seeder
{
    public function run(): void
    {
        $donations = [
            // Food donations
            [
                'barangay_id' => 'CC001', // Lahug
                'tracking_code' => 'CC001-2025-00001',
                'donor_name' => 'Juan Dela Cruz',
                'donor_contact' => '09171234567',
                'donor_email' => 'juan@example.com',
                'donor_address' => '123 Main St, Lahug, Cebu City',
                'category' => 'food',
                'items_description' => '10 sacks of rice (25kg each), 50 canned goods, 20 packs of noodles',
                'quantity' => '10',
                'estimated_value' => 15000.00,
                'intended_recipients' => 'Flood-affected families',
                'notes' => 'Delivered by donor personally',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U003', // BDRRMC for CC001
                'recorded_at' => now()->subDays(2),
            ],
            [
                'barangay_id' => 'CC002', // Apas
                'tracking_code' => 'CC002-2025-00001',
                'donor_name' => 'SM Foundation',
                'donor_contact' => '09171111111',
                'donor_email' => 'foundation@sm.com',
                'donor_address' => 'SM City Cebu',
                'category' => 'food',
                'items_description' => '100 family food packs with rice, canned goods, noodles',
                'quantity' => '100',
                'estimated_value' => 50000.00,
                'intended_recipients' => 'Emergency distribution',
                'notes' => 'Ready for distribution to any barangay in need',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U004', // BDRRMC for CC002
                'recorded_at' => now()->subDays(1),
            ],

            // Water donations
            [
                'barangay_id' => 'CC001', // Lahug
                'tracking_code' => 'CC001-2025-00002',
                'donor_name' => 'Maria Santos',
                'donor_contact' => '09187654321',
                'donor_email' => 'maria@example.com',
                'donor_address' => '456 Oak Ave, Lahug, Cebu City',
                'category' => 'water',
                'items_description' => '50 gallons of purified water in 5-gallon containers',
                'quantity' => '50',
                'estimated_value' => 2500.00,
                'intended_recipients' => 'All affected residents',
                'notes' => 'Water is sealed and safe for drinking',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U003',
                'recorded_at' => now()->subDays(1),
            ],
            [
                'barangay_id' => 'CC004', // Banilad
                'tracking_code' => 'CC004-2025-00001',
                'donor_name' => 'Cebu Water District',
                'donor_contact' => '09192222222',
                'donor_email' => 'relief@cebuwater.gov',
                'donor_address' => 'Cebu Water District Office',
                'category' => 'water',
                'items_description' => 'Bottled water and large water containers',
                'quantity' => '300',
                'estimated_value' => 15000.00,
                'intended_recipients' => 'Emergency distribution',
                'notes' => 'Available for immediate distribution',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U005', // BDRRMC for CC004
                'recorded_at' => now()->subHours(6),
            ],

            // Medical donations
            [
                'barangay_id' => 'CC001', // Lahug
                'tracking_code' => 'CC001-2025-00003',
                'donor_name' => 'Cebu Medical Clinic',
                'donor_contact' => '09191112222',
                'donor_email' => 'info@cebumedical.com',
                'donor_address' => '789 Medical Plaza, Cebu City',
                'category' => 'medical',
                'items_description' => '15 complete first aid kits with bandages, antiseptics, pain relievers',
                'quantity' => '15',
                'estimated_value' => 7500.00,
                'intended_recipients' => 'Health center and affected families',
                'notes' => 'Includes instructions for use',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U003',
                'recorded_at' => now()->subDays(3),
            ],
            [
                'barangay_id' => 'CC005', // Mabolo
                'tracking_code' => 'CC005-2025-00001',
                'donor_name' => 'Philippine Red Cross',
                'donor_contact' => '09193333333',
                'donor_email' => 'cebu@redcross.ph',
                'donor_address' => 'Red Cross Cebu Chapter',
                'category' => 'medical',
                'items_description' => 'Medical kits with first aid supplies and basic medicines',
                'quantity' => '25',
                'estimated_value' => 12500.00,
                'intended_recipients' => 'Disaster-affected areas',
                'notes' => 'Standard disaster relief medical kits',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U006', // BDRRMC for CC005
                'recorded_at' => now()->subHours(12),
            ],

            // Clothing donations
            [
                'barangay_id' => 'CC002', // Apas
                'tracking_code' => 'CC002-2025-00002',
                'donor_name' => 'Anonymous Donor',
                'donor_contact' => '09123456789',
                'donor_email' => null,
                'donor_address' => 'Withheld',
                'category' => 'clothing',
                'items_description' => 'Assorted clean clothing for men, women, and children',
                'quantity' => '150',
                'estimated_value' => 12000.00,
                'intended_recipients' => 'Families who lost belongings in disasters',
                'notes' => 'Donor wishes to remain anonymous',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U004',
                'recorded_at' => now(),
            ],

            // Shelter materials
            [
                'barangay_id' => 'CC006', // Tisa
                'tracking_code' => 'CC006-2025-00001',
                'donor_name' => 'Construction Builders Inc.',
                'donor_contact' => '09194444444',
                'donor_email' => 'relief@builders.com',
                'donor_address' => 'Tisa, Cebu City',
                'category' => 'shelter',
                'items_description' => 'Tarpaulins, tents, and emergency shelter materials',
                'quantity' => '40',
                'estimated_value' => 25000.00,
                'intended_recipients' => 'Displaced families',
                'notes' => 'Heavy-duty tarpaulins and family-sized tents',
                'distribution_status' => 'pending_distribution',
                'recorded_by' => 'U007', // BDRRMC for CC006
                'recorded_at' => now()->subHours(3),
            ],

            // Already distributed (for testing status)
            [
                'barangay_id' => 'CC003', // Basak San Nicolas
                'tracking_code' => 'CC003-2025-00001',
                'donor_name' => 'Jollibee Foundation',
                'donor_contact' => '09195555555',
                'donor_email' => 'foundation@jollibee.com',
                'donor_address' => 'Jollibee Foods Corporation',
                'category' => 'food',
                'items_description' => 'Ready-to-eat meals and food packages',
                'quantity' => '200',
                'estimated_value' => 40000.00,
                'intended_recipients' => 'Flood victims in Basak San Nicolas',
                'notes' => 'Already distributed to affected families',
                'distribution_status' => 'fully_distributed',
                'recorded_by' => 'U008', // BDRRMC for CC003
                'recorded_at' => now()->subDays(4),
            ],
        ];

        foreach ($donations as $donation) {
            PhysicalDonation::create($donation);
        }
    }
}