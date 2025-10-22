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
        Schema::create('donations', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->string('barangay_id', 10);
            $table->foreign('barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            $table->string('user_id', 36)->nullable();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');

            // Tracking
            $table->string('tracking_code', 50)->unique();

            // Donation Details
            $table->decimal('amount', 15, 2);
            $table->enum('donation_type', ['monetary', 'in-kind'])->default('monetary');
            $table->json('items')->nullable(); // For in-kind donations

            // Status
            $table->enum('status', ['pending', 'confirmed', 'distributed', 'completed', 'failed'])->default('pending');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');

            // PayMongo Integration
            $table->string('payment_session_id')->nullable(); // Checkout session ID
            $table->string('payment_id')->nullable(); // Payment ID from PayMongo
            $table->string('payment_source_id')->nullable(); // Source ID (for older payment methods)
            $table->string('checkout_url')->nullable(); // PayMongo checkout URL

            // Blockchain
            $table->string('transaction_hash')->nullable(); // Lisk blockchain tx hash

            // Donor Information
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_phone', 20)->nullable();
            $table->boolean('is_anonymous')->default(false);

            // Distribution tracking
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('distributed_at')->nullable();
            $table->text('distribution_notes')->nullable();
            
            $table->timestamps();

            // Indexes for performance
            $table->index('payment_status');
            $table->index('status');
            $table->index('barangay_id');
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
