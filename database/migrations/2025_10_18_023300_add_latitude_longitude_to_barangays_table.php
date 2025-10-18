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
        // Check if columns don't exist before adding them
        if (!Schema::hasColumn('barangays', 'latitude')) {
            Schema::table('barangays', function (Blueprint $table) {
                $table->decimal('latitude', 10, 8)->nullable()->after('district');
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangays', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};