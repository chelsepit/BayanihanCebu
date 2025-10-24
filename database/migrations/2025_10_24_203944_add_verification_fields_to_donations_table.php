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
        Schema::table('donations', function (Blueprint $table) {
            // Add verification fields for barangay approval workflow
            if (!Schema::hasColumn('donations', 'verification_status')) {
                $table->enum('verification_status', ['pending', 'verified', 'rejected'])
                    ->default('pending')
                    ->after('blockchain_recorded_at');
            }

            if (!Schema::hasColumn('donations', 'verified_by')) {
                $table->string('verified_by', 36)->nullable()->after('verification_status');
                $table->foreign('verified_by')->references('user_id')->on('users')->onDelete('set null');
            }

            if (!Schema::hasColumn('donations', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verified_by');
            }

            if (!Schema::hasColumn('donations', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('verified_at');
            }

            // Add indexes
            $table->index('verification_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'rejection_reason']);
        });
    }
};
