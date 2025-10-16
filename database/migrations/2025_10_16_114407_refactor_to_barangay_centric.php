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
        // Add disaster_type to barangays table
        Schema::table('barangays', function (Blueprint $table) {
            $table->enum('disaster_type', ['flood', 'fire', 'earthquake', 'typhoon', 'landslide', 'other'])
                  ->nullable()
                  ->after('disaster_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate disasters table
        Schema::create('disasters', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_id', 10);
            $table->foreign('barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
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

        // Recreate urgent_needs table
        Schema::create('urgent_needs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disaster_id')->constrained()->onDelete('cascade');
            $table->enum('category', ['food', 'water', 'medical', 'shelter', 'clothing', 'other']);
            $table->text('description');
            $table->string('quantity', 100)->nullable();
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['pending', 'partially_fulfilled', 'fulfilled'])->default('pending');
            $table->timestamps();
        });

        // Revert donations table
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
            $table->foreignId('disaster_id')->after('id')->constrained()->onDelete('cascade');
        });

        // Remove disaster_type from barangays
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropColumn('disaster_type');
        });
    }
};
