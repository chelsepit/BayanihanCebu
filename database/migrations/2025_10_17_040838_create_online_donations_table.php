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

            // Tracking and Transaction
            $table->string('tracking_code', 50)->unique();
            $table->string('transaction_id', 100)->unique()->nullable();

            // Donor Information
            $table->string('donor_name', 100);
            $table->string('donor_email', 100);
            $table->string('donor_phone', 20)->nullable();
            $table->string('source_barangay_id', 10)->nullable(); // Where donor is from
            $table->boolean('is_anonymous')->default(false);

            // Donation Target
            $table->string('target_barangay_id', 10)->nullable(); // Where donation goes
            $table->foreignId('disaster_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 15, 2);

            // Payment Information
            $table->enum('payment_method', ['gcash', 'paymaya', 'bank', 'crypto', 'other'])->default('gcash');
            $table->string('payment_proof_url', 255)->nullable();
            $table->string('payment_reference', 100)->nullable();

            // Verification Status
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('verified_by', 10)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Crypto-specific fields (for Carl's module)
            $table->string('tx_hash', 66)->nullable(); // Crypto transaction hash
            $table->string('wallet_address', 42)->nullable(); // Donor's wallet
            $table->string('explorer_url', 255)->nullable(); // Block explorer link

            // Blockchain Recording (for transparency)
            $table->string('blockchain_tx_hash', 66)->nullable();
            $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->timestamp('blockchain_recorded_at')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('source_barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('set null');

            $table->foreign('target_barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('set null');

            $table->foreign('verified_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');

            // Indexes for performance
            $table->index('tracking_code');
            $table->index('verification_status');
            $table->index('blockchain_status');
            $table->index('payment_method');
            $table->index('target_barangay_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_donations');
    }
};
