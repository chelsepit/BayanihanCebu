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
        Schema::table('resource_needs', function (Blueprint $table) {
            // Change category from enum/short string to varchar(100)
            $table->string('category', 100)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resource_needs', function (Blueprint $table) {
            // Revert back if needed
            $table->string('category', 50)->change();
        });
    }
};