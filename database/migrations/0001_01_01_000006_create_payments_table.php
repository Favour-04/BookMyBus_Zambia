<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('ZMW');
            $table->enum('payment_method', ['mtn_money', 'airtel_money', 'zanaco', 'card'])->default('mtn_money');
            $table->enum('status', ['pending', 'successful', 'failed', 'refunded'])->default('pending')->index();
            $table->string('transaction_reference')->unique()->nullable()->index();
            $table->json('gateway_response')->nullable()->comment('Raw API response from payment gateway');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
