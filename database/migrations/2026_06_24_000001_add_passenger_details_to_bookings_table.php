<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('passenger_name')->nullable()->after('seat_number');
            $table->string('passenger_id_number')->nullable()->after('passenger_name');
            $table->string('passenger_phone')->nullable()->after('passenger_id_number');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['passenger_name', 'passenger_id_number', 'passenger_phone']);
        });
    }
};
