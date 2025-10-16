<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resource_needs', function (Blueprint $table) {
            $table->string('blockchain_tx_hash', 66)->nullable()->after('updated_at');
            $table->enum('blockchain_status', ['pending', 'confirmed', 'failed'])->default('pending')->after('blockchain_tx_hash');
            $table->timestamp('blockchain_recorded_at')->nullable()->after('blockchain_status');

            $table->index('blockchain_tx_hash');
            $table->index('blockchain_status');
        });
    }

    public function down(): void
    {
        Schema::table('resource_needs', function (Blueprint $table) {
            $table->dropIndex(['blockchain_tx_hash']);
            $table->dropIndex(['blockchain_status']);
            $table->dropColumn([
                'blockchain_tx_hash',
                'blockchain_status',
                'blockchain_recorded_at'
            ]);
        });
    }
};
