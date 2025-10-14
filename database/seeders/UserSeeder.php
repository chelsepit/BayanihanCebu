<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        DB::table('users')->insert([
            'user_id' => 'U001',
            'full_name' => 'System Administrator',
            'email' => 'admin@bayanihancebu.com',
            'password_hash' => Hash::make('admin123'), // Change this in production
            'role' => 'admin',
            'barangay_id' => null,
            'blockchain_address' => null,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create LDRRMO user
        DB::table('users')->insert([
            'user_id' => 'U002',
            'full_name' => 'LDRRMO Officer',
            'email' => 'ldrrmo@cebu.gov.ph',
            'password_hash' => Hash::make('ldrrmo123'), // Change this in production
            'role' => 'ldrrmo',
            'barangay_id' => null,
            'blockchain_address' => null,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create BDRRMC users for each barangay
        $barangayIds = ['CC001', 'CC002', 'CC003', 'CC004', 'CC005'];
        foreach ($barangayIds as $index => $barangayId) {
            DB::table('users')->insert([
                'user_id' => 'U' . str_pad($index + 3, 3, '0', STR_PAD_LEFT),
                'full_name' => "BDRRMC Officer {$barangayId}",
                'email' => "bdrrmc.{$barangayId}@bayanihancebu.com",
                'password_hash' => Hash::make('bdrrmc123'), // Change this in production
                'role' => 'bdrrmc',
                'barangay_id' => $barangayId,
                'blockchain_address' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Create sample resident users
        $residents = [
            [
                'user_id' => 'U008',
                'full_name' => 'John Doe',
                'email' => 'john.doe@example.com',
                'barangay_id' => 'CC001'
            ],
            [
                'user_id' => 'U009',
                'full_name' => 'Jane Smith',
                'email' => 'jane.smith@example.com',
                'barangay_id' => 'CC002'
            ],
            [
                'user_id' => 'U010',
                'full_name' => 'Maria Santos',
                'email' => 'maria.santos@example.com',
                'barangay_id' => 'CC003'
            ]
        ];

        foreach ($residents as $resident) {
            DB::table('users')->insert([
                'user_id' => $resident['user_id'],
                'full_name' => $resident['full_name'],
                'email' => $resident['email'],
                'password_hash' => Hash::make('resident123'), // Change this in production
                'role' => 'resident',
                'barangay_id' => $resident['barangay_id'],
                'blockchain_address' => null,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
