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
