<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemWallet;

class SystemWalletSeeder extends Seeder
{
    public function run(): void
    {
        SystemWallet::create([
            'name' => 'BayanihanCebu Main Wallet',
            'wallet_address' => '0x33D2C101f9b48347DD1E955152704898DE9D3c9C',
            'network' => 'testnet',
            'purpose' => 'receiving',
            'description' => 'Main wallet for receiving all online donations via MetaMask',
            'is_active' => true,
        ]);

        SystemWallet::create([
            'name' => 'BayanihanCebu System Wallet',
            'wallet_address' => env('SYSTEM_WALLET_ADDRESS', '0x0000000000000000000000000000000000000000'),
            'network' => 'testnet',
            'purpose' => 'system',
            'description' => 'System wallet used for logging transactions to smart contract',
            'is_active' => true,
        ]);
    }
}
