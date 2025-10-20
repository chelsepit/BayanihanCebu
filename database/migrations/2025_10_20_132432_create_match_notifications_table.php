<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_notifications', function (Blueprint $table) {
            $table->id();
            
            // Match Reference
            $table->foreignId('resource_match_id')->constrained('resource_matches')->onDelete('cascade');
            
            // Recipient Info
            $table->string('barangay_id', 10);
            $table->string('user_id', 10)->nullable();
            
            // Notification Details
            $table->enum('type', [
                'match_request',
                'match_accepted',
                'match_rejected',
                'match_completed',
                'match_cancelled',
                'new_message'
            ]);
            
            $table->string('title', 255);
            $table->text('message');
            
            // Status
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['barangay_id', 'is_read']);
            $table->index(['user_id', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_notifications');
    }
};