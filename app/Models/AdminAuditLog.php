<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'admin_id',
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

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
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
            'operator.verified'  => 'Verified Operator',
            'operator.suspended' => 'Suspended Operator',
            'operator.deleted'   => 'Removed Operator',
            'operator.viewed'    => 'Viewed Operator',
            'user.suspended'     => 'Suspended User',
            'user.activated'     => 'Activated User',
            'user.viewed'        => 'Viewed User',
            'user.deleted'       => 'Removed User',
            'login'              => 'Logged In',
            'logout'             => 'Logged Out',
        ];

        return $labels[$this->event] ?? ucfirst(str_replace('_', ' ', str_replace('.', ' - ', $this->event)));
    }
}