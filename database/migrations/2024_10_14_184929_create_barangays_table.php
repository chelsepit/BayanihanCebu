<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangays', function (Blueprint $table) {
            $table->string('barangay_id', 10)->primary();
            $table->string('name', 100);
            $table->string('slug')->unique()->nullable(); // Consolidated from 2025_10_16_132805
            $table->string('city', 100);
            $table->string('district', 50)->nullable();
            $table->decimal('latitude', 10, 8)->nullable(); // Fixed precision
            $table->decimal('longitude', 11, 8)->nullable(); // Fixed precision
            
            // Disaster management fields
            $table->enum('disaster_status', ['safe', 'warning', 'critical', 'emergency'])->default('safe');
            $table->enum('disaster_type', ['flood', 'fire', 'earthquake', 'typhoon', 'landslide', 'other'])->nullable(); // Consolidated from 2025_10_16_114407
            
            // Contact information
            $table->string('contact_person', 100)->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email', 100)->nullable();
            
            // Metrics
            $table->integer('affected_families')->default(0);
            $table->text('needs_summary')->nullable();
            
            // Blockchain
            $table->string('blockchain_address', 50)->unique()->nullable();
            
            $table->timestamps();
        });

        // Add foreign key constraint to users table
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
        });
        
        Schema::dropIfExists('barangays');
    }
};