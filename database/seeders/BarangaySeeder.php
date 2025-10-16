<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;
use App\Models\ResourceNeed;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        // Create Barangays with disaster information directly
        $barangays = [
            [
                'barangay_id' => 'CC001',
                'name' => 'Apas',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'barangay_id' => 'CC002',
                'name' => 'Basak Pardo',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'barangay_id' => 'CC003',
                'name' => 'Basak San Nicolas',
                'city' => 'Cebu City',
                'disaster_status' => 'warning',
                'disaster_type' => 'flood',
                'affected_families' => 50,
            ],
            [
                'barangay_id' => 'CC004',
                'name' => 'Buasay',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'barangay_id' => 'CC005',
                'name' => 'Capitol Site',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'barangay_id' => 'CC006',
                'name' => 'Mabolo',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'barangay_id' => 'CC007',
                'name' => 'Tisa',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'barangay_id' => 'CC008',
                'name' => 'Guadalupe',
                'city' => 'Cebu City',
                'disaster_status' => 'emergency',
                'disaster_type' => 'flood',
                'affected_families' => 250,
            ],
            [
                'barangay_id' => 'CC009',
                'name' => 'Bambad',
                'city' => 'Cebu City',
                'disaster_status' => 'warning',
                'disaster_type' => 'flood',
                'affected_families' => 45,
            ],
            [
                'barangay_id' => 'CC010',
                'name' => 'Talamban',
                'city' => 'Cebu City',
                'disaster_status' => 'warning',
                'disaster_type' => 'landslide',
                'affected_families' => 32,
            ],
            [
                'barangay_id' => 'CC011',
                'name' => 'Lahug',
                'city' => 'Cebu City',
                'disaster_status' => 'critical',
                'disaster_type' => 'fire',
                'affected_families' => 120,
            ],
        ];

        foreach ($barangays as $barangayData) {
            $barangay = Barangay::create($barangayData);

            // If barangay needs help, create resource needs
            if ($barangayData['disaster_status'] !== 'safe') {
                $this->createResourceNeedsForBarangay($barangay);
            }
        }
    }

    private function createResourceNeedsForBarangay($barangay)
    {
        $needsData = [
            'warning' => [
                ['category' => 'food', 'quantity' => '100 packs', 'description' => 'Rice, canned goods, and noodles'],
                ['category' => 'water', 'quantity' => '50 gallons', 'description' => 'Purified drinking water'],
            ],
            'critical' => [
                ['category' => 'medical', 'quantity' => '200 kits', 'description' => 'First aid kits and medicines'],
                ['category' => 'shelter', 'quantity' => '50 tents', 'description' => 'Emergency tents and tarpaulins'],
                ['category' => 'food', 'quantity' => '300 packs', 'description' => 'Food supplies for affected families'],
            ],
            'emergency' => [
                ['category' => 'food', 'quantity' => '500 packs', 'description' => 'Emergency food rations'],
                ['category' => 'water', 'quantity' => '200 gallons', 'description' => 'Potable water for distribution'],
                ['category' => 'medical', 'quantity' => '150 kits', 'description' => 'Medical supplies and first aid'],
                ['category' => 'shelter', 'quantity' => '100 tents', 'description' => 'Temporary shelters for displaced families'],
            ],
        ];

        $needs = $needsData[$barangay->disaster_status] ?? [];

        foreach ($needs as $need) {
            ResourceNeed::create([
                'barangay_id' => $barangay->barangay_id, // Use barangay_id string, not id
                'category' => $need['category'],
                'description' => $need['description'] . ' needed for ' . $barangay->disaster_type . ' relief',
                'quantity' => $need['quantity'],
                'urgency' => $this->getUrgencyByStatus($barangay->disaster_status),
                'status' => 'pending',
            ]);
        }
    }

    private function getUrgencyByStatus($status)
    {
        return match($status) {
            'emergency' => 'critical',
            'critical' => 'high',
            'warning' => 'medium',
            default => 'low',
        };
    }
}
