<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ResourceNeed;

class ResourceNeedSeeder extends Seeder
{
    public function run(): void
    {
        $needs = [
            [
                'barangay_id' => 'CC001', // Changed from B001
                'category' => 'food',
                'description' => 'Rice, canned goods, and noodles for 50 families affected by flooding',
                'quantity' => '50 sacks of rice, 200 canned goods',
                'urgency' => 'high',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC001',
                'category' => 'water',
                'description' => 'Drinking water and water containers',
                'quantity' => '100 gallons',
                'urgency' => 'critical',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC001',
                'category' => 'medical',
                'description' => 'First aid kits and basic medicines',
                'quantity' => '20 first aid kits',
                'urgency' => 'medium',
                'status' => 'partially_fulfilled',
            ],
            [
                'barangay_id' => 'CC002',
                'category' => 'clothing',
                'description' => 'Clean clothes for children and adults',
                'quantity' => '100 pieces',
                'urgency' => 'low',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'MC001',
                'category' => 'shelter',
                'description' => 'Tarpaulins and emergency tents',
                'quantity' => '30 tarpaulins, 10 tents',
                'urgency' => 'high',
                'status' => 'pending',
            ],
        ];

        foreach ($needs as $need) {
            ResourceNeed::create($need);
        }
    }
}