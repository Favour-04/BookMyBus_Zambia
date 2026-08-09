<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operator_id',
        'full_name',
        'phone_number',
        'email',
        'license_number',
        'license_expiry_date',
        'address',
        'photo_path',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'license_expiry_date' => 'date',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}