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
            $table->string('barangay_id', 10);
            $table->foreign('barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            $table->string('user_id', 36)->nullable();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->string('tracking_code', 50)->unique();
            $table->decimal('amount', 15, 2);
            $table->enum('donation_type', ['monetary', 'in-kind'])->default('monetary');
            $table->json('items')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'distributed', 'completed'])->default('pending');
            $table->string('transaction_hash')->nullable();
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_phone', 20)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->timestamp('distributed_at')->nullable();
            $table->text('distribution_notes')->nullable();
            $table->timestamps();
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
