<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_donations', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_id', 10);
            $table->string('tracking_code', 20)->unique();

            // Donor Information
            $table->string('donor_name', 100);
            $table->string('donor_contact', 20);
            $table->string('donor_email', 100)->nullable();
            $table->text('donor_address');

            // Donation Details
            $table->enum('category', ['food', 'water', 'medical', 'shelter', 'clothing', 'other']);
            $table->text('items_description');
            $table->string('quantity', 100);
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->json('photo_urls')->nullable();

            // Allocation
            $table->string('intended_recipients', 100);
            $table->text('notes')->nullable();

            // Distribution Status
            $table->enum('distribution_status', ['pending_distribution', 'partially_distributed', 'fully_distributed'])
                  ->default('pending_distribution');

            // Recording Info
            $table->string('recorded_by', 10);
            $table->timestamp('recorded_at');

            // ✅ BLOCKCHAIN FIELDS (included from start)
            $table->string('blockchain_tx_hash', 66)->nullable();
            $table->string('ipfs_hash', 100)->nullable();
            $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->timestamp('blockchain_recorded_at')->nullable();
            $table->text('blockchain_error')->nullable();

            $table->timestamps();

            // Foreign Keys
            $table->foreign('barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('cascade');

            $table->foreign('recorded_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');

            // Indexes
            $table->index('blockchain_status');
            $table->index('blockchain_tx_hash');
            $table->index('distribution_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_donations');
    }
};
