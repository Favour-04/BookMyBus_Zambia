@extends('layouts.app')

@section('title', 'System Reports - BookMyBus Zambia')

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>
@endpush

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-headline font-extrabold text-on-surface">System Reports</h1>
        <p class="text-zinc-500 mt-1">Overview of platform performance and key metrics</p>
    </div>

    <!-- Alert Messages -->
    <div id="alertMessage" class="hidden p-4 rounded-lg mb-6"></div>

    <!-- Date Range Filter -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Date From</label>
                <input type="date" id="filterDateFrom" class="px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Date To</label>
                <input type="date" id="filterDateTo" class="px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Period</label>
                <select id="filterPeriod" class="px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            <button onclick="applyFilters()" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all">Apply</button>
            <button onclick="refreshReports()" class="bg-primary/10 text-primary px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-primary/20 transition-all">🔄 Refresh</button>
            <button onclick="exportReports()" class="bg-secondary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all ml-auto">⬇ Export Report</button>
        </div>
    </div>

    <!-- Stats Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-zinc-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Users</p>
                    <p class="text-3xl font-headline font-extrabold text-primary" id="totalUsers">0</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-primary/30">people</span>
            </div>
            <p class="text-xs text-zinc-400 mt-1">+<span id="userGrowth">0</span> this period</p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-zinc-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Operators</p>
                    <p class="text-3xl font-headline font-extrabold text-secondary" id="totalOperators">0</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-secondary/30">business</span>
            </div>
            <p class="text-xs text-zinc-400 mt-1">+<span id="operatorGrowth">0</span> this period</p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-zinc-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Bookings</p>
                    <p class="text-3xl font-headline font-extrabold text-primary-container" id="totalBookings">0</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-primary-container/30">receipt_long</span>
            </div>
            <p class="text-xs text-zinc-400 mt-1">+<span id="bookingGrowth">0</span> this period</p>
        </div>
        <div class="bg-surface-container-lowest p-5 rounded-xl shadow-sm border border-zinc-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Revenue</p>
                    <p class="text-3xl font-headline font-extrabold text-on-surface" id="totalRevenue">ZMW 0</p>
                </div>
                <span class="material-symbols-outlined text-4xl text-zinc-300">payments</span>
            </div>
            <p class="text-xs text-zinc-400 mt-1">+<span id="revenueGrowth">0</span> this period</p>
        </div>
    </section>

    <!-- Revenue Chart -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-headline font-bold text-xl text-on-surface">📊 Revenue Overview</h2>
            <span class="text-xs text-zinc-400">Last 12 months</span>
        </div>
        <div class="h-[200px] flex items-end gap-2 pt-5" id="revenueChart"></div>
        <div class="flex justify-between mt-2 text-xs text-zinc-400" id="revenueLabels"></div>
    </div>

    <!-- Booking Trends -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-headline font-bold text-xl text-on-surface">📈 Booking Trends</h2>
            <span class="text-xs text-zinc-400">Last 12 months</span>
        </div>
        <div class="h-[200px] flex items-end gap-2 pt-5" id="bookingChart"></div>
        <div class="flex justify-between mt-2 text-xs text-zinc-400" id="bookingLabels"></div>
    </div>

    <!-- Popular Routes & Top Operators -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Popular Routes -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6">
            <h2 class="font-headline font-bold text-xl text-on-surface mb-4">🔥 Popular Routes</h2>
            <div id="popularRoutes"></div>
        </div>
        <!-- Top Operators -->
        <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6">
            <h2 class="font-headline font-bold text-xl text-on-surface mb-4">🏆 Top Operators</h2>
            <div id="topOperators"></div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    let reportData = {};
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    window.onload = function () {
        loadReports();
        setDefaultDates();
    };

    function setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        document.getElementById('filterDateFrom').value = firstDay.toISOString().split('T')[0];
        document.getElementById('filterDateTo').value = today.toISOString().split('T')[0];
    }

    async function loadReports() {
        try {
            const response = await fetch('/api/system-reports', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load reports');
            }

            reportData = await response.json();
            displayReports();
        } catch (error) {
            showAlert('Unable to load system reports. Please try again.', 'danger');
        }
    }

    function displayReports() {
        // Stats
        document.getElementById('totalUsers').textContent = reportData.totalUsers || 0;
        document.getElementById('userGrowth').textContent = reportData.userGrowth || 0;
        document.getElementById('totalOperators').textContent = reportData.totalOperators || 0;
        document.getElementById('operatorGrowth').textContent = reportData.operatorGrowth || 0;
        document.getElementById('totalBookings').textContent = reportData.totalBookings || 0;
        document.getElementById('bookingGrowth').textContent = reportData.bookingGrowth || 0;
        document.getElementById('totalRevenue').textContent = `ZMW ${(reportData.totalRevenue || 0).toLocaleString()}`;
        document.getElementById('revenueGrowth').textContent = reportData.revenueGrowth || 0;

        // Revenue Chart
        renderChart('revenueChart', 'revenueLabels', reportData.revenueData || [], 'ZMW', '#00601f');

        // Booking Chart
        renderChart('bookingChart', 'bookingLabels', reportData.bookingData || [], '', '#954a00');

        // Popular Routes
        displayPopularRoutes();

        // Top Operators
        displayTopOperators();
    }

    function renderChart(chartId, labelId, data, prefix, color) {
        const max = Math.max(...data, 1);
        const container = document.getElementById(chartId);
        const labelContainer = document.getElementById(labelId);

        if (!container) return;

        const currentMonth = new Date().getMonth();
        const labels = [];
        for (let i = 11; i >= 0; i--) {
            const idx = (currentMonth - i + 12) % 12;
            labels.push(months[idx]);
        }

        container.innerHTML = data.map((value, index) => {
            const height = Math.max((value / max) * 150, 10);
            const tooltip = `${prefix || ''}${value.toLocaleString()}`;
            return `
                <div class="group flex-1 rounded-t-lg relative hover:opacity-80 hover:scale-y-[1.02] hover:origin-bottom min-h-2 transition-[height] duration-500 ease-out" style="height: ${height}px; background: ${color};">
                    <div class="invisible group-hover:visible absolute -top-7 left-1/2 -translate-x-1/2 bg-gray-800 text-white px-2 py-1 rounded text-[10px] whitespace-nowrap">${tooltip}</div>
                </div>
            `;
        }).join('');

        if (labelContainer) {
            labelContainer.innerHTML = labels.map(label => `<span>${label}</span>`).join('');
        }
    }

    function displayPopularRoutes() {
        const container = document.getElementById('popularRoutes');
        const routes = reportData.popularRoutes || [];

        if (routes.length === 0) {
            container.innerHTML = '<p class="text-zinc-500 text-sm">No route data available.</p>';
            return;
        }

        container.innerHTML = routes.map((route, index) => `
            <div class="flex items-center gap-3 py-2 border-b border-zinc-50">
                <span class="text-xs font-bold text-zinc-400 w-6">${index + 1}</span>
                <div class="flex-1">
                    <p class="font-medium text-sm">${route.route}</p>
                    <div class="flex items-center gap-2">
                        <div class="flex-1 h-1.5 bg-zinc-100 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: ${route.percentage}%;"></div>
                        </div>
                        <span class="text-xs font-bold text-zinc-500">${route.percentage}%</span>
                    </div>
                </div>
                <span class="text-xs text-zinc-400">${route.bookings} bookings</span>
            </div>
        `).join('');
    }

    function displayTopOperators() {
        const container = document.getElementById('topOperators');
        const operators = reportData.topOperators || [];

        if (operators.length === 0) {
            container.innerHTML = '<p class="text-zinc-500 text-sm">No operator data available.</p>';
            return;
        }

        container.innerHTML = operators.map((operator, index) => `
            <div class="flex items-center gap-3 py-2 border-b border-zinc-50">
                <span class="text-xs font-bold text-zinc-400 w-6">${index + 1}</span>
                <div class="flex-1">
                    <p class="font-medium text-sm">${operator.name}</p>
                    <div class="flex items-center gap-2 text-xs text-zinc-500">
                        <span>📋 ${operator.bookings} bookings</span>
                        <span>💰 ZMW ${operator.revenue.toLocaleString()}</span>
                    </div>
                </div>
                <span class="text-xs font-bold text-primary">🏆</span>
            </div>
        `).join('');
    }

    function applyFilters() {
        showAlert('Filters applied successfully!', 'success');
        loadReports();
    }

    function refreshReports() {
        showAlert('Refreshing reports...', 'success');
        loadReports();
    }

    function exportReports() {
        const data = {
            generatedAt: new Date().toISOString(),
            reportData: reportData
        };

        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `system-report-${new Date().toISOString().split('T')[0]}.json`;
        a.click();
        window.URL.revokeObjectURL(url);

        showAlert('Report exported successfully!', 'success');
    }

    function showAlert(message, type) {
        const alertDiv = document.getElementById('alertMessage');
        alertDiv.classList.remove('hidden');
        alertDiv.className = `p-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700 border-l-4 border-green-500' : 'bg-red-100 text-red-700 border-l-4 border-red-500'}`;
        alertDiv.textContent = message;
        setTimeout(() => alertDiv.classList.add('hidden'), 3000);
    }
</script>
@endpush
