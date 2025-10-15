<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barangay;

class UpdateBarangayCoordinatesSeeder extends Seeder
{
    public function run(): void
    {
        // Cebu City Barangay Coordinates
        $coordinates = [
            'Apas' => ['lat' => 10.3467, 'lng' => 123.9073],
            'Basak Pardo' => ['lat' => 10.2814, 'lng' => 123.8562],
            'Basak San Nicolas' => ['lat' => 10.3008, 'lng' => 123.8989],
            'Buasay' => ['lat' => 10.2947, 'lng' => 123.8792],
            'Capitol Site' => ['lat' => 10.3157, 'lng' => 123.8854],
            'Mabolo' => ['lat' => 10.3278, 'lng' => 123.9061],
            'Tisa' => ['lat' => 10.2936, 'lng' => 123.8736],
            'Guadalupe' => ['lat' => 10.3094, 'lng' => 123.8992],
            'Bambad' => ['lat' => 10.2856, 'lng' => 123.8714],
            'Talamban' => ['lat' => 10.3511, 'lng' => 123.9164],
            'Lahug' => ['lat' => 10.3265, 'lng' => 123.8954],
        ];

        foreach ($coordinates as $name => $coords) {
            Barangay::where('name', $name)->update([
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng']
            ]);
        }

        echo "✅ Updated coordinates for " . count($coordinates) . " barangays\n";
    }
}