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
            $table->string('user_id', 10);
            $table->string('barangay_id', 10)->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('transaction_hash')->unique();
            $table->string('donor_name');
            $table->string('donor_email');
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->text('purpose')->nullable();
            $table->timestamps();

            // Add foreign key constraints manually
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_donations');
    }
};
