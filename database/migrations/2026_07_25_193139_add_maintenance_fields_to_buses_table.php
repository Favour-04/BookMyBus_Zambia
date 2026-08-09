<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->timestamp('last_maintenance_date')->nullable()->after('amenities');
            $table->timestamp('next_maintenance_date')->nullable()->after('last_maintenance_date');
            $table->unsignedInteger('mileage_km')->nullable()->after('next_maintenance_date');
            $table->text('notes')->nullable()->after('mileage_km');
        });
    }

    public function down(): void
    {
        Schema::table('buses', function (Blueprint $table) {
            $table->dropColumn(['last_maintenance_date', 'next_maintenance_date', 'mileage_km', 'notes']);
        });
    }
};