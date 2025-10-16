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
                'name' => 'Mabolo',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'name' => 'Tisa',
                'city' => 'Cebu City',
                'disaster_status' => 'safe',
                'disaster_type' => null,
                'affected_families' => 0,
            ],
            [
                'name' => 'Guadalupe',
                'city' => 'Cebu City',
                'disaster_status' => 'emergency',
                'disaster_type' => 'flood',
                'affected_families' => 250,
            ],
            [
                'name' => 'Bambad',
                'city' => 'Cebu City',
                'disaster_status' => 'warning',
                'disaster_type' => 'flood',
                'affected_families' => 45,
            ],
            [
                'name' => 'Talamban',
                'city' => 'Cebu City',
                'disaster_status' => 'warning',
                'disaster_type' => 'landslide',
                'affected_families' => 32,
            ],
            [
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
            'warning' => ['food'],
            'critical' => ['medical', 'shelter'],
            'emergency' => ['food', 'water', 'medical'],
        ];

        $needs = $needsData[$barangay->disaster_status] ?? [];

        foreach ($needs as $category) {
            ResourceNeed::create([
                'barangay_id' => $barangay->barangay_id,
                'category' => $category,
                'description' => ucfirst($category) . ' needed for disaster response in ' . $barangay->name,
                'quantity' => rand(50, 200) . ' units',
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
