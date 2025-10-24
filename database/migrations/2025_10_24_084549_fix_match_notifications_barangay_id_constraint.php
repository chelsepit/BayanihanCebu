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
        Schema::table('match_notifications', function (Blueprint $table) {
            // Drop the existing foreign key if it exists
            try {
                $table->dropForeign(['barangay_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }

            // Recreate the foreign key correctly (barangay_id column is already nullable)
            $table->foreign('barangay_id')
                ->references('barangay_id')
                ->on('barangays')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('match_notifications', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
        });
    }
};
