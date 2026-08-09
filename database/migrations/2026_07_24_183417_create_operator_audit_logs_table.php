<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('operator_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->string('event'); // e.g. 'login', 'booking.cancelled', 'trip.created', 'customer.viewed'
            $table->string('auditable_type')->nullable(); // polymorphic: model being acted upon
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('description')->nullable(); // human-readable summary
            $table->json('old_values')->nullable(); // previous state (for updates)
            $table->json('new_values')->nullable(); // new state (for creates/updates)
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->index(['operator_id', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index('event');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_audit_logs');
    }
};