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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'passenger_name')) {
                $table->string('passenger_name', 255)->nullable()->after('seat_number');
            }
            if (!Schema::hasColumn('bookings', 'id_number')) {
                $table->string('id_number', 50)->nullable()->after('passenger_name');
            }
            if (!Schema::hasColumn('bookings', 'phone_number')) {
                $table->string('phone_number', 20)->nullable()->after('id_number');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $columns = ['passenger_name', 'id_number', 'phone_number'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};