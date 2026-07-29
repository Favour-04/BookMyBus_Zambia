@extends('layouts.app')

@section('title', 'Audit Logs - BookMyBus Zambia')

@push('styles')
<style>
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
    body { font-family: 'Inter', sans-serif; }
    h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
</style>
@endpush

@section('content')
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-headline font-extrabold text-on-surface">Audit Logs</h1>
        <p class="text-zinc-500 mt-1">Track all system activities and user actions</p>
    </div>

    <!-- Alert Messages -->
    <div id="alertMessage" class="hidden p-4 rounded-lg mb-6"></div>

    <!-- Stats -->
    <section class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-zinc-100">
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Activities</p>
            <p class="text-2xl font-headline font-extrabold text-primary" id="totalActivities">0</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-zinc-100">
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Today's Activities</p>
            <p class="text-2xl font-headline font-extrabold text-secondary" id="todayActivities">0</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-zinc-100">
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">This Week</p>
            <p class="text-2xl font-headline font-extrabold text-on-surface" id="weekActivities">0</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-zinc-100">
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Unique Users</p>
            <p class="text-2xl font-headline font-extrabold text-primary-container" id="uniqueUsers">0</p>
        </div>
    </section>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">User</label>
                <input type="text" id="filterUser" placeholder="Search user..." class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Action Type</label>
                <select id="filterAction" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option value="">All Actions</option>
                    <option value="Login">Login</option>
                    <option value="Logout">Logout</option>
                    <option value="Booked Ticket">Booked Ticket</option>
                    <option value="Cancelled Booking">Cancelled Booking</option>
                    <option value="Added Route">Added Route</option>
                    <option value="Updated Route">Updated Route</option>
                    <option value="Deleted Route">Deleted Route</option>
                    <option value="Added Fare">Added Fare</option>
                    <option value="Updated Fare">Updated Fare</option>
                    <option value="Deleted Fare">Deleted Fare</option>
                    <option value="Added Bus">Added Bus</option>
                    <option value="Updated Bus">Updated Bus</option>
                    <option value="Deleted Bus">Deleted Bus</option>
                    <option value="Created Schedule">Created Schedule</option>
                    <option value="Updated Schedule">Updated Schedule</option>
                    <option value="Deleted Schedule">Deleted Schedule</option>
                    <option value="Verified Operator">Verified Operator</option>
                    <option value="Rejected Operator">Rejected Operator</option>
                    <option value="Deleted User">Deleted User</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Date From</label>
                <input type="date" id="filterDateFrom" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Date To</label>
                <input type="date" id="filterDateTo" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
        </div>
        <button onclick="applyFilters()" class="mt-4 bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all">Apply Filters</button>
        <button onclick="clearFilters()" class="mt-4 ml-2 bg-zinc-200 text-zinc-700 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-zinc-300 transition-all">Clear Filters</button>
        <button onclick="exportLogs()" class="mt-4 ml-2 bg-secondary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all float-right">⬇ Export Logs</button>
        <button onclick="refreshLogs()" class="mt-4 mr-2 bg-primary/10 text-primary px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-primary/20 transition-all float-right">🔄 Refresh</button>
    </div>

    <!-- Logs Container -->
    <div id="logsContainer">
        <!-- Logs will be loaded here by JavaScript -->
    </div>

    <!-- Log Details Modal -->
    <div id="logModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-headline font-bold text-primary">📋 Log Details</h3>
                <span onclick="closeLogModal()" class="cursor-pointer text-2xl text-zinc-500 hover:text-zinc-800">&times;</span>
            </div>
            <div id="logDetails" class="space-y-3"></div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    let logs = [];
    let filteredLogs = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    window.onload = function () {
        loadLogs();
    };

    async function loadLogs() {
        try {
            const response = await fetch('/api/audit-logs', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load audit logs');
            }

            logs = await response.json();
            applyFilters();
        } catch (error) {
            showAlert('Unable to load audit logs. Please try again.', 'danger');
        }
    }

    function applyFilters() {
        const user = document.getElementById('filterUser').value.toLowerCase();
        const action = document.getElementById('filterAction').value;
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;

        filteredLogs = logs.filter(log => {
            let match = true;

            if (user && !log.userName.toLowerCase().includes(user)) match = false;
            if (action && log.action !== action) match = false;
            if (dateFrom && log.createdAt < dateFrom) match = false;
            if (dateTo && log.createdAt > dateTo + ' 23:59:59') match = false;

            return match;
        });

        displayLogs();
        updateStats();
    }

    function clearFilters() {
        document.getElementById('filterUser').value = '';
        document.getElementById('filterAction').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        applyFilters();
    }

    function displayLogs() {
        const container = document.getElementById('logsContainer');

        if (filteredLogs.length === 0) {
            container.innerHTML = `
                <div class="bg-surface-container-lowest p-12 rounded-2xl text-center border border-zinc-100">
                    <span class="material-symbols-outlined text-6xl text-zinc-300 mb-4">history</span>
                    <h3 class="font-headline text-2xl font-bold text-on-surface mb-2">No logs found</h3>
                    <p class="text-zinc-500">Try adjusting your filters.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = filteredLogs.map(log => `
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-4 mb-3 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="font-headline font-bold text-on-surface">${log.userName}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${log.userRole === 'Admin' ? 'bg-purple-100 text-purple-700' : log.userRole === 'Operator' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'}">
                                ${log.userRole}
                            </span>
                            <span class="text-sm font-medium text-zinc-600">${log.action}</span>
                            <span class="text-xs text-zinc-400">${log.details || ''}</span>
                        </div>
                        <div class="flex items-center gap-4 mt-1 text-xs text-zinc-400">
                            <span>📅 ${formatDateTime(log.createdAt)}</span>
                            <span>🌐 ${log.ipAddress || 'N/A'}</span>
                        </div>
                    </div>
                    <button onclick="viewLog(${log.id})" class="px-4 py-2 bg-primary/10 text-primary rounded-xl font-bold text-xs hover:bg-primary hover:text-white transition-colors">View Details</button>
                </div>
            </div>
        `).join('');
    }

    function viewLog(id) {
        const log = filteredLogs.find(l => l.id === id);
        if (!log) {
            showAlert('Log not found.', 'danger');
            return;
        }

        const detailsHtml = `
            <div class="space-y-3">
                <div class="flex justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs text-zinc-400">User</span>
                    <span class="font-semibold">${log.userName}</span>
                </div>
                <div class="flex justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs text-zinc-400">Role</span>
                    <span class="font-semibold">${log.userRole}</span>
                </div>
                <div class="flex justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs text-zinc-400">Action</span>
                    <span class="font-semibold">${log.action}</span>
                </div>
                <div class="flex justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs text-zinc-400">Details</span>
                    <span class="font-semibold">${log.details || 'N/A'}</span>
                </div>
                <div class="flex justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs text-zinc-400">Date & Time</span>
                    <span class="font-semibold">${formatDateTime(log.createdAt)}</span>
                </div>
                <div class="flex justify-between border-b border-zinc-100 pb-2">
                    <span class="text-xs text-zinc-400">IP Address</span>
                    <span class="font-semibold">${log.ipAddress || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Log ID</span>
                    <span class="font-semibold">#${log.id}</span>
                </div>
            </div>
        `;

        document.getElementById('logDetails').innerHTML = detailsHtml;
        document.getElementById('logModal').classList.remove('hidden');
    }

    function closeLogModal() {
        document.getElementById('logModal').classList.add('hidden');
    }

    function updateStats() {
        const total = filteredLogs.length;
        const today = new Date().toISOString().split('T')[0];
        const todayCount = filteredLogs.filter(l => l.createdAt.startsWith(today)).length;

        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        const weekAgoStr = weekAgo.toISOString().split('T')[0];
        const weekCount = filteredLogs.filter(l => l.createdAt >= weekAgoStr).length;

        const uniqueUsers = new Set(filteredLogs.map(l => l.userName)).size;

        document.getElementById('totalActivities').textContent = total;
        document.getElementById('todayActivities').textContent = todayCount;
        document.getElementById('weekActivities').textContent = weekCount;
        document.getElementById('uniqueUsers').textContent = uniqueUsers;
    }

    function exportLogs() {
        if (filteredLogs.length === 0) {
            showAlert('No logs to export.', 'danger');
            return;
        }

        let csv = 'User,Role,Action,Details,Date & Time,IP Address\n';
        filteredLogs.forEach(l => {
            csv += `"${l.userName}","${l.userRole}","${l.action}","${l.details || ''}","${l.createdAt}","${l.ipAddress || 'N/A'}"\n`;
        });

        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'audit-logs.csv';
        a.click();
        window.URL.revokeObjectURL(url);

        showAlert('Logs exported successfully!', 'success');
    }

    function refreshLogs() {
        showAlert('Refreshing logs...', 'success');
        loadLogs();
    }

    function formatDateTime(dateTime) {
        const date = new Date(dateTime);
        return date.toLocaleString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function showAlert(message, type) {
        const alertDiv = document.getElementById('alertMessage');
        alertDiv.classList.remove('hidden');
        alertDiv.className = `p-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700 border-l-4 border-green-500' : 'bg-red-100 text-red-700 border-l-4 border-red-500'}`;
        alertDiv.textContent = message;
        setTimeout(() => alertDiv.classList.add('hidden'), 3000);
    }

    window.onclick = function (event) {
        const modal = document.getElementById('logModal');
        if (event.target === modal) {
            closeLogModal();
        }
    };
</script>
@endpush
