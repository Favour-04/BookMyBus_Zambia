<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancellationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'name',
        'hours_before_departure',
        'refund_percentage',
        'is_active',
    ];

    protected $casts = [
        'hours_before_departure' => 'integer',
        'refund_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Scope to get rules sorted by hours_before_departure descending.
     * The first matching rule (where hours_before_departure <= remaining hours) applies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('hours_before_departure', 'desc');
    }
}