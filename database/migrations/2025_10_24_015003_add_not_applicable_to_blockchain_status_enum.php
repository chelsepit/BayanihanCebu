<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add 'not_applicable' to blockchain_status enum for online donations
     */
    public function up(): void
    {
        // Update the blockchain_status enum to include 'not_applicable'
        // This is for online donations which don't use blockchain (they use PayMongo verification)
        DB::statement("ALTER TABLE donations MODIFY COLUMN blockchain_status ENUM('pending', 'confirmed', 'failed', 'not_applicable') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE donations MODIFY COLUMN blockchain_status ENUM('pending', 'confirmed', 'failed') DEFAULT 'pending'");
    }
};
