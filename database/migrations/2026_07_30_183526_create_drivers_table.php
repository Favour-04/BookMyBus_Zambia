<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->string('license_number')->unique();
            $table->date('license_expiry_date')->nullable();
            $table->string('address')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Add driver_id and delay fields to routes table
        Schema::table('routes', function (Blueprint $table) {
            $table->foreignId('driver_id')->nullable()->after('bus_id')->constrained('drivers')->nullOnDelete();
            $table->timestamp('delayed_at')->nullable()->after('arrival_time');
            $table->integer('delay_minutes')->nullable()->after('delayed_at');
            $table->string('delay_reason')->nullable()->after('delay_minutes');
            $table->timestamp('departed_at')->nullable()->after('delay_reason');
            $table->timestamp('arrived_at')->nullable()->after('departed_at');
        });
    }

    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn([
                'driver_id', 'delayed_at', 'delay_minutes',
                'delay_reason', 'departed_at', 'arrived_at'
            ]);
        });
        Schema::dropIfExists('drivers');
    }
};