<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_donations', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code', 20)->unique();
            $table->string('transaction_id', 50)->unique()->nullable(); // For GCash/PayMaya ref

            // Donor Information
            $table->string('donor_name', 100);
            $table->string('donor_email', 100)->nullable();
            $table->string('donor_phone', 20)->nullable();
            $table->string('source_barangay_id', 10)->nullable();
            $table->boolean('is_anonymous')->default(false);

            // Donation Details
            $table->string('target_barangay_id', 10);
            $table->foreignId('disaster_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['gcash', 'paymaya', 'bank_transfer', 'metamask', 'crypto'])->default('gcash');

            // Payment Proof (for manual verification)
            $table->string('payment_proof_url')->nullable();
            $table->string('payment_reference', 100)->nullable();

            // Verification
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('verified_by', 10)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Blockchain (MetaMask/Crypto)
            $table->string('tx_hash', 66)->unique()->nullable();
            $table->string('wallet_address', 42)->nullable();
            $table->string('explorer_url')->nullable();

            // Blockchain Logging (for smart contract)
            $table->string('blockchain_tx_hash', 66)->nullable();
            $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->timestamp('blockchain_recorded_at')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('target_barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('cascade');

            $table->foreign('verified_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');

            // Indexes
            $table->index('tracking_code');
            $table->index('verification_status');
            $table->index('payment_method');
            $table->index('blockchain_status');
            $table->index('tx_hash');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_donations');
    }
};
