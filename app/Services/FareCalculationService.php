<?php

namespace App\Services;

use App\Models\PromoCode;
use App\Models\Route;
use App\Models\ServiceFee;

class FareCalculationService
{
    /**
     * Calculate the final fare for a booking.
     *
     * @param Route $route
     * @param PromoCode|null $promoCode
     * @return array ['base_fare', 'service_fees', 'service_fee_total', 'discount', 'promo_code_id', 'total']
     */
    public function calculate(Route $route, ?PromoCode $promoCode = null): array
    {
        $baseFare = (float) $route->fare;
        $operatorId = $route->operator_id;

        // 1. Calculate service fees
        $serviceFees = ServiceFee::where('operator_id', $operatorId)
            ->active()
            ->get()
            ->map(function ($fee) use ($baseFare) {
                return [
                    'name' => $fee->name,
                    'type' => $fee->fee_type,
                    'value' => $fee->fee_value,
                    'amount' => $fee->calculateFee($baseFare),
                ];
            });

        $serviceFeeTotal = $serviceFees->sum('amount');

        // 2. Calculate subtotal (base fare + service fees)
        $subtotal = $baseFare + $serviceFeeTotal;

        // 3. Apply promo code discount
        $discount = 0;
        $promoCodeId = null;

        if ($promoCode && $promoCode->operator_id === $operatorId && $promoCode->isValid()) {
            if ($subtotal >= $promoCode->min_booking_amount) {
                $discount = $promoCode->calculateDiscount($subtotal);
                $promoCodeId = $promoCode->id;
            }
        }

        // 4. Final total
        $total = round($subtotal - $discount, 2);

        return [
            'base_fare' => $baseFare,
            'service_fees' => $serviceFees,
            'service_fee_total' => $serviceFeeTotal,
            'discount' => $discount,
            'promo_code_id' => $promoCodeId,
            'total' => $total,
        ];
    }

    /**
     * Validate a promo code for a given operator and subtotal.
     *
     * @param string $code
     * @param int $operatorId
     * @param float $subtotal
     * @return array ['valid' => bool, 'promo_code' => PromoCode|null, 'message' => string]
     */
    public function validatePromoCode(string $code, int $operatorId, float $subtotal): array
    {
        $promoCode = PromoCode::where('code', $code)
            ->where('operator_id', $operatorId)
            ->first();

        if (!$promoCode) {
            return ['valid' => false, 'promo_code' => null, 'message' => 'Invalid promo code.'];
        }

        if (!$promoCode->isValid()) {
            return ['valid' => false, 'promo_code' => $promoCode, 'message' => 'This promo code has expired or is no longer active.'];
        }

        if ($subtotal < $promoCode->min_booking_amount) {
            return [
                'valid' => false,
                'promo_code' => $promoCode,
                'message' => 'Minimum booking amount of ZMW ' . number_format($promoCode->min_booking_amount, 2) . ' required for this code.',
            ];
        }

        $discount = $promoCode->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'promo_code' => $promoCode,
            'discount' => $discount,
            'message' => 'Promo code applied! You save ZMW ' . number_format($discount, 2) . '.',
        ];
    }
}