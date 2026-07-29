@extends('layouts.landing')

@section('title', 'Search Results - BookMyBus Zambia')

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
</style>
@endpush

@section('content')
<main class="pt-24 pb-20 px-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-headline font-extrabold text-on-surface tracking-tight mb-2">Available Trips</h1>
            <p class="text-zinc-500">
                @if(request('origin') && request('destination'))
                    {{ request('origin') }} → {{ request('destination') }} on {{ request('travel_date') }}
                @else
                    Showing all available trips
                @endif
            </p>
        </div>

        <!-- Results -->
        @if(isset($trips) && $trips->count() > 0)
            <div class="space-y-6">
                @foreach($trips as $trip)
                    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-headline font-bold text-xl text-on-surface">
                                    {{ $trip->route->origin }} → {{ $trip->route->destination }}
                                </h3>
                                <p class="text-sm text-zinc-500">{{ $trip->route->distance_km }} km</p>
                            </div>
                            <div class="text-right">
                                <p class="font-headline font-extrabold text-2xl text-primary">ZMW {{ number_format($trip->fare, 2) }}</p>
                                <p class="text-xs text-zinc-500">per seat</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6 text-sm mb-4">
                            <div>
                                <span class="text-zinc-500">Departure:</span>
                                <span class="font-bold text-on-surface">{{ $trip->departure_time }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500">Date:</span>
                                <span class="font-bold text-on-surface">{{ \Carbon\Carbon::parse($trip->travel_date)->format('d M Y') }}</span>
                            </div>
                            <div>
                                <span class="text-zinc-500">Bus:</span>
                                <span class="font-bold text-on-surface">{{ $trip->bus->bus_number ?? 'TBA' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm">
                                <span class="text-zinc-500">Available seats:</span>
                                <span class="font-bold text-primary">{{ $trip->available_seats }}/{{ $trip->bus->capacity }}</span>
                            </div>
                            <a href="{{ route('booking.seats', $trip) }}" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                                Select Seats
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-12 text-center">
                <span class="material-symbols-outlined text-6xl text-zinc-300 block mb-4">search_off</span>
                <h3 class="font-headline text-2xl font-bold text-on-surface mb-2">No trips found</h3>
                <p class="text-zinc-500 mb-6">Try adjusting your search criteria</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Back to search
                </a>
            </div>
        @endif
    </div>
</main>
@endsection
