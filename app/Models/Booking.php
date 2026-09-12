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
        'base_fare',
        'service_fee_total',
        'discount_amount',
        'promo_code_id',
        'cancellation_rule_id',
        'refund_amount',
        'cancelled_at',
        'status',
        'held_until',
        'reference_id',
        'group_reference',
        'id_number',
        'phone_number',
        'boarded_at',
        'boarded_by',
        'notes',
    ];

    protected $casts = [
        'held_until' => 'datetime',
        'boarded_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'amount' => 'decimal:2',
        'base_fare' => 'decimal:2',
        'service_fee_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
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

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function cancellationRule()
    {
        return $this->belongsTo(CancellationRule::class);
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

    public function isBoarded(): bool
    {
        return !is_null($this->boarded_at);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    public function markBoarded(string $boardedBy = 'operator'): void
    {
        $this->update([
            'status' => 'confirmed',
            'boarded_at' => now(),
            'boarded_by' => $boardedBy,
        ]);
    }

    public function undoBoarded(): void
    {
        $this->update([
            'boarded_at' => null,
            'boarded_by' => null,
        ]);
    }
}