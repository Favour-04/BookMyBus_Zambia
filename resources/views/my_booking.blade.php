@extends('layouts.landing')

@section('title', 'My Bookings - BookMyBus Zambia')

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
<main class="pt-32 pb-20 px-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-headline font-extrabold text-on-surface tracking-tight mb-4">My Bookings</h1>
            <p class="text-zinc-500">Look up your booking by phone number or reference ID</p>
        </div>

        <!-- Search Form Card -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-8">
            <form action="{{ route('booking.lookup.search') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-on-surface-variant mb-2">Phone Number</label>
                        <input type="tel" name="phone_number" class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="+260 97 123 4567">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-on-surface-variant mb-2">Or Reference ID</label>
                        <input type="text" name="reference_id" class="w-full px-4 py-3 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="BBZ-XXXXXX">
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold text-sm hover:opacity-90 transition-all">
                        Find My Bookings
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        @if(isset($bookings) && $bookings->count() > 0)
        <div class="mt-8 space-y-4">
            <h2 class="text-xl font-headline font-bold text-on-surface mb-4">Your Bookings</h2>
            @foreach($bookings as $booking)
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-headline font-bold text-lg text-on-surface">{{ $booking->route->origin }} → {{ $booking->route->destination }}</h3>
                        <p class="text-sm text-zinc-500">{{ $booking->route->travel_date }}</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold {{ $booking->status === 'confirmed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                        {{ ucfirst($booking->status) }}
                    </span>
                </div>
                <div class="flex items-center gap-6 text-sm">
                    <div>
                        <span class="text-zinc-500">Seat:</span>
                        <span class="font-bold text-on-surface">#{{ $booking->seat_number }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Amount:</span>
                        <span class="font-bold text-primary">ZMW {{ number_format($booking->amount, 2) }}</span>
                    </div>
                    <div>
                        <span class="text-zinc-500">Reference:</span>
                        <span class="font-mono font-bold">{{ $booking->reference_id }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</main>
@endsection
