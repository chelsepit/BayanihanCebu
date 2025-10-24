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
        // Use raw SQL to ensure the column is nullable
        DB::statement('ALTER TABLE `match_notifications` MODIFY `barangay_id` VARCHAR(10) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to NOT NULL if needed
        DB::statement('ALTER TABLE `match_notifications` MODIFY `barangay_id` VARCHAR(10) NOT NULL');
    }
};
