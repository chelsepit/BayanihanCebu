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
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['safe', 'warning', 'critical', 'emergency'])->default('safe');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('disasters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barangay_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['flood', 'fire', 'earthquake', 'typhoon', 'landslide', 'other'])->default('other');
            $table->enum('severity', ['warning', 'critical', 'emergency'])->default('warning');
            $table->integer('affected_families')->default(0);
            $table->decimal('total_donations', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('urgent_needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['food', 'water', 'medical', 'shelter', 'clothing', 'hygiene', 'other']);
            $table->integer('quantity_needed')->nullable();
            $table->string('unit')->nullable(); // kg, liters, pieces, etc.
            $table->integer('quantity_fulfilled')->default(0);
            $table->boolean('is_fulfilled')->default(false);
            $table->timestamps();
        });

        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_id')->constrained()->onDelete('cascade');
           $table->string('user_id', 10)->nullable();
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            $table->string('tracking_code')->unique();
            $table->decimal('amount', 15, 2);
            $table->enum('donation_type', ['monetary', 'in-kind'])->default('monetary');
            $table->text('items')->nullable(); // JSON for in-kind donations
            $table->enum('status', ['pending', 'confirmed', 'distributed', 'completed'])->default('pending');
            $table->string('transaction_hash')->nullable(); // Blockchain transaction hash
            $table->string('donor_name')->nullable();
            $table->string('donor_email')->nullable();
            $table->string('donor_phone')->nullable();
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
        Schema::dropIfExists('urgent_needs');
        Schema::dropIfExists('disasters');
        Schema::dropIfExists('barangays');
    }
};