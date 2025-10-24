<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add new donation_status column with default value
        Schema::table('barangays', function (Blueprint $table) {
            $table->enum('donation_status', ['pending', 'in_progress', 'completed'])->default('pending')->after('longitude');
        });

        // Step 2: Migrate existing data from disaster_status to donation_status
        // Map old disaster statuses to new donation statuses:
        // safe -> completed (no help needed)
        // warning -> in_progress (help is being arranged)
        // critical -> pending (needs immediate help)
        // emergency -> pending (needs immediate help)
        DB::statement("UPDATE barangays SET donation_status = CASE
            WHEN disaster_status = 'safe' THEN 'completed'
            WHEN disaster_status = 'warning' THEN 'in_progress'
            WHEN disaster_status = 'critical' THEN 'pending'
            WHEN disaster_status = 'emergency' THEN 'pending'
            ELSE 'pending'
        END");

        // Step 3: Drop the old disaster_status column
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropColumn('disaster_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate the old disaster_status column
        Schema::table('barangays', function (Blueprint $table) {
            $table->enum('disaster_status', ['safe', 'warning', 'critical', 'emergency'])->default('safe')->after('longitude');
        });

        // Copy and convert data back
        DB::statement("UPDATE barangays SET disaster_status = CASE
            WHEN donation_status = 'completed' THEN 'safe'
            WHEN donation_status = 'in_progress' THEN 'warning'
            WHEN donation_status = 'pending' THEN 'critical'
            ELSE 'safe'
        END");

        // Drop the new column
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropColumn('donation_status');
        });
    }
};
