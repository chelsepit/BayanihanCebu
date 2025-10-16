<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blockchain_transaction_logs', function (Blueprint $table) {
            $table->id();

            // Transaction Info
            $table->string('tx_hash', 66)->unique();
            $table->enum('transaction_type', [
                'physical_donation',
                'online_donation',
                'distribution',
                'resource_need'
            ]);
            $table->unsignedBigInteger('reference_id'); // ID from related table

            // Blockchain Details
            $table->string('from_address', 42);
            $table->string('to_address', 42)->nullable(); // Contract address
            $table->string('contract_address', 42)->nullable();
            $table->string('function_called', 100)->nullable(); // e.g., "recordPhysicalDonation"
            $table->decimal('gas_used', 20, 0)->nullable();
            $table->decimal('gas_price', 30, 0)->nullable();
            $table->string('block_number', 20)->nullable();

            // Status
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);

            // IPFS (if applicable)
            $table->string('ipfs_hash', 100)->nullable();

            // Timestamps
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('tx_hash');
            $table->index('transaction_type');
            $table->index('reference_id');
            $table->index('status');
            $table->index(['transaction_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blockchain_transaction_logs');
    }
};
