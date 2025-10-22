<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Add payment status field
            if (!Schema::hasColumn('donations', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('status');
            }

            // Add PayMongo payment source ID
            if (!Schema::hasColumn('donations', 'payment_source_id')) {
                $table->string('payment_source_id')->nullable()->after('payment_status');
            }

            // Add PayMongo payment intent ID
            if (!Schema::hasColumn('donations', 'paymongo_payment_intent_id')) {
                $table->string('paymongo_payment_intent_id')->nullable()->after('payment_source_id');
            }

            // Add payment method (gcash, paymaya, grab_pay, card)
            if (!Schema::hasColumn('donations', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('paymongo_payment_intent_id');
            }

            // Add paid timestamp
            if (!Schema::hasColumn('donations', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_source_id',
                'paymongo_payment_intent_id',
                'payment_method',
                'paid_at'
            ]);
        });
    }
};
