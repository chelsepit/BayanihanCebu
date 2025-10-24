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
            PhysicalDonationSeeder::class,
            DistributionLogSeeder::class,
            SystemWalletSeeder::class,
        ]);
    }
}
/*
Admin Account:

Email: admin@bayanihancebu.com
Password: admin123
LDRRMO Account:

Email: ldrrmo@cebu.gov.ph
Password: ldrrmo123
BDRRMC Accounts:

Emails: bdrrmc.CC001@bayanihancebu.com (through B005)
Password: bdrrmc123
Resident Accounts:

Email: john.doe@example.com
Email: jane.smith@example.com
Email: maria.santos@example.com
Password: resident123
*/
