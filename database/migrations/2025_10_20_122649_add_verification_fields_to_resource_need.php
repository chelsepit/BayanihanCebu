<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resource_needs', function (Blueprint $table) {
            // Add verification fields
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending')->after('status');
            $table->string('verified_by', 10)->nullable()->after('verification_status');
            $table->timestamp('verified_at')->nullable()->after('verified_by');
            $table->text('rejection_reason')->nullable()->after('verified_at');
            
            // Add foreign key for verified_by
            $table->foreign('verified_by')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('resource_needs', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verification_status', 'verified_by', 'verified_at', 'rejection_reason']);
        });
    }
};