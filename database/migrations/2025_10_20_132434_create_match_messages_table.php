<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_messages', function (Blueprint $table) {
            $table->id();
            
            // Conversation Reference
            $table->foreignId('conversation_id')->constrained('match_conversations')->onDelete('cascade');
            
            // Sender Info
            $table->string('sender_user_id', 10);
            $table->string('sender_barangay_id', 10);
            
            // Message Content
            $table->enum('message_type', ['text', 'system', 'image', 'document', 'status_update'])->default('text');
            $table->text('message');
            
            // Attachments (optional)
            $table->string('attachment_url', 255)->nullable();
            $table->string('attachment_type', 50)->nullable();
            $table->string('attachment_name', 255)->nullable();
            
            // Read Status
            $table->boolean('is_read_by_requester')->default(false);
            $table->boolean('is_read_by_donor')->default(false);
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('sender_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('sender_barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            
            // Indexes
            $table->index('conversation_id');
            $table->index('created_at');
            $table->index('message_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_messages');
    }
};