<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CancellationRule;
use Carbon\Carbon;

class RefundCalculationService
{
    /**
     * Calculate the refund amount for a booking based on cancellation rules.
     *
     * @param Booking $booking
     * @return array ['refund_amount', 'refund_percentage', 'rule_name', 'rule_id', 'hours_until_departure']
     */
    public function calculateRefund(Booking $booking): array
    {
        $route = $booking->route;
        $operatorId = $route->operator_id;

        // Calculate hours until departure
        $departureDateTime = Carbon::parse($route->travel_date)->setTimeFromTimeString((string) $route->departure_time);
        $hoursUntilDeparture = now()->diffInHours($departureDateTime, false);

        // If departure has already passed, no refund
        if ($hoursUntilDeparture < 0) {
            return [
                'refund_amount' => 0,
                'refund_percentage' => 0,
                'rule_name' => 'No Refund',
                'rule_id' => null,
                'hours_until_departure' => 0,
                'message' => 'Departure has already passed. No refund applicable.',
            ];
        }

        // Find the applicable cancellation rule
        $rule = CancellationRule::where('operator_id', $operatorId)
            ->active()
            ->where('hours_before_departure', '<=', $hoursUntilDeparture)
            ->first();

        if (!$rule) {
            // If no rule matches, default to no refund
            return [
                'refund_amount' => 0,
                'refund_percentage' => 0,
                'rule_name' => 'No Refund Policy',
                'rule_id' => null,
                'hours_until_departure' => $hoursUntilDeparture,
                'message' => 'No cancellation policy applies. No refund available.',
            ];
        }

        $refundAmount = round(($booking->amount * $rule->refund_percentage) / 100, 2);

        return [
            'refund_amount' => $refundAmount,
            'refund_percentage' => (float) $rule->refund_percentage,
            'rule_name' => $rule->name,
            'rule_id' => $rule->id,
            'hours_until_departure' => $hoursUntilDeparture,
            'message' => "Cancellation {$hoursUntilDeparture}h before departure. Refund: {$rule->refund_percentage}% (ZMW {$refundAmount}).",
        ];
    }

    /**
     * Process cancellation and return refund details.
     * This doesn't actually process payment refunds, just calculates and records.
     *
     * @param Booking $booking
     * @return array
     */
    public function processCancellation(Booking $booking): array
    {
        $refundInfo = $this->calculateRefund($booking);

        // Update the booking
        $booking->update([
            'status' => 'cancelled',
            'cancellation_rule_id' => $refundInfo['rule_id'],
            'refund_amount' => $refundInfo['refund_amount'],
            'cancelled_at' => now(),
        ]);

        return $refundInfo;
    }
}