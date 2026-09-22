<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'qr_code',
        'status',
        'issued_at',
        'used_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'used_at'   => 'datetime',
    ];

    // Boot

    protected static function boot()
    {
        parent::boot();

        // Auto generate the QR code string on creation. It's built from the
        // booking's own reference_id (rather than an unrelated random UUID)
        // so the value encoded in the QR image always matches the Booking ID
        // printed on the ticket next to it.
        static::creating(function ($ticket) {
            $booking = $ticket->booking_id ? Booking::find($ticket->booking_id) : null;

            $ticket->qr_code   = 'BMZ-QR-' . ($booking->reference_id ?? strtoupper(Str::uuid()));
            $ticket->issued_at = now();
        });
    }

    // relationship definitions

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // function helpers

    public function markAsUsed(): void
    {
        $this->update([
            'status'  => 'used',
            'used_at' => now(),
        ]);
    }

    public function isValid(): bool
    {
        return $this->status === 'issued';
    }
}
