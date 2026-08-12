@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <!-- KPI Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider"><a href="{{ route('admin.users.index') }}" class="hover:text-primary hover:underline">Travelers</a></p>
                <span class="material-symbols-outlined text-primary text-2xl">group</span>
            </div>
            <a href="{{ route('admin.users.index') }}" class="font-headline font-extrabold text-3xl hover:text-primary hover:underline">{{ number_format($stats['total_users']) }}</a>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Operators</p>
                <span class="material-symbols-outlined text-primary text-2xl">directions_bus</span>
            </div>
            <p class="font-headline font-extrabold text-3xl">{{ number_format($stats['total_operators']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Revenue</p>
                <span class="material-symbols-outlined text-primary text-2xl">payments</span>
            </div>
            <p class="font-headline font-extrabold text-2xl leading-tight">ZMW {{ number_format($stats['total_revenue']) }}</p>
            <p class="text-xs font-bold mt-1 {{ str_starts_with($stats['revenue_trend'], '+') ? 'text-primary' : 'text-tertiary' }}">{{ $stats['revenue_trend'] }} mo/mo</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Bookings</p>
                <span class="material-symbols-outlined text-primary text-2xl">book_online</span>
            </div>
            <p class="font-headline font-extrabold text-3xl">{{ number_format($stats['total_bookings']) }}</p>
            <p class="text-xs font-bold mt-1 {{ str_starts_with($stats['bookings_trend'], '+') ? 'text-primary' : 'text-tertiary' }}">{{ $stats['bookings_trend'] }} mo/mo</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Avg. Booking</p>
                <span class="material-symbols-outlined text-primary text-2xl">receipt_long</span>
            </div>
            <p class="font-headline font-extrabold text-2xl leading-tight">ZMW {{ number_format($stats['avg_booking_value']) }}</p>
            <p class="text-xs font-bold mt-1 text-on-surface-variant">{{ number_format($stats['confirmed_bookings']) }} confirmed</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Pending Verif.</p>
                <span class="material-symbols-outlined text-2xl {{ $stats['pending_verifications'] > 0 ? 'text-tertiary' : 'text-primary' }}">verified_user</span>
            </div>
            <a href="{{ route('admin.operators.index', ['status' => 'pending']) }}" class="font-headline font-extrabold text-3xl {{ $stats['pending_verifications'] > 0 ? 'text-tertiary hover:underline' : '' }}">{{ number_format($stats['pending_verifications']) }}</a>
            <p class="text-xs font-bold mt-1 text-on-surface-variant">{{ number_format($stats['total_buses']) }} buses · {{ number_format($stats['total_routes']) }} routes</p>
        </div>
    </div>
<!-- Charts Row -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        <div class="xl:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Revenue — Last 30 Days</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Bookings — Last 30 Days</h3>
            <canvas id="bookingsChart" height="100"></canvas>
        </div>
        <div class="xl:col-span-3 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                <h3 class="font-headline font-bold text-lg mb-4">Booking Status</h3>
                <canvas id="statusChart" height="90"></canvas>
            </div>
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                <h3 class="font-headline font-bold text-lg mb-4">Payment Channels</h3>
                @forelse($payment_channels as $channel)
                    <div class="flex items-center justify-between py-2 border-b border-outline-variant/10 last:border-0">
                        <span class="capitalize text-sm">{{ $channel->channel }}</span>
                        <span class="text-sm font-bold">{{ number_format($channel->total) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-on-surface-variant">No successful payments yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Operators -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-headline font-bold text-lg">Top Operators by Revenue</h3>
            <a href="{{ route('admin.operators.index') }}" class="text-sm font-bold text-primary hover:underline">All operators →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20">
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">#</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Company</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Fleet</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Bookings</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Revenue</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($top_operators as $index => $operator)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-2 text-on-surface-variant">{{ $index + 1 }}</td>
                        <td class="py-3 px-2 font-bold">{{ $operator->company_name }}</td>
                        <td class="py-3 px-2">{{ $operator->bus_count }}</td>
                        <td class="py-3 px-2">{{ $operator->total_bookings }}</td>
                        <td class="py-3 px-2 font-bold">ZMW {{ number_format($operator->revenue, 2) }}</td>
                        <td class="py-3 px-2">
                            @if($operator->is_verified)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Verified</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Pending</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-6 text-center text-on-surface-variant">No revenue recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
<!-- Recent Operators -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-8">
        <h3 class="font-headline font-bold text-lg mb-4">Recent Operators</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20">
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Company</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Email</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Joined</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_operators as $operator)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-2 font-bold">{{ $operator->company_name ?? $operator->name ?? 'N/A' }}</td>
                        <td class="py-3 px-2">{{ $operator->email }}</td>
                        <td class="py-3 px-2">
                            @if($operator->is_verified)
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Verified</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Pending</span>
                            @endif
                        </td>
                        <td class="py-3 px-2 text-on-surface-variant">{{ $operator->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-6 text-center text-on-surface-variant">No operators registered yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">New Travelers</h3>
            <div class="space-y-3">
                @forelse($recent_travelers as $traveler)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center text-primary text-sm font-bold">{{ strtoupper(substr($traveler->full_name ?? 'T', 0, 1)) }}</div>
                        <div>
                            <p class="text-sm font-bold">{{ $traveler->full_name ?? 'Traveler' }}</p>
                            <p class="text-xs text-on-surface-variant">{{ $traveler->email }}</p>
                        </div>
                    </div>
                    <span class="text-xs text-on-surface-variant">{{ $traveler->created_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-sm text-on-surface-variant">No travelers yet.</p>
                @endforelse
            </div>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Recent Verifications</h3>
            <div class="space-y-3">
                @forelse($recent_verifications as $operator)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold">{{ $operator->company_name ?? $operator->name ?? 'Operator' }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $operator->email }}</p>
                    </div>
                    <span class="text-xs font-bold text-primary">{{ $operator->verified_at->diffForHumans() }}</span>
                </div>
                @empty
                <p class="text-sm text-on-surface-variant">No operator verifications yet.</p>
                @endforelse
            </div>
        </div>
    </div>
<!-- Recent Bookings -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
        <h3 class="font-headline font-bold text-lg mb-4">
                <a href="{{ route('admin.bookings.index') }}" class="hover:text-primary hover:underline">Recent Bookings →</a>
            </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-outline-variant/20">
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Reference</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Passenger</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Route</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Amount</th>
                        <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_bookings as $booking)
                    <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                        <td class="py-3 px-2 font-mono text-xs">{{ $booking->reference_id }}</td>
                        <td class="py-3 px-2">{{ $booking->passenger_name ?? $booking->user->full_name ?? 'Guest' }}</td>
                        <td class="py-3 px-2">{{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}</td>
                        <td class="py-3 px-2 font-bold">ZMW {{ number_format($booking->amount, 2) }}</td>
                        <td class="py-3 px-2">
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
                    </tr>
                    @empty
                    <tr><td colspan="5" class="py-6 text-center text-on-surface-variant">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const labels       = @json($chartLabels);
    const revenueData  = @json($revenueChartData);
    const bookingsData = @json($bookingsChartData);

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{ label: 'Revenue (ZMW)', data: revenueData, backgroundColor: 'rgba(25, 123, 48, 0.75)', borderRadius: 4 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { ticks: { callback: (v) => 'ZMW ' + new Intl.NumberFormat().format(v) } } }
        }
    });

    new Chart(document.getElementById('bookingsChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{ label: 'Bookings', data: bookingsData, borderColor: '#00601f', backgroundColor: 'rgba(0, 96, 31, 0.12)', fill: true, tension: 0.35, pointRadius: 2 }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
        }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json(array_column($status_breakdown, 'label')),
            datasets: [{ data: @json(array_column($status_breakdown, 'count')), backgroundColor: @json(array_column($status_breakdown, 'color')), borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush