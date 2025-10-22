<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_user_id']);
            $table->dropForeign(['sender_barangay_id']);
        });

        Schema::table('match_messages', function (Blueprint $table) {
            $table->string('sender_user_id', 10)->nullable()->change();
            $table->string('sender_barangay_id', 10)->nullable()->change();
        });

        Schema::table('match_messages', function (Blueprint $table) {
            $table->foreign('sender_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('sender_barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('match_messages', function (Blueprint $table) {
            $table->dropForeign(['sender_user_id']);
            $table->dropForeign(['sender_barangay_id']);
        });

        Schema::table('match_messages', function (Blueprint $table) {
            $table->string('sender_user_id', 10)->nullable(false)->change();
            $table->string('sender_barangay_id', 10)->nullable(false)->change();
        });

        Schema::table('match_messages', function (Blueprint $table) {
            $table->foreign('sender_user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('sender_barangay_id')->references('barangay_id')->on('barangays')->onDelete('cascade');
        });
    }
};
