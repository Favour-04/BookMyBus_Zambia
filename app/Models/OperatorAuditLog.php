<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperatorAuditLog extends Model
{
    protected $fillable = [
        'operator_id',
        'event',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(Operator::class);
    }

    public function auditable()
    {
        return $this->morphTo();
    }

    /**
     * Get a human-readable label for the event.
     */
    public function eventLabel(): string
    {
        $labels = [
            'login' => 'Logged In',
            'logout' => 'Logged Out',
            'booking.viewed' => 'Viewed Booking',
            'booking.cancelled' => 'Cancelled Booking',
            'booking.marked_boarded' => 'Marked as Boarded',
            'booking.undo_boarded' => 'Undid Boarded Status',
            'booking.updated' => 'Updated Booking',
            'booking.notes_updated' => 'Updated Booking Notes',
            'booking.exported' => 'Exported Bookings',
            'booking.bulk_action' => 'Bulk Action on Bookings',
            'trip.created' => 'Created Trip',
            'trip.updated' => 'Updated Trip',
            'trip.cancelled' => 'Cancelled Trip',
            'trip.status_updated' => 'Updated Trip Status',
            'trip.exported' => 'Exported Trips',
            'customer.viewed' => 'Viewed Customer',
            'customer.list_viewed' => 'Viewed Customer List',
            'profile.updated' => 'Updated Profile',
            'profile.password_changed' => 'Changed Password',
            'revenue.viewed' => 'Viewed Revenue Report',
        ];

        return $labels[$this->event] ?? ucfirst(str_replace('_', ' ', str_replace('.', ' - ', $this->event)));
    }
}