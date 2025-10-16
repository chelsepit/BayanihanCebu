<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_wallets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100); // e.g., "Main System Wallet", "BayanihanCebu Central"
            $table->string('wallet_address', 42)->unique();
            $table->enum('network', ['mainnet', 'testnet'])->default('testnet');
            $table->enum('purpose', ['receiving', 'distribution', 'system'])->default('receiving');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('wallet_address');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_wallets');
    }
};
