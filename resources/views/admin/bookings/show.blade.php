@extends('layouts.admin')

@section('title', $booking->reference_id)
@section('page_title', 'Booking Detail')

@section('content')
    <a href="{{ route('admin.bookings.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-on-surface-variant hover:text-primary mb-6">
        <span class="material-symbols-outlined text-base">arrow_back</span>Back to Bookings
    </a>

    <!-- Header -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="h-14 w-14 rounded-2xl bg-primary/20 flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined text-2xl">confirmation_number</span>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h3 class="font-headline font-extrabold text-2xl font-mono">{{ $booking->reference_id }}</h3>
                        @php
                            $badgeClass = match($booking->status) {
                                'confirmed' => 'bg-primary/10 text-primary',
                                'pending' => 'bg-tertiary/10 text-tertiary',
                                'cancelled' => 'bg-error/10 text-error',
                                default => 'bg-surface-container-high text-on-surface-variant',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                    <p class="text-sm text-on-surface-variant">
                        {{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}
                        · {{ $booking->route->travel_date ? $booking->route->travel_date->format('d M Y') : 'N/A' }}
                    </p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Amount Paid</p>
                <p class="font-headline font-extrabold text-3xl text-primary">ZMW {{ number_format($booking->amount, 2) }}</p>
                <p class="text-xs text-on-surface-variant">Seat {{ $booking->seat_number ?? '—' }}</p>
            </div>
        </div>
    </div>
<!-- Details -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Passenger -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Passenger</h3>
            <dl class="grid grid-cols-1 gap-y-4 text-sm">
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Name</dt><dd class="mt-1 font-semibold">{{ $booking->passenger_name ?? $booking->user->full_name ?? 'Guest' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Phone</dt><dd class="mt-1 font-semibold">{{ $booking->passenger_phone ?? $booking->user->phone_number ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">ID / NRC</dt><dd class="mt-1 font-semibold">{{ $booking->passenger_id_number ?? $booking->id_number ?? '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Seat</dt><dd class="mt-1 font-semibold">{{ $booking->seat_number ?? '—' }}</dd></div>
                @if($booking->user)
                    <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Account</dt>
                        <dd class="mt-1">
                            <a href="{{ route('admin.users.show', $booking->user->id) }}" class="text-primary font-bold hover:underline">{{ $booking->user->full_name }}</a>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Trip / Route -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Trip</h3>
            <dl class="grid grid-cols-1 gap-y-4 text-sm">
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Route</dt><dd class="mt-1 font-semibold">{{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Travel Date</dt><dd class="mt-1 font-semibold">{{ $booking->route->travel_date ? $booking->route->travel_date->format('d M Y') : '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Departure</dt><dd class="mt-1 font-semibold">{{ $booking->route->departure_time ? \Carbon\Carbon::parse($booking->route->departure_time)->format('H:i') : '—' }}</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Operator</dt>
                    <dd class="mt-1">
                        @if($booking->route->operator)
                            <a href="{{ route('admin.operators.show', $booking->route->operator->id) }}" class="text-primary font-bold hover:underline">{{ $booking->route->operator->company_name }}</a>
                        @else N/A @endif
                    </dd>
                </div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Bus</dt><dd class="mt-1 font-semibold">{{ $booking->route->bus->registration_number ?? '—' }} ({{ $booking->route->bus->model ?? 'Bus' }})</dd></div>
                <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Fare</dt><dd class="mt-1 font-semibold">ZMW {{ number_format($booking->base_fare ?? $booking->route->fare ?? $booking->amount, 2) }}</dd></div>
            </dl>
        </div>

        <!-- Payment & Ticket -->
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Payment & Ticket</h3>
            @if($booking->payment)
                <dl class="grid grid-cols-1 gap-y-4 text-sm">
                    <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Payment Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $booking->payment->isSuccessful() ? 'bg-primary/10 text-primary' : 'bg-tertiary/10 text-tertiary' }}">{{ ucfirst($booking->payment->status) }}</span>
                        </dd>
                    </div>
                    <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Method</dt><dd class="mt-1 font-semibold capitalize">{{ $booking->payment->payment_method ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Paid Amount</dt><dd class="mt-1 font-semibold">ZMW {{ number_format($booking->payment->amount, 2) }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Reference</dt><dd class="mt-1 font-mono text-xs">{{ $booking->payment->transaction_reference ?? '—' }}</dd></div>
                    <div><dt class="text-xs uppercase tracking-wider text-on-surface-variant font-bold">Paid At</dt><dd class="mt-1 font-semibold">{{ $booking->payment->paid_at?->format('d M Y H:i') ?? '—' }}</dd></div>
                </dl>
            @else
                <p class="text-sm text-on-surface-variant">No payment recorded for this booking.</p>
            @endif

            @if($booking->ticket)
                <div class="mt-5 pt-4 border-t border-outline-variant/15">
                    <p class="text-xs font-bold text-primary">Ticket issued</p>
                    <p class="text-sm mt-1 font-mono">{{ $booking->ticket->ticket_number ?? $booking->reference_id }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection