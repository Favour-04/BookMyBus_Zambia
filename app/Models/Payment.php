<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'transaction_reference',
        'gateway_response',
        'paid_at',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'gateway_response' => 'array',
        'paid_at'          => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────────

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────

    public function isSuccessful(): bool
    {
        return $this->status === 'successful';
    }

    /**
     * Mark payment as successful and confirm the linked booking.
     */
    public function markSuccessful(string $transactionRef, array $gatewayResponse = []): void
    {
        $this->update([
            'status'                => 'successful',
            'transaction_reference' => $transactionRef,
            'gateway_response'      => $gatewayResponse,
            'paid_at'               => now(),
        ]);

        $this->booking->update(['status' => 'confirmed']);
    }

    /**
     * Mark payment as failed.
     */
    public function markFailed(array $gatewayResponse = []): void
    {
        $this->update([
            'status'           => 'failed',
            'gateway_response' => $gatewayResponse,
        ]);
    }
}
