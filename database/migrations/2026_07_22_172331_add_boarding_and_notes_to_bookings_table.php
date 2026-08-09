<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('boarded_at')->nullable()->after('held_until');
            $table->string('boarded_by')->nullable()->after('boarded_at');
            $table->text('notes')->nullable()->after('boarded_by');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['boarded_at', 'boarded_by', 'notes']);
        });
    }
};