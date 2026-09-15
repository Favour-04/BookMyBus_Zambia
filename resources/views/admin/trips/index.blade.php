@extends('layouts.admin')

@section('title', 'Trips')
@section('page_title', 'System-Wide Trips')

@section('content')
    <!-- Summary strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Trips</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Today</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['today']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Delayed</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-tertiary">{{ number_format($stats['delayed']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Active</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-primary">{{ number_format($stats['active']) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4 mb-6">
        <form method="GET" class="grid grid-cols-2 md:grid-cols-5 gap-3" role="search" aria-label="Filter trips">
            <label for="trip-operator" class="sr-only">Filter by operator</label>
            <select id="trip-operator" name="operator_id" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="">All operators</option>
                @foreach($operators as $operator)
                    <option value="{{ $operator->id }}" {{ request('operator_id') == $operator->id ? 'selected' : '' }}>{{ $operator->company_name }}</option>
                @endforeach
            </select>
            <label for="trip-date" class="sr-only">Filter by travel date</label>
            <input id="trip-date" type="date" name="date" value="{{ request('date') }}" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <label for="trip-status" class="sr-only">Filter by trip status</label>
            <select id="trip-status" name="status" class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="">All statuses</option>
                @foreach(['scheduled', 'delayed', 'departed', 'arrived', 'inactive'] as $st)
                    <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                @endforeach
            </select>
            <label for="trip-search" class="sr-only">Search trips by origin or destination</label>
            <input id="trip-search" type="text" name="search" value="{{ request('search') }}" placeholder="Origin / destination..."
                class="rounded-xl border border-outline-variant/30 px-3 py-2 text-sm focus:outline-none focus:border-primary">
            <div class="flex gap-2 col-span-2 md:col-span-1">
                <button type="submit" class="flex-1 flex items-center justify-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold">
                    <span class="material-symbols-outlined text-base">filter_alt</span>Filter
                </button>
                @if(request('operator_id') || request('date') || request('status') || request('search'))
                    <a href="{{ route('admin.trips.index') }}" class="flex items-center px-3 py-2 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Clear</a>
                @endif
            </div>
        </form>
    </div>
<!-- Trips table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Route</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Operator</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Bus</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Date</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Departure</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Fare</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trips as $trip)
                        @php
                            $tripStatus = 'scheduled';
                            if ($trip->arrived_at) $tripStatus = 'arrived';
                            elseif ($trip->departed_at) $tripStatus = 'departed';
                            elseif ($trip->delayed_at) $tripStatus = 'delayed';
                            elseif (!$trip->is_active) $tripStatus = 'inactive';
                            $tripBadge = match($tripStatus) {
                                'arrived' => 'bg-primary/10 text-primary',
                                'departed' => 'bg-surface-container-high text-on-surface-variant',
                                'delayed' => 'bg-tertiary/10 text-tertiary',
                                'inactive' => 'bg-error/10 text-error',
                                default => 'bg-surface-container-high text-on-surface-variant',
                            };
                        @endphp
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-4 font-bold">{{ $trip->origin }} → {{ $trip->destination }}</td>
                        <td class="py-3 px-4 text-xs">{{ $trip->operator->company_name ?? 'N/A' }}</td>
                        <td class="py-3 px-4 text-xs">{{ $trip->bus->registration_number ?? '—' }}</td>
                        <td class="py-3 px-4 text-xs">{{ $trip->travel_date?->format('d M Y') ?? '—' }}</td>
                        <td class="py-3 px-4 text-xs">{{ $trip->departure_time ? \Carbon\Carbon::parse($trip->departure_time)->format('H:i') : '—' }}</td>
                        <td class="py-3 px-4 font-bold">ZMW {{ number_format($trip->fare, 2) }}</td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $tripBadge }}">{{ ucfirst($tripStatus) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10 text-center text-on-surface-variant">No trips found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-outline-variant/15 flex flex-wrap items-center justify-between gap-2">
            <span class="text-sm text-on-surface-variant">Showing {{ $trips->firstItem() ?? 0 }}–{{ $trips->lastItem() ?? 0 }} of {{ $trips->total() }}</span>
            {{ $trips->links() }}
        </div>
    </div>
@endsection