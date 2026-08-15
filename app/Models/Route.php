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
        return $query->where('origin', 'like', '%' . $origin . '%')
                     ->where('destination', 'like', '%' . $destination . '%')
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

    // Get seat numbers already booked on this route (confirmed or pending).
     
    public function bookedSeats(): array
    {
        return $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
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
}
