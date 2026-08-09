<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->string('name'); // e.g. "Booking Fee", "Processing Fee"
            $table->enum('fee_type', ['fixed', 'percentage']);
            $table->decimal('fee_value', 8, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fees');
    }
};