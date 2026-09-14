<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Route extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operator_id',
        'bus_id',
        'driver_id',
        'route_template_id',
        'origin',
        'destination',
        'distance_km',
        'departure_time',
        'arrival_time',
        'fare',
        'travel_date',
        'is_active',
        'delayed_at',
        'delay_minutes',
        'delay_reason',
        'departed_at',
        'arrived_at',
    ];

    protected $casts = [
        'fare'        => 'decimal:2',
        'distance_km' => 'decimal:2',
        'travel_date' => 'date',
        'is_active'   => 'boolean',
        'delayed_at'  => 'datetime',
        'departed_at' => 'datetime',
        'arrived_at'  => 'datetime',
    ];

    // Query scopes
    public function scopeSearch($query, $origin, $destination, $travel_date)
    {
        return $query->where('origin', 'ilike', '%' . $origin . '%')
            ->where('destination', 'ilike', '%' . $destination . '%')
            ->where('travel_date', $travel_date);
    }

    // relationships definitions

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function routeTemplate()
    {
        return $this->belongsTo(RouteTemplate::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // helper methods

    // Get seat numbers already booked on this route: confirmed outright, or
    // pending with a seat hold (held_until) that hasn't passed yet. A pending
    // booking whose 10-minute hold has expired no longer counts — its seat
    // is free again, even if nothing has gone back and flipped its status.

    public function bookedSeats(): array
    {
        return $this->bookings()
            ->where(function ($query) {
                $query->where('status', 'confirmed')
                    ->orWhere(function ($query) {
                        $query->where('status', 'pending')
                            ->where('held_until', '>', now());
                    });
            })
            ->pluck('seat_number')
            ->toArray();
    }

    // Get all available seat numbers for this route.

    public function availableSeats(): array
    {
        $total  = range(1, $this->bus->seat_capacity);
        $booked = $this->bookedSeats();

        return array_values(array_diff($total, $booked));
    }

    public function availableSeatsCount(): int
    {
        return count($this->availableSeats());
    }

    public function isDelayed(): bool
    {
        return !is_null($this->delayed_at);
    }

    public function hasDeparted(): bool
    {
        return !is_null($this->departed_at);
    }

    public function hasArrived(): bool
    {
        return !is_null($this->arrived_at);
    }

    /**
     * The users who have a confirmed booking on this route.
     */
    public function confirmedPassengers()
    {
        $userIds = $this->bookings()->where('status', 'confirmed')->pluck('user_id')->unique();

        return User::whereIn('id', $userIds)->get();
    }
    public function scopePriceBetween($query, $min = null, $max = null)
    {
        return $query
            ->when($min !== null && $min !== '', fn($q) => $q->where('fare', '>=', $min))
            ->when($max !== null && $max !== '', fn($q) => $q->where('fare', '<=', $max));
    }

    public function scopeDepartureTimeOfDay($query, array $periods)
    {
        if (empty($periods)) {
            return $query;
        }

        return $query->where(function ($q) use ($periods) {
            foreach ($periods as $period) {
                $q->orWhere(function ($q2) use ($period) {
                    match ($period) {
                        'dawn' => $q2->whereTime('departure_time', '>=', '04:00:00')
                            ->whereTime('departure_time', '<', '08:00:00'),
                        'morning' => $q2->whereTime('departure_time', '>=', '08:00:00')
                            ->whereTime('departure_time', '<', '12:00:00'),
                        'afternoon' => $q2->whereTime('departure_time', '>=', '12:00:00')
                            ->whereTime('departure_time', '<', '17:00:00'),
                        'night' => $q2->where(function ($q3) {
                            $q3->whereTime('departure_time', '>=', '17:00:00')
                                ->orWhereTime('departure_time', '<', '04:00:00');
                        }),
                        default => null,
                    };
                });
            }
        });
    }
}
