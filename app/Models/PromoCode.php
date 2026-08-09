<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'code',
        'discount_type',
        'discount_value',
        'min_booking_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_booking_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Check if the promo code is valid for use.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($now->lt($this->valid_from) || $now->gt($this->valid_until)) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'fixed') {
            return min($this->discount_value, $subtotal);
        }

        // Percentage
        $discount = ($subtotal * $this->discount_value) / 100;

        if ($this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return round($discount, 2);
    }

    /**
     * Increment the usage counter.
     */
    public function markUsed(): void
    {
        $this->increment('used_count');
    }
}