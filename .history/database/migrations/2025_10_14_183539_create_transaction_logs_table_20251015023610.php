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
            $table->string('donor_name', 100);
            $table->string('donor_email', 100)->nullable();
            $table->string('source_barangay_id', 10)->nullable();
            $table->string('target_barangay_id', 10);
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', ['gcash', 'paymaya', 'bank_transfer']);
            $table->string('tx_hash')->unique()->nullable();
            $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->string('explorer_url')->nullable();
            $table->text('message')->nullable();
            $table->timestamps();

            $table->foreign('source_barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('set null');

            $table->foreign('target_barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_donations');
    }
};
