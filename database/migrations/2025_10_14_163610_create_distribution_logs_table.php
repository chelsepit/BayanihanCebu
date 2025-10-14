<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('physical_donation_id')->constrained()->onDelete('cascade');
            $table->string('distributed_to', 200);
            $table->string('quantity_distributed', 100);
            $table->string('distributed_by', 10);
            $table->timestamp('distributed_at');
            $table->text('notes')->nullable();
            $table->json('photo_urls')->nullable();
            $table->timestamps();

            $table->foreign('distributed_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_logs');
    }
};