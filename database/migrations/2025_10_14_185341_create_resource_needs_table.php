<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_needs', function (Blueprint $table) {
            $table->id();
            $table->string('barangay_id', 10);
            $table->enum('category', ['food', 'water', 'medical', 'shelter', 'clothing', 'other']);
            $table->text('description');
            $table->string('quantity', 100);
            $table->enum('urgency', ['low', 'medium', 'high', 'critical']);
            $table->enum('status', ['pending', 'partially_fulfilled', 'fulfilled'])->default('pending');
            $table->timestamps();

            $table->foreign('barangay_id')
                  ->references('barangay_id')
                  ->on('barangays')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_needs');
    }
};