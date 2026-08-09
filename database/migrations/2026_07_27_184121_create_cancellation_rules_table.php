<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cancellation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->string('name'); // e.g. "Standard Refund", "Last Minute"
            $table->integer('hours_before_departure'); // e.g. 48, 24, 12, 6, 2, 0
            $table->decimal('refund_percentage', 5, 2); // e.g. 100.00, 75.00, 50.00, 25.00, 0.00
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancellation_rules');
    }
};