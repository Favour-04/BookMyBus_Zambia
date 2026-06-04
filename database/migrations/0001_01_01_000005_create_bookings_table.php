z<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->unsignedInteger('seat_number');
            $table->decimal('amount', 10, 2)->comment('Total fare amount');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'expired'])->default('pending')->index();
            $table->timestamp('held_until')->nullable()->comment('Seat hold expires after 10 minutes');
            $table->string('reference_id')->unique()->comment('e.g. BMZ123456');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->unique(['route_id', 'seat_number']); // prevent double booking
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};