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
        'origin',
        'destination',
        'distance_km',
        'departure_time',
        'arrival_time',
        'fare',
        'travel_date',
        'is_active',
    ];

    protected $casts = [
        'fare'        => 'decimal:2',
        'distance_km' => 'decimal:2',
        'travel_date' => 'date',
        'is_active'   => 'boolean',
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
}
