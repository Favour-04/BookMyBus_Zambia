@extends('layouts.operator')

@section('title')Seat Map - {{ $route->origin }} → {{ $route->destination }}@endsection
@section('page_title')Seat Map - {{ $route->origin }} → {{ $route->destination }}@endsection

@section('content')


            <!-- Back link -->
            <a href="{{ route('operator.trips.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to Manage Trips
            </a>

            <!-- Trip Info Banner -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div>
                        <h3 class="font-headline font-extrabold text-2xl">{{ $route->origin }} → {{ $route->destination }}</h3>
                        <p class="text-on-surface-variant mt-1">
                            {{ $route->travel_date instanceof \Carbon\Carbon ? $route->travel_date->format('l, d M Y') : \Carbon\Carbon::parse($route->travel_date)->format('l, d M Y') }}
                            · Departure: {{ $route->departure_time }}
                            @if($route->arrival_time)
                             · Arrival: {{ $route->arrival_time }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-primary/20 border border-primary/40"></span>
                            <span>Available</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-on-surface/60 border border-on-surface"></span>
                            <span>Booked</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-tertiary/20 border border-tertiary/40"></span>
                            <span>Pending</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6 pt-6 border-t border-dashed border-outline-variant/20">
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Bus</p>
                        <p class="font-bold mt-1">{{ $route->bus->registration_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Capacity</p>
                        <p class="font-bold mt-1">{{ $capacity }} seats</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Booked</p>
                        <p class="font-bold mt-1 text-primary">{{ count($bookedSeats) }} seats</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Available</p>
                        <p class="font-bold mt-1">{{ $capacity - count($bookedSeats) }} seats</p>
                    </div>
                </div>
            </div>

            <!-- Seat Grid -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-8">
                <h3 class="font-headline font-bold text-lg mb-6">Seat Layout</h3>
                
                <div class="flex flex-col items-center">
                    <!-- Driver area -->
                    <div class="w-full max-w-md mb-6 p-4 bg-surface-container rounded-xl text-center">
                        <span class="material-symbols-outlined text-outline">steering_wheel</span>
                        <p class="text-xs font-bold text-on-surface-variant mt-1">Driver</p>
                    </div>

                    <!-- Seats grid -->
                    <div class="grid gap-3 w-full max-w-md">
                        @foreach($seats as $row)
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @foreach($row as $seat)
                                    @php
                                        $seatClass = match($seat['status']) {
                                            'available' => 'bg-primary/10 border-primary/40 text-primary hover:bg-primary/20 cursor-pointer',
                                            'booked' => 'bg-on-surface/10 border-on-surface/30 text-on-surface-variant cursor-not-allowed',
                                            'pending' => 'bg-tertiary/10 border-tertiary/40 text-tertiary cursor-not-allowed',
                                            default => 'bg-surface-container border-outline-variant',
                                        };
                                        $icon = match($seat['status']) {
                                            'available' => 'event_seat',
                                            'booked' => 'event_busy',
                                            'pending' => 'timer',
                                            default => 'event_seat',
                                        };
                                    @endphp
                                    <div class="flex flex-col items-center p-2 rounded-xl border-2 {{ $seatClass }} transition-colors">
                                        <span class="material-symbols-outlined text-lg">{{ $icon }}</span>
                                        <span class="text-[10px] font-bold mt-0.5">{{ $seat['number'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>

                    <!-- Back door -->
                    <div class="w-full max-w-md mt-6 p-3 bg-surface-container rounded-xl text-center">
                        <p class="text-xs font-bold text-on-surface-variant">Exit</p>
                    </div>
                </div>
            </div>

            <!-- Legend with Passenger Details -->
            @if(!empty($passengerMap))
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mt-6">
                <h3 class="font-headline font-bold text-lg mb-4">Passenger Details</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-outline-variant/20">
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Seat</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Passenger</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Booking ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($passengerMap as $seatNum => $passenger)
                            <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                                <td class="py-3 px-2 font-bold">#{{ $seatNum }}</td>
                                <td class="py-3 px-2">{{ $passenger['name'] }}</td>
                                <td class="py-3 px-2">
                                    @php
                                        $badgeClass = $passenger['status'] === 'confirmed' ? 'bg-primary/10 text-primary' : 'bg-tertiary/10 text-tertiary';
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">
                                        {{ ucfirst($passenger['status']) }}
                                    </span>
                                </td>
                                <td class="py-3 px-2 font-mono text-xs">{{ $passenger['booking_id'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
@endsection
