<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Always seed barangays first since users depend on them
        $this->call([
            BarangaySeeder::class,
            UserSeeder::class,
        ]);
    }
}
