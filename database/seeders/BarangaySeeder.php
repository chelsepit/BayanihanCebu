<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    /**
     * Seed barangays from multiple cities/municipalities in Cebu Province
     * Carl's Task: Day 1-2 Backend Foundation
     *
     * ID Format: {CITY_CODE}{NUMBER}
     * - CC = Cebu City
     * - MC = Mandaue City
     * - LL = Lapu-Lapu City
     * - TC = Talisay City
     * - BC = Bogo City
     */
    public function run(): void
    {
        $barangays = [
            // ========== CEBU CITY (CC001-CC005) ==========
            [
                'barangay_id' => 'CC001',
                'name' => 'Lahug',
                'city' => 'Cebu City',
                'district' => 'District 1',
                'latitude' => 10.3321,
                'longitude' => 123.8942,
                'disaster_status' => 'safe',
                'contact_person' => 'Maria Santos',
                'contact_phone' => '+63 917 123 4567',
                'contact_email' => 'lahug.bdrrmc@cebu.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'CC002',
                'name' => 'Mabolo',
                'city' => 'Cebu City',
                'district' => 'District 1',
                'latitude' => 10.3259,
                'longitude' => 123.9036,
                'disaster_status' => 'safe',
                'contact_person' => 'Juan Dela Cruz',
                'contact_phone' => '+63 917 234 5678',
                'contact_email' => 'mabolo.bdrrmc@cebu.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'CC003',
                'name' => 'Guadalupe',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3114,
                'longitude' => 123.8819,
                'disaster_status' => 'warning',
                'contact_person' => 'Pedro Reyes',
                'contact_phone' => '+63 917 345 6789',
                'contact_email' => 'guadalupe.bdrrmc@cebu.gov.ph',
                'affected_families' => 25,
                'needs_summary' => 'Flood-prone area, needs sandbags and relief goods',
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'CC004',
                'name' => 'Banilad',
                'city' => 'Cebu City',
                'district' => 'District 1',
                'latitude' => 10.3428,
                'longitude' => 123.9144,
                'disaster_status' => 'safe',
                'contact_person' => 'Rosa Garcia',
                'contact_phone' => '+63 917 456 7890',
                'contact_email' => 'banilad.bdrrmc@cebu.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'CC005',
                'name' => 'Talamban',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3572,
                'longitude' => 123.9144,
                'disaster_status' => 'critical',
                'contact_person' => 'Carlos Mendoza',
                'contact_phone' => '+63 917 567 8901',
                'contact_email' => 'talamban.bdrrmc@cebu.gov.ph',
                'affected_families' => 150,
                'needs_summary' => 'Fire incident, urgent need for temporary shelter and food',
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ========== MANDAUE CITY (MC001-MC003) ==========
            [
                'barangay_id' => 'MC001',
                'name' => 'Centro',
                'city' => 'Mandaue City',
                'district' => 'District 1',
                'latitude' => 10.3237,
                'longitude' => 123.9225,
                'disaster_status' => 'safe',
                'contact_person' => 'Linda Torres',
                'contact_phone' => '+63 918 111 2222',
                'contact_email' => 'centro.bdrrmc@mandaue.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'MC002',
                'name' => 'Basak',
                'city' => 'Mandaue City',
                'district' => 'District 2',
                'latitude' => 10.3398,
                'longitude' => 123.9267,
                'disaster_status' => 'warning',
                'contact_person' => 'Antonio Cruz',
                'contact_phone' => '+63 918 222 3333',
                'contact_email' => 'basak.bdrrmc@mandaue.gov.ph',
                'affected_families' => 35,
                'needs_summary' => 'Flooding issues, needs water pumps and sanitation kits',
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'MC003',
                'name' => 'Alang-Alang',
                'city' => 'Mandaue City',
                'district' => 'District 1',
                'latitude' => 10.3519,
                'longitude' => 123.9441,
                'disaster_status' => 'safe',
                'contact_person' => 'Elena Ramos',
                'contact_phone' => '+63 918 333 4444',
                'contact_email' => 'alangalang.bdrrmc@mandaue.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ========== LAPU-LAPU CITY (LL001-LL003) ==========
            [
                'barangay_id' => 'LL001',
                'name' => 'Poblacion',
                'city' => 'Lapu-Lapu City',
                'district' => 'District 1',
                'latitude' => 10.3103,
                'longitude' => 123.9494,
                'disaster_status' => 'safe',
                'contact_person' => 'Roberto Silva',
                'contact_phone' => '+63 919 111 2222',
                'contact_email' => 'poblacion.bdrrmc@lacity.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'LL002',
                'name' => 'Mactan',
                'city' => 'Lapu-Lapu City',
                'district' => 'District 2',
                'latitude' => 10.3122,
                'longitude' => 123.9819,
                'disaster_status' => 'safe',
                'contact_person' => 'Carmen Lopez',
                'contact_phone' => '+63 919 222 3333',
                'contact_email' => 'mactan.bdrrmc@lacity.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'LL003',
                'name' => 'Basak',
                'city' => 'Lapu-Lapu City',
                'district' => 'District 1',
                'latitude' => 10.3047,
                'longitude' => 123.9553,
                'disaster_status' => 'emergency',
                'contact_person' => 'Fernando Diaz',
                'contact_phone' => '+63 919 333 4444',
                'contact_email' => 'basak.bdrrmc@lacity.gov.ph',
                'affected_families' => 320,
                'needs_summary' => 'Typhoon damage, urgent need for food, water, medical supplies',
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ========== TALISAY CITY (TC001-TC002) ==========
            [
                'barangay_id' => 'TC001',
                'name' => 'Poblacion',
                'city' => 'Talisay City',
                'district' => 'District 1',
                'latitude' => 10.2448,
                'longitude' => 123.8492,
                'disaster_status' => 'safe',
                'contact_person' => 'Gloria Fernandez',
                'contact_phone' => '+63 920 111 2222',
                'contact_email' => 'poblacion.bdrrmc@talisay.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'TC002',
                'name' => 'Tabunok',
                'city' => 'Talisay City',
                'district' => 'District 2',
                'latitude' => 10.2636,
                'longitude' => 123.8511,
                'disaster_status' => 'warning',
                'contact_person' => 'Miguel Santos',
                'contact_phone' => '+63 920 222 3333',
                'contact_email' => 'tabunok.bdrrmc@talisay.gov.ph',
                'affected_families' => 18,
                'needs_summary' => 'Coastal flooding risk, needs early warning system',
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // ========== BOGO CITY (BC001-BC002) ==========
            [
                'barangay_id' => 'BC001',
                'name' => 'Poblacion',
                'city' => 'Bogo City',
                'district' => 'District 1',
                'latitude' => 11.0519,
                'longitude' => 124.0069,
                'disaster_status' => 'safe',
                'contact_person' => 'Ana Martinez',
                'contact_phone' => '+63 921 111 2222',
                'contact_email' => 'poblacion.bdrrmc@bogo.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'BC002',
                'name' => 'Bungtod',
                'city' => 'Bogo City',
                'district' => 'District 2',
                'latitude' => 11.0583,
                'longitude' => 124.0139,
                'disaster_status' => 'safe',
                'contact_person' => 'Sofia Ramirez',
                'contact_phone' => '+63 921 222 3333',
                'contact_email' => 'bungtod.bdrrmc@bogo.gov.ph',
                'affected_families' => 0,
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('barangays')->insert($barangays);

        $this->command->info('✅ 15 barangays seeded across Cebu Province:');
        $this->command->info('   - Cebu City: 5 barangays (CC001-CC005)');
        $this->command->info('   - Mandaue City: 3 barangays (MC001-MC003)');
        $this->command->info('   - Lapu-Lapu City: 3 barangays (LL001-LL003)');
        $this->command->info('   - Talisay City: 2 barangays (TC001-TC002)');
        $this->command->info('   - Bogo City: 2 barangays (BC001-BC002)');
    }
}
