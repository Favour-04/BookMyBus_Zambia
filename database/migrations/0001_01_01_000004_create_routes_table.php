<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operator_id')->constrained('operators')->cascadeOnDelete();
            $table->foreignId('bus_id')->constrained('buses')->cascadeOnDelete();
            $table->string('origin')->comment('Origin town/city');
            $table->string('destination')->comment('Destination town/city');
            $table->decimal('distance_km', 8, 2)->nullable()->index()->comment('Distance in kilometers');
            $table->time('departure_time');
            $table->time('arrival_time')->nullable();
            $table->decimal('fare', 10, 2);
            $table->date('travel_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['origin', 'destination']);
            $table->index('origin');
            $table->index('destination');
            $table->index('travel_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
