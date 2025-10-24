<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds blockchain recording fields for DonationRecorder smart contract integration.
     * All donations (online monetary and physical goods) are recorded to blockchain for transparency.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Blockchain recording fields for DonationRecorder contract
            if (!Schema::hasColumn('donations', 'blockchain_tx_hash')) {
                $table->string('blockchain_tx_hash', 66)->nullable()->after('transaction_hash');
            }

            if (!Schema::hasColumn('donations', 'blockchain_status')) {
                $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending')->after('blockchain_tx_hash');
            }

            if (!Schema::hasColumn('donations', 'blockchain_recorded_at')) {
                $table->timestamp('blockchain_recorded_at')->nullable()->after('blockchain_status');
            }

            if (!Schema::hasColumn('donations', 'explorer_url')) {
                $table->string('explorer_url', 255)->nullable()->after('blockchain_recorded_at');
            }

            // Add index for performance
            $table->index('blockchain_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Drop blockchain columns
            $table->dropColumn([
                'blockchain_tx_hash',
                'blockchain_status',
                'blockchain_recorded_at',
                'explorer_url',
            ]);
        });
    }
};
