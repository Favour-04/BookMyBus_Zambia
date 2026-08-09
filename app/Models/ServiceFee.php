<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceFee extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'name',
        'fee_type',
        'fee_value',
        'is_active',
    ];

    protected $casts = [
        'fee_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    /**
     * Calculate the fee amount for a given subtotal.
     */
    public function calculateFee(float $subtotal): float
    {
        if ($this->fee_type === 'fixed') {
            return $this->fee_value;
        }

        // Percentage
        return round(($subtotal * $this->fee_value) / 100, 2);
    }

    /**
     * Scope active fees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}