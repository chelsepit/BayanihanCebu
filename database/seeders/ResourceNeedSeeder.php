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
                'barangay_id' => 'CC003', // Basak San Nicolas - warning status
                'category' => 'food',
                'description' => 'Rice, canned goods, and noodles for 50 families affected by flooding',
                'quantity' => '50 sacks of rice, 200 canned goods',
                'urgency' => 'high',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC003',
                'category' => 'water',
                'description' => 'Drinking water and water containers for flood victims',
                'quantity' => '100 gallons',
                'urgency' => 'critical',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC008', // Guadalupe - emergency status
                'category' => 'food',
                'description' => 'Emergency food supplies for 250 affected families',
                'quantity' => '250 family food packs',
                'urgency' => 'critical',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC008',
                'category' => 'medical',
                'description' => 'First aid kits and basic medicines for flood victims',
                'quantity' => '50 first aid kits',
                'urgency' => 'high',
                'status' => 'partially_fulfilled',
            ],
            [
                'barangay_id' => 'CC008',
                'category' => 'water',
                'description' => 'Potable water for emergency distribution',
                'quantity' => '500 gallons',
                'urgency' => 'critical',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC010', // Talamban - warning, landslide
                'category' => 'shelter',
                'description' => 'Tarpaulins and emergency tents for families displaced by landslide',
                'quantity' => '30 tarpaulins, 10 tents',
                'urgency' => 'high',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC010',
                'category' => 'food',
                'description' => 'Food supplies for landslide-affected families',
                'quantity' => '32 family food packs',
                'urgency' => 'medium',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC011', // Lahug - critical, fire
                'category' => 'clothing',
                'description' => 'Clean clothes for fire victims who lost their belongings',
                'quantity' => '200 pieces assorted clothing',
                'urgency' => 'high',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC011',
                'category' => 'shelter',
                'description' => 'Emergency shelter materials for fire victims',
                'quantity' => '60 tarpaulins, 20 tents',
                'urgency' => 'critical',
                'status' => 'pending',
            ],
            [
                'barangay_id' => 'CC011',
                'category' => 'food',
                'description' => 'Food relief for 120 families affected by fire',
                'quantity' => '120 family food packs',
                'urgency' => 'high',
                'status' => 'pending',
            ],
        ];

        foreach ($needs as $need) {
            ResourceNeed::create($need);
        }
    }
}
