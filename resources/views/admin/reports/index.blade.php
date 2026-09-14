@extends('layouts.admin')

@section('title', 'Reports & Analytics')
@section('page_title', 'Reports & Analytics')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
    <!-- KPI -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Collected Revenue</p>
            <p class="font-headline font-extrabold text-3xl mt-2">ZMW {{ number_format($totals['revenue'], 2) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Bookings</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($totals['bookings']) }}</p>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Registered Operators</p>
            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($totals['operators']) }}</p>
        </div>
    </div>

    <!-- Trend charts -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-headline font-bold text-lg">Revenue — Last 12 Months</h3>
                <a href="{{ route('admin.reports.export') }}" class="flex items-center gap-1 text-sm font-bold text-primary hover:underline">
                    <span class="material-symbols-outlined text-base">download</span>Export CSV
                </a>
            </div>
            <canvas id="revenueChart" height="90" role="img" aria-label="Bar chart of collected revenue in ZMW for each of the last 12 months. Total over the period: ZMW {{ number_format($totals['revenue'], 2) }}."></canvas>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Bookings — Last 12 Months</h3>
            <canvas id="bookingsChart" height="90" role="img" aria-label="Line chart of booking counts for each of the last 12 months. Total over the period: {{ number_format($totals['bookings']) }} bookings."></canvas>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Payment Methods</h3>
            <canvas id="methodChart" height="90" role="img" aria-label="Doughnut chart of successful payments split by payment method: {{ $paymentMethods->map(fn($v,$k) => $k . ' (' . $v . ')')->join(', ') }}."></canvas>
        </div>
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Booking Status</h3>
            <canvas id="statusChart" height="90" role="img" aria-label="Doughnut chart of booking status: {{ $statusBreakdown['confirmed'] }} confirmed, {{ $statusBreakdown['pending'] }} pending, {{ $statusBreakdown['cancelled'] }} cancelled."></canvas>
        </div>
    </div>
<!-- Revenue tables -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Revenue by Operator</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">#</th>
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Operator</th>
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Txns</th>
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByOperator as $i => $row)
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                            <td class="py-2 px-2 text-on-surface-variant">{{ $i + 1 }}</td>
                            <td class="py-2 px-2 font-bold"><a href="{{ route('admin.operators.show', $row->id) }}" class="hover:text-primary hover:underline">{{ $row->company_name }}</a></td>
                            <td class="py-2 px-2">{{ number_format($row->tx_count) }}</td>
                            <td class="py-2 px-2 font-bold">ZMW {{ number_format($row->revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 text-center text-on-surface-variant">No successful payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
            <h3 class="font-headline font-bold text-lg mb-4">Top Routes by Revenue</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-outline-variant/20 bg-surface-container-low">
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">#</th>
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Route</th>
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Txns</th>
                            <th class="text-left py-2 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByRoute as $i => $row)
                        <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                            <td class="py-2 px-2 text-on-surface-variant">{{ $i + 1 }}</td>
                            <td class="py-2 px-2 font-bold">{{ $row->origin }} → {{ $row->destination }}</td>
                            <td class="py-2 px-2">{{ number_format($row->tx_count) }}</td>
                            <td class="py-2 px-2 font-bold">ZMW {{ number_format($row->revenue, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="py-6 text-center text-on-surface-variant">No successful payments yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{ label: 'Revenue (ZMW)', data: @json($revenueData), backgroundColor: 'rgba(25, 123, 48, 0.75)', borderRadius: 4 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { ticks: { callback: (v) => 'ZMW ' + new Intl.NumberFormat().format(v) } } }
        }
    });

    new Chart(document.getElementById('bookingsChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{ label: 'Bookings', data: @json($bookingsData), borderColor: '#00601f', backgroundColor: 'rgba(0, 96, 31, 0.12)', fill: true, tension: 0.35, pointRadius: 2 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
    });

    new Chart(document.getElementById('methodChart'), {
        type: 'doughnut',
        data: {
            labels: @json($paymentMethods->keys()),
            datasets: [{ data: @json($paymentMethods->values()), borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Confirmed', 'Pending', 'Cancelled'],
            datasets: [{ data: [{{ $statusBreakdown['confirmed'] }}, {{ $statusBreakdown['pending'] }}, {{ $statusBreakdown['cancelled'] }}], backgroundColor: ['#197b30', '#c26400', '#ba1a1a'], borderWidth: 0 }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });
</script>
@endpush