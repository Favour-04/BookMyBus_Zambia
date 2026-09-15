@extends('layouts.operator')

@section('title', 'All Bookings')
@section('page_title', 'All Bookings')

@push('head')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
</style>
@endpush

@section('header_actions')
                <a href="{{ route('operator.bookings.export') }}?{{ http_build_query(request()->only(['status', 'date_filter', 'search'])) }}"
                   class="flex items-center gap-2 px-4 py-2 border border-outline-variant/30 rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-sm">download</span> Export
                </a>
@endsection

@section('content')


            <!-- Stats Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($stats['total_bookings']) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Bookings</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ $stats['today_bookings'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Today</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ $stats['confirmed_bookings'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Confirmed</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-tertiary">{{ $stats['pending_bookings'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Pending</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold">ZMW {{ number_format($stats['revenue_today'], 2) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Revenue Today</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1">
                        @php $currentStatus = request('status', ''); @endphp
                        @foreach(['' => 'All', 'confirmed' => 'Confirmed', 'pending' => 'Pending', 'cancelled' => 'Cancelled', 'expired' => 'Expired'] as $val => $label)
                        <a href="{{ route('operator.bookings.index', array_merge(request()->except('status', 'page'), ['status' => $val ?: null])) }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-bold transition-all
                                  {{ ($currentStatus === $val) ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                    <select name="date_filter" onchange="window.location=this.value"
                            class="pl-3 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-sm text-on-surface-variant">
                        <option value="{{ route('operator.bookings.index', request()->except('date_filter', 'page')) }}">All dates</option>
                        @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', 'tomorrow' => 'Tomorrow', 'this_week' => 'This Week', 'this_month' => 'This Month'] as $val => $label)
                        <option value="{{ route('operator.bookings.index', array_merge(request()->except('date_filter', 'page'), ['date_filter' => $val])) }}"
                            {{ request('date_filter') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <form method="GET" action="{{ route('operator.bookings.index') }}" class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by ref, name, phone, seat..."
                           class="pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-full w-72 text-sm focus:ring-2 focus:ring-primary transition-all">
                </form>
            </div>

            <!-- Bulk Actions Bar -->
            <form id="bulk-action-form" method="POST" action="{{ route('operator.bookings.bulk-action') }}" class="mb-4">
                @csrf
                <div class="flex items-center gap-3 bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-3">
                    <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Bulk Actions:</span>
                    <select name="action" class="px-3 py-1.5 bg-surface-container-low border border-outline-variant/30 rounded-lg text-sm text-on-surface-variant">
                        <option value="">Select action...</option>
                        <option value="board">Mark as Boarded</option>
                        <option value="cancel">Cancel Selected</option>
                    </select>
                    <button type="submit" onclick="return confirm('Apply bulk action to selected bookings?')"
                        class="px-4 py-1.5 bg-primary text-on-primary rounded-lg text-xs font-bold hover:bg-primary/90 transition-colors">
                        Apply
                    </button>
                    <span class="text-xs text-on-surface-variant ml-auto">
                        <span id="selected-count">0</span> selected
                    </span>
                </div>
            </form>

            <!-- Bookings Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 w-10">
                                    <input type="checkbox" id="select-all" class="rounded border-outline-variant/50 text-primary focus:ring-primary">
                                </th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Ref ID</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Passenger</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Route</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Date</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Seat</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Amount</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Status</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($bookings as $booking)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4">
                                    <input type="checkbox" name="booking_ids[]" value="{{ $booking->id }}"
                                        class="booking-checkbox rounded border-outline-variant/50 text-primary focus:ring-primary">
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-mono text-sm font-bold">{{ $booking->reference_id }}</span>
                                    @if($booking->isBoarded())
                                        <span class="ml-1.5 px-1.5 py-0.5 rounded bg-primary/10 text-primary text-[10px] font-bold uppercase">Boarded</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium">{{ $booking->passenger_name ?? 'N/A' }}</span>
                                    @if($booking->phone_number)
                                        <span class="block text-xs text-on-surface-variant">{{ $booking->phone_number }}</span>
                                    @endif
                                </td>
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
                                    <span class="font-bold">#{{ $booking->seat_number }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold">ZMW {{ number_format($booking->amount, 2) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                        {{ $statusStyles[$booking->status] ?? 'bg-surface-container text-on-surface-variant' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('operator.bookings.show', $booking->id) }}"
                                            class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-block"
                                            title="View booking details">
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
                                <td colspan="9" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">book_online</span>
                                    <p class="font-medium text-on-surface-variant">No bookings found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-sm text-on-surface-variant">
                        Showing {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} of {{ $bookings->total() }} bookings
                    </span>
                    <div class="flex items-center gap-1">
                        @if($bookings->previousPageUrl())
                        <a href="{{ $bookings->previousPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </a>
                        @endif
                        <span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-sm font-bold">{{ $bookings->currentPage() }}</span>
                        @if($bookings->nextPageUrl())
                        <a href="{{ $bookings->nextPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

@endsection

@push('scripts')
<script>
                // Select all / deselect all checkboxes
                document.getElementById('select-all').addEventListener('change', function() {
                    document.querySelectorAll('.booking-checkbox').forEach(cb => cb.checked = this.checked);
                    updateSelectedCount();
                });
                document.querySelectorAll('.booking-checkbox').forEach(cb => {
                    cb.addEventListener('change', updateSelectedCount);
                });
                function updateSelectedCount() {
                    const count = document.querySelectorAll('.booking-checkbox:checked').length;
                    document.getElementById('selected-count').textContent = count;
                }
            </script>
@endpush

