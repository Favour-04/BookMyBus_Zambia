@extends('layouts.admin')

@section('title', 'Bookings')
@section('page_title', 'System-Wide Bookings')

@section('content')
    <!-- Summary strip -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Bookings</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Confirmed</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-primary">{{ number_format($stats['confirmed']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Pending</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-tertiary">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Cancelled</p>
            <p class="font-headline font-extrabold text-3xl mt-2 text-error">{{ number_format($stats['cancelled']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Revenue</p>
            <p class="font-headline font-extrabold text-2xl mt-2 leading-tight">ZMW {{ number_format($stats['revenue'], 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div class="inline-flex rounded-xl border border-outline-variant/20 bg-surface-container-lowest p-1 flex-wrap" role="group" aria-label="Filter bookings by status">
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request('status', 'all') === 'all' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">All</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'confirmed']) }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request('status') === 'confirmed' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">Confirmed</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request('status') === 'pending' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">Pending</a>
            <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" class="px-4 py-2 rounded-lg text-sm font-bold {{ request('status') === 'cancelled' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container-high' }}">Cancelled</a>
        </div>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:flex gap-2" role="search" aria-label="Filter bookings">
            <label for="bk-operator" class="sr-only">Filter by operator</label>
            <select id="bk-operator" name="operator_id" class="rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="">All operators</option>
                @foreach($operators as $operator)
                    <option value="{{ $operator->id }}" {{ request('operator_id') == $operator->id ? 'selected' : '' }}>{{ $operator->company_name }}</option>
                @endforeach
            </select>
            <label for="bk-date" class="sr-only">Filter by date range</label>
            <select id="bk-date" name="date" class="rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-3 py-2 text-sm focus:outline-none focus:border-primary">
                <option value="">Any date</option>
                <option value="today" {{ request('date') === 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ request('date') === 'week' ? 'selected' : '' }}>This week</option>
                <option value="month" {{ request('date') === 'month' ? 'selected' : '' }}>This month</option>
            </select>
            <label for="bk-search" class="sr-only">Search bookings by reference, passenger, or seat</label>
            <input id="bk-search" type="text" name="search" value="{{ request('search') }}" placeholder="Reference, passenger, seat..."
                class="rounded-xl border border-outline-variant/30 bg-surface-container-lowest px-4 py-2 text-sm focus:outline-none focus:border-primary">
            <button type="submit" class="flex items-center gap-1 px-4 py-2 rounded-xl bg-primary text-white text-sm font-bold">
                <span class="material-symbols-outlined text-base">filter_alt</span>Filter
            </button>
            @if(request('status') || request('operator_id') || request('date') || request('search'))
                <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-3 py-2 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant">Clear</a>
            @endif
        </form>
    </div>
<!-- Bookings table -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Reference</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Passenger</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Route</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Operator</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Amount</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-4 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Booked</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-4">
                            <a href="{{ route('admin.bookings.show', $booking->id) }}" class="font-mono text-xs font-bold hover:text-primary">{{ $booking->reference_id }}</a>
                            <span class="block text-[10px] text-on-surface-variant">Seat {{ $booking->seat_number ?? '—' }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <p class="font-medium">{{ $booking->passenger_name ?? $booking->user->full_name ?? 'Guest' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $booking->passenger_phone ?? $booking->user->phone_number ?? '' }}</p>
                        </td>
                        <td class="py-3 px-4">{{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}</td>
                        <td class="py-3 px-4">
                            <p class="text-xs">{{ $booking->route->operator->company_name ?? 'N/A' }}</p>
                        </td>
                        <td class="py-3 px-4 font-bold">ZMW {{ number_format($booking->amount, 2) }}</td>
                        <td class="py-3 px-4">
                            @php
                                $badgeClass = match($booking->status) {
                                    'confirmed' => 'bg-primary/10 text-primary',
                                    'pending' => 'bg-tertiary/10 text-tertiary',
                                    'cancelled' => 'bg-error/10 text-error',
                                    default => 'bg-surface-container-high text-on-surface-variant',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="py-3 px-4 text-xs text-on-surface-variant">{{ $booking->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-10 text-center text-on-surface-variant">No bookings found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-outline-variant/15 flex flex-wrap items-center justify-between gap-2">
                <span class="text-sm text-on-surface-variant">Showing {{ $bookings->firstItem() ?? 0 }}–{{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }}</span>
                {{ $bookings->links() }}
            </div>
    </div>
@endsection