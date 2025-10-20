<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resource_matches', function (Blueprint $table) {
            $table->id();
            
            // Resource Need Info
            $table->foreignId('resource_need_id')->constrained('resource_needs')->onDelete('cascade');
            $table->string('requesting_barangay_id', 10);
            
            // Physical Donation Info
            $table->foreignId('physical_donation_id')->constrained('physical_donations')->onDelete('cascade');
            $table->string('donating_barangay_id', 10);
            
            // Match Details
            $table->decimal('match_score', 5, 2)->nullable();
            $table->string('quantity_requested', 100)->nullable();
            $table->boolean('can_fully_fulfill')->default(false);
            
            // Status Workflow
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed', 'cancelled'])->default('pending');
            
            // Timestamps for workflow
            $table->string('initiated_by', 10);
            $table->timestamp('initiated_at')->useCurrent();
            
            $table->string('responded_by', 10)->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->text('response_message')->nullable();
            
            $table->timestamp('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('requesting_barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            $table->foreign('donating_barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
            $table->foreign('initiated_by')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('responded_by')->references('user_id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index('status');
            $table->index('requesting_barangay_id');
            $table->index('donating_barangay_id');
            $table->index('initiated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resource_matches');
    }
};