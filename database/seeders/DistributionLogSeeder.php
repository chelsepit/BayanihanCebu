<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DistributionLog;

class DistributionLogSeeder extends Seeder
{
    public function run(): void
    {
        $logs = [
            [
                'physical_donation_id' => 2, // Maria Santos' water donation
                'distributed_to' => 'Sitio 1 residents (15 families)',
                'quantity_distributed' => '5 containers (25 gallons)',
                'distributed_by' => 'U003',
                'distributed_at' => now()->subHours(5),
                'notes' => 'Distributed during morning relief operations',
            ],
            [
                'physical_donation_id' => 3, // Medical clinic donation
                'distributed_to' => 'Barangay Health Center',
                'quantity_distributed' => '10 kits',
                'distributed_by' => 'U003',
                'distributed_at' => now()->subDays(2),
                'notes' => 'For emergency use',
            ],
            [
                'physical_donation_id' => 3, // Medical clinic donation
                'distributed_to' => 'Selected affected families',
                'quantity_distributed' => '5 kits',
                'distributed_by' => 'U003',
                'distributed_at' => now()->subDays(2)->addHours(2),
                'notes' => 'Given to families with medical needs',
            ],
        ];

        foreach ($logs as $log) {
            DistributionLog::create($log);
        }
    }
}