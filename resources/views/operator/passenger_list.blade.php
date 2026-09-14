@extends('layouts.operator')

@section('title', 'Passenger List')
@section('page_title', 'Passenger List')

@push('head')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
</style>
@endpush

@section('content')


            <!-- Status messages -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-primary/10 border border-primary/20 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
                    <p class="text-on-surface text-sm font-medium">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($stats['total']) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Passengers</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ $stats['todayCount'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Today</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ $stats['boarded'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Boarded</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-tertiary">{{ $stats['notBoarded'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Not Boarded</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Trip filter -->
                    <select name="trip_id" onchange="window.location=buildUrl('trip_id', this.value)"
                            class="pl-3 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-sm text-on-surface-variant">
                        <option value="">All Trips</option>
                        @foreach($trips as $trip)
                        <option value="{{ $trip['id'] }}" {{ request('trip_id') == $trip['id'] ? 'selected' : '' }}>{{ $trip['label'] }}</option>
                        @endforeach
                    </select>

                    <!-- Status filter -->
                    <select name="status" onchange="window.location=buildUrl('status', this.value)"
                            class="pl-3 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-sm text-on-surface-variant">
                        <option value="">All Statuses</option>
                        @foreach(['confirmed' => 'Confirmed', 'pending' => 'Pending', 'cancelled' => 'Cancelled'] as $val => $label)
                        <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <!-- Boarded filter -->
                    <select name="boarded" onchange="window.location=buildUrl('boarded', this.value)"
                            class="pl-3 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-sm text-on-surface-variant">
                        <option value="">All Boarding</option>
                        <option value="yes" {{ request('boarded') === 'yes' ? 'selected' : '' }}>Boarded</option>
                        <option value="no" {{ request('boarded') === 'no' ? 'selected' : '' }}>Not Boarded</option>
                    </select>

                    <!-- Date from -->
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           onchange="window.location=buildUrl('date_from', this.value)"
                           class="px-3 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-sm text-on-surface-variant" placeholder="From">

                    <!-- Date to -->
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           onchange="window.location=buildUrl('date_to', this.value)"
                           class="px-3 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-sm text-on-surface-variant" placeholder="To">
                </div>

                <form method="GET" action="{{ route('operator.passengers.index') }}" class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, ref, seat..."
                           class="pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-full w-72 text-sm focus:ring-2 focus:ring-primary transition-all">
                </form>
            </div>

            <!-- Bulk Check-in Bar -->
            <form id="bulk-checkin-form" method="POST" action="{{ route('operator.passengers.bulk-checkin') }}" class="mb-4">
                @csrf
                <div class="flex items-center gap-3 bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-3">
                    <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Bulk Check-in:</span>
                    <button type="submit" onclick="return confirm('Mark selected passengers as boarded?')"
                        class="px-4 py-1.5 bg-primary text-on-primary rounded-lg text-xs font-bold hover:bg-primary/90 transition-colors">
                        Check In Selected
                    </button>
                    <span class="text-xs text-on-surface-variant ml-auto">
                        <span id="selected-count">0</span> selected
                    </span>
                </div>
            </form>

            <!-- Passenger Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 w-10">
                                    <input type="checkbox" id="select-all" class="rounded border-outline-variant/50 text-primary focus:ring-primary">
                                </th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant cursor-pointer hover:text-primary"
                                    onclick="window.location=buildUrl('sort', 'seat_number')">
                                    Seat @if(request('sort') === 'seat_number')<span class="material-symbols-outlined text-sm align-middle">arrow_drop_{{ request('dir', 'asc') === 'asc' ? 'up' : 'down' }}</span>@endif
                                </th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant cursor-pointer hover:text-primary"
                                    onclick="window.location=buildUrl('sort', 'passenger_name')">
                                    Passenger @if(request('sort') === 'passenger_name')<span class="material-symbols-outlined text-sm align-middle">arrow_drop_{{ request('dir', 'asc') === 'asc' ? 'up' : 'down' }}</span>@endif
                                </th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Phone</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Trip</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Date</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Reference</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Status</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Boarded</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($passengers as $booking)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4">
                                    @if($booking->status === 'confirmed' && !$booking->isBoarded())
                                    <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}"
                                        class="passenger-checkbox rounded border-outline-variant/50 text-primary focus:ring-primary">
                                    @endif
                                </td>
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
                                    <div class="flex items-center gap-1.5 font-medium text-sm">
                                        {{ $booking->route->origin }}
                                        <span class="material-symbols-outlined text-xs text-outline">arrow_forward</span>
                                        {{ $booking->route->destination }}
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    @if($booking->route->travel_date instanceof \Carbon\Carbon)
                                        {{ $booking->route->travel_date->format('d M') }}
                                    @else
                                        {{ \Carbon\Carbon::parse($booking->route->travel_date)->format('d M') }}
                                    @endif
                                    <span class="block text-xs text-on-surface-variant">{{ $booking->route->departure_time }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-mono text-sm font-bold">{{ $booking->reference_id }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $statusStyles[$booking->status] ?? 'bg-surface-container text-on-surface-variant' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($booking->isBoarded())
                                        <span class="flex items-center gap-1 text-primary font-bold text-xs">
                                            <span class="material-symbols-outlined text-sm">check_circle</span>
                                            Boarded
                                        </span>
                                    @else
                                        <span class="text-on-surface-variant text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('operator.bookings.show', $booking->id) }}"
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-block"
                                            title="View details">
                                            <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
                                        </a>
                                        <a href="{{ route('operator.trips.bookings', $booking->route_id) }}"
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-block"
                                            title="View trip bookings">
                                            <span class="material-symbols-outlined" style="font-size:18px">directions_bus</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">group</span>
                                    <p class="font-medium text-on-surface-variant">No passengers found matching your filters.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-sm text-on-surface-variant">
                        Showing {{ $passengers->firstItem() ?? 0 }} - {{ $passengers->lastItem() ?? 0 }} of {{ $passengers->total() }} passengers
                    </span>
                    <div class="flex items-center gap-2">
                        @if($passengers->previousPageUrl())
                        <a href="{{ $passengers->previousPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </a>
                        @endif
                        <span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-sm font-bold">{{ $passengers->currentPage() }}</span>
                        @if($passengers->nextPageUrl())
                        <a href="{{ $passengers->nextPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <script>
                // Build URL with query parameter
                function buildUrl(key, value) {
                    const url = new URL(window.location.href);
                    if (value) {
                        url.searchParams.set(key, value);
                    } else {
                        url.searchParams.delete(key);
                    }
                    url.searchParams.delete('page');
                    return url.toString();
                }

                // Select all / deselect all checkboxes
                document.getElementById('select-all').addEventListener('change', function() {
                    document.querySelectorAll('.passenger-checkbox').forEach(cb => cb.checked = this.checked);
                    updateSelectedCount();
                });
                document.querySelectorAll('.passenger-checkbox').forEach(cb => {
                    cb.addEventListener('change', updateSelectedCount);
                });
                function updateSelectedCount() {
                    const count = document.querySelectorAll('.passenger-checkbox:checked').length;
                    document.getElementById('selected-count').textContent = count;
                }
            </script>
@endsection
