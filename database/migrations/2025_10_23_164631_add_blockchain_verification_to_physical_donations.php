<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('physical_donations', function (Blueprint $table) {
            // Add verification status
            $table->enum('verification_status', ['unverified', 'verified', 'mismatch'])
                ->default('unverified')
                ->after('blockchain_status')
                ->index();

            // Add off-chain hash (hash of items_description stored locally)
            $table->string('offchain_hash', 66)->nullable()->after('verification_status');

            // Add on-chain hash (hash retrieved from smart contract)
            $table->string('onchain_hash', 66)->nullable()->after('offchain_hash');

            // Add verification timestamp
            $table->timestamp('verified_at')->nullable()->after('onchain_hash');

            // Add last verification check timestamp
            $table->timestamp('last_verification_check')->nullable()->after('verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_donations', function (Blueprint $table) {
            $table->dropColumn([
                'verification_status',
                'offchain_hash',
                'onchain_hash',
                'verified_at',
                'last_verification_check'
            ]);
        });
    }
};
