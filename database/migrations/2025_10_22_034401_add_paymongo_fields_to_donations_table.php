<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            // Only add columns if they don't exist
            if (!Schema::hasColumn('donations', 'payment_session_id')) {
                $table->string('payment_session_id')->nullable()->after('tracking_code');
            }

            if (!Schema::hasColumn('donations', 'payment_id')) {
                $table->string('payment_id')->nullable()->after('tracking_code');
            }

            if (!Schema::hasColumn('donations', 'payment_source_id')) {
                $table->string('payment_source_id')->nullable()->after('tracking_code');
            }

            if (!Schema::hasColumn('donations', 'checkout_url')) {
                $table->string('checkout_url')->nullable()->after('tracking_code');
            }

            if (!Schema::hasColumn('donations', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])
                      ->default('pending')->after('status');
            }

            if (!Schema::hasColumn('donations', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('created_at');
            }
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $columns = [
                'payment_session_id',
                'payment_id',
                'checkout_url',
                // Note: payment_source_id, payment_status, and paid_at might be from the earlier migration
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('donations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
