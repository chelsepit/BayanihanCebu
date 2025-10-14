<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;
use App\Models\Disaster;
use App\Models\UrgentNeed;
use App\Models\Donation;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        // Create Barangays with specific IDs for the first few (for user seeder compatibility)
        $barangays = [
            ['barangay_id' => 'CC001', 'name' => 'Apas', 'city' => 'Cebu City', 'status' => 'safe'],
            ['barangay_id' => 'CC002', 'name' => 'Basak Pardo', 'city' => 'Cebu City', 'status' => 'safe'],
            ['barangay_id' => 'CC003', 'name' => 'Basak San Nicolas', 'city' => 'Cebu City', 'status' => 'warning'],
            ['barangay_id' => 'CC004', 'name' => 'Buasay', 'city' => 'Cebu City', 'status' => 'safe'],
            ['barangay_id' => 'CC005', 'name' => 'Capitol Site', 'city' => 'Cebu City', 'status' => 'safe'],
            ['name' => 'Mabolo', 'city' => 'Cebu City', 'status' => 'safe'],
            ['name' => 'Tisa', 'city' => 'Cebu City', 'status' => 'safe'],
            ['name' => 'Guadalupe', 'city' => 'Cebu City', 'status' => 'emergency'],
            ['name' => 'Bambad', 'city' => 'Cebu City', 'status' => 'warning'],
            ['name' => 'Talamban', 'city' => 'Cebu City', 'status' => 'warning'],
            ['name' => 'Lahug', 'city' => 'Cebu City', 'status' => 'critical'],
        ];

        foreach ($barangays as $barangayData) {
            $barangay = Barangay::create($barangayData);

            // Create disasters for barangays with active status
            if ($barangayData['status'] !== 'safe') {
                $this->createDisasterForBarangay($barangay, $barangayData['status']);
            }
        }
    }

    private function createDisasterForBarangay($barangay, $severity)
    {
        $disasterData = [
            'warning' => [
                'Basak San Nicolas' => [
                    'type' => 'flood',
                    'affected_families' => 50,
                    'total_donations' => 50000,
                    'needs' => ['food'],
                ],
                'Bambad' => [
                    'type' => 'flood',
                    'affected_families' => 45,
                    'total_donations' => 23150,
                    'needs' => ['food', 'water'],
                ],
                'Talamban' => [
                    'type' => 'landslide',
                    'affected_families' => 32,
                    'total_donations' => 82000,
                    'needs' => ['food'],
                ],
            ],
            'critical' => [
                'Lahug' => [
                    'type' => 'fire',
                    'affected_families' => 120,
                    'total_donations' => 84420,
                    'needs' => ['medical', 'shelter'],
                ],
            ],
            'emergency' => [
                'Guadalupe' => [
                    'type' => 'flood',
                    'affected_families' => 250,
                    'total_donations' => 125000,
                    'needs' => ['food', 'water', 'medical'],
                ],
            ],
        ];

        $data = $disasterData[$severity][$barangay->name] ?? null;

        if (!$data) {
            return;
        }

        $disaster = Disaster::create([
            'barangay_id' => $barangay->barangay_id,
            'title' => ucfirst($data['type']) . ' in ' . $barangay->name,
            'description' => 'Active ' . $data['type'] . ' disaster affecting the community.',
            'type' => $data['type'],
            'severity' => $severity,
            'affected_families' => $data['affected_families'],
            'total_donations' => $data['total_donations'],
            'is_active' => true,
            'started_at' => now()->subDays(rand(1, 7)),
        ]);

        // Create urgent needs
        foreach ($data['needs'] as $need) {
            UrgentNeed::create([
                'disaster_id' => $disaster->id,
                'type' => $need,
                'quantity_needed' => rand(50, 200),
                'unit' => $this->getUnitForNeedType($need),
                'quantity_fulfilled' => rand(10, 50),
                'is_fulfilled' => false,
            ]);
        }

        // Create some sample donations
        for ($i = 0; $i < rand(3, 8); $i++) {
            Donation::create([
                'disaster_id' => $disaster->id,
                'tracking_code' => 'DN' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 10)),
                'amount' => rand(1000, 50000),
                'donation_type' => 'monetary',
                'status' => ['confirmed', 'distributed'][rand(0, 1)],
                'transaction_hash' => '0x' . bin2hex(random_bytes(32)),
                'donor_name' => $this->getRandomDonorName(),
                'donor_email' => 'donor' . rand(1000, 9999) . '@example.com',
                'is_anonymous' => rand(0, 1),
                'distributed_at' => rand(0, 1) ? now()->subDays(rand(1, 5)) : null,
            ]);
        }
    }

    private function getUnitForNeedType($type)
    {
        return match($type) {
            'food' => 'kg',
            'water' => 'liters',
            'medical' => 'kits',
            'shelter' => 'tents',
            'clothing' => 'pieces',
            'hygiene' => 'kits',
            default => 'units',
        };
    }

    private function getRandomDonorName()
    {
        $names = [
            'Juan Dela Cruz',
            'Maria Santos',
            'Jose Garcia',
            'Ana Reyes',
            'Pedro Martinez',
            'Carmen Lopez',
            'Miguel Torres',
            'Sofia Rivera',
        ];

        return $names[array_rand($names)];
    }
}