<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RouteTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operator_id',
        'name',
        'origin',
        'destination',
        'distance_km',
        'base_fare',
        'is_active',
    ];

    protected $casts = [
        'distance_km' => 'decimal:2',
        'base_fare' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Trips (routes) created from this template.
     */
    public function trips()
    {
        return $this->hasMany(Route::class, 'route_template_id');
    }

    /**
     * Scope active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}