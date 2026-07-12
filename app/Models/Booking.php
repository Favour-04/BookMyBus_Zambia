<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'route_id',
        'seat_number',
        'passenger_name',
        'passenger_id_number',
        'passenger_phone',
        'amount',
        'status',
        'held_until',
        'reference_id',
        'id_number',
        'phone_number',
    ];

    protected $casts = [
        'held_until' => 'datetime',
        'amount' => 'decimal:2',
    ];

    // boot

    protected static function boot()
    {
        parent::boot();

        // Function to auto generate a unique reference ID on creation (e.g. BMZ-A3F9K2)
        static::creating(function ($booking) {
            $booking->reference_id = 'BMZ-' . strtoupper(Str::random(6));
            $booking->held_until   = now()->addMinutes(10);
        });
    }

    // relationship definitions

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function route()
    {
        return $this->belongsTo(Route::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function ticket()
    {
        return $this->hasOne(Ticket::class);
    }

    // function helpers

    public function isExpired(): bool
    {
        return $this->status === 'pending' && now()->isAfter($this->held_until);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }
}