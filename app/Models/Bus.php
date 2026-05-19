<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operator_id',
        'registration_number',
        'model',
        'seat_capacity',
        'bus_class',
        'amenities',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    // relationship definitions

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    // function helpers

    public function hasAmenity(string $amenity): bool
    {
        return in_array($amenity, $this->amenities ?? []);
    }
}
