<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_conversations', function (Blueprint $table) {
            $table->id();
            
            // Match Reference (One conversation per match)
            $table->foreignId('resource_match_id')->unique()->constrained('resource_matches')->onDelete('cascade');
            
            // Participants
            $table->string('requesting_barangay_id', 10);
            $table->string('donating_barangay_id', 10);
            
            // Conversation Status
            $table->boolean('is_active')->default(true);
            
            // Last Activity Tracking
            $table->timestamp('last_message_at')->nullable();
            $table->string('last_message_by', 10)->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('requesting_barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            $table->foreign('donating_barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            
            // Indexes
            $table->index('is_active');
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_conversations');
    }
};