<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('physical_donations', function (Blueprint $table) {
            $table->string('blockchain_tx_hash', 66)->nullable()->after('recorded_at');
            $table->string('ipfs_hash', 100)->nullable()->after('blockchain_tx_hash');
            $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending')->after('ipfs_hash');
            $table->timestamp('blockchain_recorded_at')->nullable()->after('blockchain_status');
            $table->text('blockchain_error')->nullable()->after('blockchain_recorded_at');

            $table->index('blockchain_tx_hash');
            $table->index('blockchain_status');
        });
    }

    public function down(): void
    {
        Schema::table('physical_donations', function (Blueprint $table) {
            $table->dropIndex(['blockchain_tx_hash']);
            $table->dropIndex(['blockchain_status']);
            $table->dropColumn([
                'blockchain_tx_hash',
                'ipfs_hash',
                'blockchain_status',
                'blockchain_recorded_at',
                'blockchain_error'
            ]);
        });
    }
};
