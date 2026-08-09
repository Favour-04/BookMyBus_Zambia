<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Pricing breakdown
            $table->decimal('base_fare', 10, 2)->nullable()->after('amount');
            $table->decimal('service_fee_total', 10, 2)->default(0)->after('base_fare');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('service_fee_total');

            // Promo code reference
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete()->after('discount_amount');

            // Cancellation fields
            $table->foreignId('cancellation_rule_id')->nullable()->constrained('cancellation_rules')->nullOnDelete()->after('promo_code_id');
            $table->decimal('refund_amount', 10, 2)->default(0)->after('cancellation_rule_id');
            $table->timestamp('cancelled_at')->nullable()->after('refund_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['promo_code_id']);
            $table->dropForeign(['cancellation_rule_id']);
            $table->dropColumn([
                'base_fare',
                'service_fee_total',
                'discount_amount',
                'promo_code_id',
                'cancellation_rule_id',
                'refund_amount',
                'cancelled_at',
            ]);
        });
    }
};