@extends('layouts.operator')

@section('title')Trip Bookings | {{ $tripData['id'] }}@endsection
@section('page_title')Trip Bookings | {{ $tripData['id'] }}@endsection

@push('head')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
        .badge { @apply px-2.5 py-1 rounded-full text-label-caps font-bold; }
        .badge-confirmed { @apply bg-primary/10 text-primary; }
        .badge-pending { @apply bg-tertiary/10 text-tertiary; }
        .badge-cancelled { @apply bg-error-container/20 text-error; }
</style>
@endpush

@section('header_actions')
                <button class="material-symbols-outlined text-on-surface-variant hover:text-primary">notifications</button>
@endsection

@section('content')


            <!-- Trip Summary Card -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="font-headline text-2xl font-extrabold text-on-surface">{{ $tripData['id'] }}</h3>
                        <p class="flex items-center gap-2 mt-1 text-on-surface-variant">
                            <span class="font-bold text-lg">{{ $tripData['route_from'] }}</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            <span class="font-bold text-lg">{{ $tripData['route_to'] }}</span>
                        </p>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="text-center">
                            <p class="text-xs font-bold uppercase text-on-surface-variant">Date</p>
                            <p class="font-bold">{{ $tripData['date'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-bold uppercase text-on-surface-variant">Departure</p>
                            <p class="font-bold">{{ $tripData['departure'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-bold uppercase text-on-surface-variant">Bus</p>
                            <p class="font-bold">{{ $tripData['bus'] }}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-xs font-bold uppercase text-on-surface-variant">Capacity</p>
                            <p class="font-bold">{{ $tripData['booked'] }}/{{ $tripData['capacity'] }}</p>
                        </div>
                    </div>
                </div>
                <!-- Stats row -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-outline-variant/15">
                    <div class="bg-primary/5 p-4 rounded-xl text-center">
                        <p class="text-2xl font-headline font-extrabold text-primary">{{ $stats['confirmed'] }}</p>
                        <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Confirmed</p>
                    </div>
                    <div class="bg-tertiary/5 p-4 rounded-xl text-center">
                        <p class="text-2xl font-headline font-extrabold text-tertiary">{{ $stats['pending'] }}</p>
                        <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Pending</p>
                    </div>
                    <div class="bg-error/5 p-4 rounded-xl text-center">
                        <p class="text-2xl font-headline font-extrabold text-error">{{ $stats['cancelled'] }}</p>
                        <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Cancelled</p>
                    </div>
                    <div class="bg-surface-container-highest p-4 rounded-xl text-center">
                        <p class="text-2xl font-headline font-extrabold">ZMW {{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Revenue</p>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Seat</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Passenger</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Phone</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Reference</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Amount</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Status</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Booked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($bookings as $booking)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4">
                                    <span class="font-bold text-lg text-on-surface">#{{ $booking->seat_number }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium">{{ $booking->passenger_name ?? 'N/A' }}</span>
                                    @if($booking->id_number)
                                        <span class="block text-xs text-on-surface-variant">ID: {{ $booking->id_number }}</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-on-surface-variant">{{ $booking->phone_number ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    <span class="font-mono text-sm font-bold">{{ $booking->reference_id }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold">ZMW {{ number_format($booking->amount, 2) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    @php
                                        $badgeClass = match($booking->status) {
                                            'confirmed' => 'badge-confirmed',
                                            'pending' => 'badge-pending',
                                            'cancelled' => 'badge-cancelled',
                                            default => 'bg-surface-container text-on-surface-variant',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-on-surface-variant">
                                    {{ $booking->created_at->format('d M H:i') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">event_seat</span>
                                    <p class="font-medium text-on-surface-variant">No bookings for this trip yet.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-sm text-on-surface-variant">{{ $bookings->count() }} booking(s)</span>
                    <a href="{{ route('operator.trips.seat-map', $route->id) }}" class="flex items-center gap-1 text-sm font-bold text-primary hover:underline">
                        <span class="material-symbols-outlined text-sm">event_seat</span>
                        View Seat Map
                    </a>
                </div>
            </div>

            <!-- Back button -->
            <div class="mt-6">
                <a href="{{ route('operator.trips.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:underline">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    Back to Manage Trips
                </a>
            </div>
@endsection
