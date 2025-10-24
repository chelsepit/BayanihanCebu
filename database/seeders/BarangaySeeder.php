<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;
use App\Models\ResourceNeed;

class BarangaySeeder extends Seeder
{
    public function run(): void
    {
        // Create Barangays with disaster information and real coordinates
        $barangays = [
            [
                'barangay_id' => 'CC001',
                'name' => 'Apas',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3367,
                'longitude' => 123.9069,
                'donation_status' => 'completed',
                'disaster_type' => null,
                'affected_families' => 0,
                'contact_person' => 'Juan Dela Cruz',
                'contact_phone' => '032-123-4567',
                'contact_email' => 'apas@cebu.gov.ph',
            ],
            [
                'barangay_id' => 'CC002',
                'name' => 'Basak Pardo',
                'city' => 'Cebu City',
                'district' => 'District 1',
                'latitude' => 10.2835,
                'longitude' => 123.8486,
                'donation_status' => 'completed',
                'disaster_type' => null,
                'affected_families' => 0,
                'contact_person' => 'Maria Santos',
                'contact_phone' => '032-234-5678',
                'contact_email' => 'basakpardo@cebu.gov.ph',
            ],
            [
                'barangay_id' => 'CC003',
                'name' => 'Basak San Nicolas',
                'city' => 'Cebu City',
                'district' => 'District 1',
                'latitude' => 10.2985,
                'longitude' => 123.8899,
                'donation_status' => 'in_progress',
                'disaster_type' => 'flood',
                'affected_families' => 50,
                'contact_person' => 'Pedro Reyes',
                'contact_phone' => '032-345-6789',
                'contact_email' => 'basaksannicolas@cebu.gov.ph',
                'needs_summary' => 'Flooding in low-lying areas. Immediate assistance needed.',
            ],
            [
                'barangay_id' => 'CC004',
                'name' => 'Busay',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3667,
                'longitude' => 123.9333,
                'donation_status' => 'completed',
                'disaster_type' => null,
                'affected_families' => 0,
                'contact_person' => 'Rosa Garcia',
                'contact_phone' => '032-456-7890',
                'contact_email' => 'busay@cebu.gov.ph',
            ],
            [
                'barangay_id' => 'CC005',
                'name' => 'Capitol Site',
                'city' => 'Cebu City',
                'district' => 'District 1',
                'latitude' => 10.3145,
                'longitude' => 123.8932,
                'donation_status' => 'completed',
                'disaster_type' => null,
                'affected_families' => 0,
                'contact_person' => 'Jose Mendoza',
                'contact_phone' => '032-567-8901',
                'contact_email' => 'capitolsite@cebu.gov.ph',
            ],
            [
                'barangay_id' => 'CC006',
                'name' => 'Mabolo',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3285,
                'longitude' => 123.9120,
                'donation_status' => 'completed',
                'disaster_type' => null,
                'affected_families' => 0,
                'contact_person' => 'Ana Lopez',
                'contact_phone' => '032-678-9012',
                'contact_email' => 'mabolo@cebu.gov.ph',
            ],
            [
                'barangay_id' => 'CC007',
                'name' => 'Tisa',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3265,
                'longitude' => 123.8852,
                'donation_status' => 'completed',
                'disaster_type' => null,
                'affected_families' => 0,
                'contact_person' => 'Carlos Ramos',
                'contact_phone' => '032-789-0123',
                'contact_email' => 'tisa@cebu.gov.ph',
            ],
            [
                'barangay_id' => 'CC008',
                'name' => 'Guadalupe',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3104,
                'longitude' => 123.9016,
                'donation_status' => 'pending',
                'disaster_type' => 'flood',
                'affected_families' => 250,
                'contact_person' => 'Elena Cruz',
                'contact_phone' => '032-890-1234',
                'contact_email' => 'guadalupe@cebu.gov.ph',
                'needs_summary' => 'Severe flooding. Multiple families evacuated. Urgent relief needed.',
            ],
            [
                'barangay_id' => 'CC009',
                'name' => 'Bambad',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3456,
                'longitude' => 123.8765,
                'donation_status' => 'in_progress',
                'disaster_type' => 'flood',
                'affected_families' => 45,
                'contact_person' => 'Miguel Torres',
                'contact_phone' => '032-901-2345',
                'contact_email' => 'bambad@cebu.gov.ph',
                'needs_summary' => 'Rising water levels. Monitoring ongoing.',
            ],
            [
                'barangay_id' => 'CC010',
                'name' => 'Talamban',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3471,
                'longitude' => 123.9127,
                'donation_status' => 'in_progress',
                'disaster_type' => 'landslide',
                'affected_families' => 32,
                'contact_person' => 'Sofia Hernandez',
                'contact_phone' => '032-012-3456',
                'contact_email' => 'talamban@cebu.gov.ph',
                'needs_summary' => 'Landslide risk in hillside areas. Preemptive evacuation ongoing.',
            ],
            [
                'barangay_id' => 'CC011',
                'name' => 'Lahug',
                'city' => 'Cebu City',
                'district' => 'District 2',
                'latitude' => 10.3272,
                'longitude' => 123.8996,
                'donation_status' => 'pending',
                'disaster_type' => 'fire',
                'affected_families' => 120,
                'contact_person' => 'Ricardo Bautista',
                'contact_phone' => '032-123-7890',
                'contact_email' => 'lahug@cebu.gov.ph',
                'needs_summary' => 'Fire incident displaced families. Emergency shelter and supplies needed.',
            ],
        ];

        foreach ($barangays as $barangayData) {
            $barangay = Barangay::create($barangayData);

            // If barangay needs help, create resource needs
            if ($barangayData['disaster_status'] !== 'safe') {
                $this->createResourceNeedsForBarangay($barangay);
            }
        }

        $this->command->info('✅ Seeded ' . count($barangays) . ' barangays with coordinates and resource needs');
    }

    private function createResourceNeedsForBarangay($barangay)
    {
        $needsData = [
            'warning' => [
                ['category' => 'Food', 'quantity' => '100 packs', 'description' => 'Rice, canned goods, and noodles'],
                ['category' => 'Water', 'quantity' => '50 gallons', 'description' => 'Purified drinking water'],
                ['category' => 'Clothing', 'quantity' => '75 sets', 'description' => 'Clean clothes and blankets'],
            ],
            'critical' => [
                ['category' => 'Medical Supplies', 'quantity' => '200 kits', 'description' => 'First aid kits and medicines'],
                ['category' => 'Shelter Materials', 'quantity' => '50 tents', 'description' => 'Emergency tents and tarpaulins'],
                ['category' => 'Food', 'quantity' => '300 packs', 'description' => 'Food supplies for affected families'],
                ['category' => 'Hygiene Kits', 'quantity' => '150 sets', 'description' => 'Soap, toothbrush, and sanitary items'],
            ],
            'emergency' => [
                ['category' => 'Food', 'quantity' => '500 packs', 'description' => 'Emergency food rations'],
                ['category' => 'Water', 'quantity' => '200 gallons', 'description' => 'Potable water for distribution'],
                ['category' => 'Medical Supplies', 'quantity' => '150 kits', 'description' => 'Medical supplies and first aid'],
                ['category' => 'Shelter Materials', 'quantity' => '100 tents', 'description' => 'Temporary shelters for displaced families'],
                ['category' => 'Hygiene Kits', 'quantity' => '250 sets', 'description' => 'Personal hygiene and sanitation supplies'],
                ['category' => 'Clothing', 'quantity' => '200 sets', 'description' => 'Emergency clothing and blankets'],
            ],
        ];

        $needs = $needsData[$barangay->disaster_status] ?? [];

        foreach ($needs as $need) {
            ResourceNeed::create([
                'barangay_id' => $barangay->barangay_id,
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