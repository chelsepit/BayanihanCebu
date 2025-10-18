<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change enum to string
        DB::statement("ALTER TABLE resource_needs MODIFY category VARCHAR(100)");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resource_needs MODIFY category ENUM('food', 'water', 'medical', 'shelter', 'clothing', 'other')");
    }
};