@extends('layouts.operator')

@section('title'){{ $customer->full_name }} | Customer Details@endsection
@section('page_title'){{ $customer->full_name }} | Customer Details@endsection

@section('content')

            <a href="{{ route('operator.customers.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>Back to Customers
            </a>

            <!-- Customer Profile Card -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
                <div class="flex items-center gap-4">
                    <div class="h-16 w-16 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-3xl">person</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-extrabold">{{ $customer->full_name }}</h3>
                        <p class="text-on-surface-variant text-sm">{{ $customer->email }}</p>
                        <p class="text-on-surface-variant text-sm">{{ $customer->phone_number ?? 'No phone' }}</p>
                    </div>
                    <div class="ml-auto text-right">
                        <p class="text-xs text-on-surface-variant">Member since</p>
                        <p class="font-bold">{{ $customer->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ $stats['total_bookings'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Bookings</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ $stats['confirmed'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Confirmed</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold">ZMW {{ number_format($stats['total_spent'], 2) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Spent</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-sm font-headline font-extrabold truncate" title="{{ $stats['frequent_route'] }}">{{ $stats['frequent_route'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Most Used Route</p>
                </div>
            </div>

            <!-- Booking History -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="px-5 py-4 border-b border-outline-variant/10">
                    <h3 class="font-headline font-bold text-lg">Booking History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Ref</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Route</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Date</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Seat</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Amount</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Status</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Booked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($bookings as $booking)
                            @php $statusColor = match($booking->status) { 'confirmed' => 'bg-primary/10 text-primary', 'pending' => 'bg-tertiary/10 text-tertiary', 'cancelled' => 'bg-error-container/20 text-error', default => 'bg-surface-container text-on-surface-variant' }; @endphp
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4"><a href="{{ route('operator.bookings.show', $booking->id) }}" class="font-mono text-sm font-bold text-primary hover:underline">{{ $booking->reference_id }}</a></td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1 text-sm">{{ $booking->route->origin }} <span class="material-symbols-outlined text-xs text-outline">arrow_forward</span> {{ $booking->route->destination }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm">{{ $booking->route->travel_date instanceof \Carbon\Carbon ? $booking->route->travel_date->format('d M Y') : \Carbon\Carbon::parse($booking->route->travel_date)->format('d M Y') }}</td>
                                <td class="px-5 py-4"><span class="font-bold">#{{ $booking->seat_number }}</span></td>
                                <td class="px-5 py-4"><span class="font-bold">ZMW {{ number_format($booking->amount, 2) }}</span></td>
                                <td class="px-5 py-4"><span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusColor }}">{{ ucfirst($booking->status) }}</span></td>
                                <td class="px-5 py-4 text-sm text-on-surface-variant">{{ $booking->created_at->format('d M H:i') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="py-16 text-center"><span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">book_online</span><p class="font-medium text-on-surface-variant">No bookings found for this customer.</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
@endsection
