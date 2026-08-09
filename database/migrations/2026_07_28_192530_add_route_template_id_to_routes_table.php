<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->foreignId('route_template_id')
                ->nullable()
                ->constrained('route_templates')
                ->nullOnDelete()
                ->after('bus_id');
        });
    }

    public function down(): void
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropForeign(['route_template_id']);
            $table->dropColumn('route_template_id');
        });
    }
};