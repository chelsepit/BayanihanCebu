<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_needs', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_id', 10);
            $table->string('category', 100); // Changed to string from the start (consolidates documents 18 & 19)
            $table->text('description');
            $table->string('quantity', 100);
            $table->enum('urgency', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['pending', 'partially_fulfilled', 'fulfilled'])->default('pending');

            // Blockchain fields
            $table->string('blockchain_tx_hash', 66)->nullable();
            $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->timestamp('blockchain_recorded_at')->nullable();

            $table->timestamps();

            $table->foreign('barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('cascade');

            // Indexes
            $table->index('blockchain_status');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_needs');
    }
};