<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->string('code', 20)->unique();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 8, 2);
            $table->decimal('min_booking_amount', 10, 2)->default(0);
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('operator_id');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};