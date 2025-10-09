<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample barangays in Cebu City
        $barangays = [
            [
                'barangay_id' => 'B001',
                'name' => 'Lahug',
                'city' => 'Cebu City',
                'latitude' => 10.3157,
                'longitude' => 123.8854,
                'disaster_status' => 'safe',
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'B002',
                'name' => 'Mabolo',
                'city' => 'Cebu City',
                'latitude' => 10.3147,
                'longitude' => 123.9139,
                'disaster_status' => 'safe',
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'B003',
                'name' => 'Guadalupe',
                'city' => 'Cebu City',
                'latitude' => 10.3114,
                'longitude' => 123.8819,
                'disaster_status' => 'safe',
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'B004',
                'name' => 'Banilad',
                'city' => 'Cebu City',
                'latitude' => 10.3428,
                'longitude' => 123.9144,
                'disaster_status' => 'safe',
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'barangay_id' => 'B005',
                'name' => 'Talamban',
                'city' => 'Cebu City',
                'latitude' => 10.3572,
                'longitude' => 123.9144,
                'disaster_status' => 'safe',
                'needs_summary' => null,
                'blockchain_address' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('barangays')->insert($barangays);
    }
}
