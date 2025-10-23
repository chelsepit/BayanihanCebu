<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration adds all fields from online_donations table to donations table
     * so we can use a single unified table for all donation types.
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Transaction ID (from online_donations)
            if (!Schema::hasColumn('donations', 'transaction_id')) {
                $table->string('transaction_id', 100)->unique()->nullable()->after('tracking_code');
            }

            // Source Barangay (where donor is from - from online_donations)
            if (!Schema::hasColumn('donations', 'source_barangay_id')) {
                $table->string('source_barangay_id', 10)->nullable()->after('user_id');
                $table->foreign('source_barangay_id')->references('barangay_id')->on('barangays')->onDelete('set null');
            }

            // Disaster ID (for disaster-specific donations - from online_donations)
            if (!Schema::hasColumn('donations', 'disaster_id')) {
                $table->foreignId('disaster_id')->nullable()->after('barangay_id')->constrained()->onDelete('set null');
            }

            // Payment proof (for manual verification - from online_donations)
            if (!Schema::hasColumn('donations', 'payment_proof_url')) {
                $table->string('payment_proof_url', 255)->nullable()->after('payment_method');
            }

            if (!Schema::hasColumn('donations', 'payment_reference')) {
                $table->string('payment_reference', 100)->nullable()->after('payment_proof_url');
            }

            // Verification fields (from online_donations)
            if (!Schema::hasColumn('donations', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('payment_status');
            }

            if (!Schema::hasColumn('donations', 'verified_by')) {
                $table->string('verified_by', 10)->nullable()->after('verification_status');
                $table->foreign('verified_by')->references('user_id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('donations', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }

            if (!Schema::hasColumn('donations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verified_at');
            }

            // Crypto-specific fields (from online_donations)
            if (!Schema::hasColumn('donations', 'tx_hash')) {
                $table->string('tx_hash', 66)->nullable()->after('transaction_hash');
            }

            if (!Schema::hasColumn('donations', 'wallet_address')) {
                $table->string('wallet_address', 42)->nullable()->after('tx_hash');
            }

            if (!Schema::hasColumn('donations', 'explorer_url')) {
                $table->string('explorer_url', 255)->nullable()->after('wallet_address');
            }

            // Blockchain recording (from online_donations)
            if (!Schema::hasColumn('donations', 'blockchain_tx_hash')) {
                $table->string('blockchain_tx_hash', 66)->nullable()->after('explorer_url');
            }

            if (!Schema::hasColumn('donations', 'blockchain_status')) {
                $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending')->after('blockchain_tx_hash');
            }

            if (!Schema::hasColumn('donations', 'blockchain_recorded_at')) {
                $table->timestamp('blockchain_recorded_at')->nullable()->after('blockchain_status');
            }

            // Add indexes for performance
            $table->index('verification_status');
            $table->index('blockchain_status');
            $table->index('source_barangay_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['source_barangay_id']);
            $table->dropForeign(['verified_by']);
            $table->dropForeign(['disaster_id']);

            // Drop columns
            $table->dropColumn([
                'transaction_id',
                'source_barangay_id',
                'disaster_id',
                'payment_proof_url',
                'payment_reference',
                'verification_status',
                'verified_by',
                'verified_at',
                'rejection_reason',
                'tx_hash',
                'wallet_address',
                'explorer_url',
                'blockchain_tx_hash',
                'blockchain_status',
                'blockchain_recorded_at',
            ]);
        });
    }
};
