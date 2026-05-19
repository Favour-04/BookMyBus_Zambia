<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Operator extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'company_name',
        'email',
        'phone_number',
        'password',
        'tpin',
        'is_verified',
        'verified_at',
        'verified_by',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password'    => 'hashed',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];

    // relationship definitions

    public function buses()
    {
        return $this->hasMany(Bus::class);
    }

    public function routes()
    {
        return $this->hasMany(Route::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // function helpers

    public function isVerified(): bool
    {
        return $this->is_verified;
    }
}
