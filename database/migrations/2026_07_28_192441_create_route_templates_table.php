<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('route_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->string('name');
            $table->string('origin');
            $table->string('destination');
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->decimal('base_fare', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->index('operator_id');
            $table->index(['origin', 'destination']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_templates');
    }
};