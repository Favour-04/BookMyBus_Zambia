<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->string('registration_number')->unique();
            $table->string('model')->nullable();
            $table->unsignedInteger('seat_capacity');
            $table->enum('bus_class', ['economy', 'business', 'luxury'])->default('economy');
            $table->json('amenities')->nullable()->comment('e.g. ["wifi","ac","usb_charging"]');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('operator_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};
