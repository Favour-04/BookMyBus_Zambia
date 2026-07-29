@extends('layouts.operator')

@section('title', 'Schedule Management - BookMyBus Zambia')

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
<main class="min-h-screen">
    <!-- Header -->
    <div class="h-20 flex items-center justify-between px-8 bg-surface-container-lowest sticky top-0 z-30 shadow-sm border-b border-zinc-100">
        <div>
            <h1 class="font-headline font-bold text-2xl text-on-surface tracking-tight">Schedule Management</h1>
            <p class="font-label text-xs text-zinc-500 uppercase tracking-widest">Assign buses to routes & manage departure times</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-4 py-2 bg-surface-container-low rounded-full border border-outline-variant/15">
                <span class="material-symbols-outlined text-zinc-400 text-sm">calendar_today</span>
                <span class="text-sm font-medium text-on-surface-variant">{{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    <div class="p-8 max-w-7xl mx-auto space-y-8">
        <div id="alertMessage" class="hidden p-4 rounded-lg"></div>

        <!-- Stats Cards -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Schedules</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-primary" id="totalSchedules">0</span>
                    <span class="text-xs text-green-600 font-bold">Active</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Routes Covered</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-secondary" id="routesCovered">0</span>
                    <span class="text-xs text-zinc-400 font-medium">Unique routes</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Buses Assigned</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-on-surface" id="busesAssigned">0</span>
                    <span class="text-xs text-zinc-400 font-medium">In operation</span>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-8 items-start">
            <div class="space-y-6">
                <!-- Add Schedule Form -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="font-headline font-bold text-xl text-on-surface">Create a Schedule</h2>
                            <p class="text-sm text-zinc-500 mt-1">Assign a bus to a route with departure time.</p>
                        </div>
                        <div class="px-3 py-1.5 bg-primary/10 text-primary rounded-full text-[10px] font-black uppercase tracking-widest">Schedule Management</div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Route</label>
                            <select id="scheduleRoute" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="">Select a route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->origin }} → {{ $route->destination }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Bus</label>
                            <select id="scheduleBus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="">Select a bus</option>
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}">{{ $bus->bus_number }} ({{ $bus->capacity }} seats)</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Departure Time</label>
                            <input type="time" id="departureTime" value="08:00" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Travel Date</label>
                            <input type="date" id="travelDate" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Days of Week</label>
                            <select id="scheduleDays" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="Monday-Friday">Monday - Friday</option>
                                <option value="Weekdays">Monday - Friday</option>
                                <option value="Weekends">Saturday - Sunday</option>
                                <option value="Daily">Daily</option>
                                <option value="Custom">Custom</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Status</label>
                            <select id="scheduleStatus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <button onclick="addSchedule()" class="mt-6 bg-gradient-to-br from-primary to-primary-container text-white py-2.5 px-5 rounded-xl font-bold text-sm shadow-md hover:opacity-90 transition-all">Create Schedule</button>
                </div>

                <!-- Schedule List -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 overflow-hidden">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 border-b border-zinc-100">
                        <div>
                            <h2 class="font-headline font-bold text-xl text-on-surface">Schedules</h2>
                            <p class="text-sm text-zinc-500 mt-1">View, search, and manage all schedules.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="searchInput" onkeyup="searchSchedules()" placeholder="Search schedules..." class="px-4 py-2 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                            <select id="filterStatus" onchange="filterSchedules()" class="px-4 py-2 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-zinc-50/70">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Route</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Bus</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Departure</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Date</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="schedulesTableBody" class="divide-y divide-zinc-50">
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-zinc-500">No schedules added yet. Create your first schedule above.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Guidance -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">info</span>
                    </div>
                    <div>
                        <h3 class="font-headline font-bold text-lg text-on-surface">Schedule guidance</h3>
                        <p class="text-sm text-zinc-500">Optimize your bus operations.</p>
                    </div>
                </div>
                <div class="rounded-xl bg-zinc-50 p-4 text-sm text-zinc-600 space-y-2">
                    <p>• Assign buses to routes with specific departure times.</p>
                    <p>• Set travel dates for when the schedule runs.</p>
                    <p>• Mark schedules as inactive when on break.</p>
                    <p>• Only active schedules appear in search results.</p>
                </div>
                <div class="rounded-xl border border-dashed border-zinc-200 p-4 text-sm text-zinc-500">
                    Tip: Create one schedule per bus per route for effective fleet management.
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-bold text-primary">Edit Schedule</h3>
            <span onclick="closeEditModal()" class="cursor-pointer text-2xl text-zinc-500 hover:text-zinc-800">&times;</span>
        </div>
        <input type="hidden" id="editScheduleId">
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Route</label>
            <select id="editScheduleRoute" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                @foreach($routes as $route)
                    <option value="{{ $route->id }}">{{ $route->origin }} → {{ $route->destination }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Bus</label>
            <select id="editScheduleBus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                @foreach($buses as $bus)
                    <option value="{{ $bus->id }}">{{ $bus->bus_number }} ({{ $bus->capacity }} seats)</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Departure Time</label>
            <input type="time" id="editDepartureTime" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Travel Date</label>
            <input type="date" id="editTravelDate" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Days</label>
            <select id="editScheduleDays" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="Monday-Friday">Monday - Friday</option>
                <option value="Weekends">Saturday - Sunday</option>
                <option value="Daily">Daily</option>
                <option value="Custom">Custom</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Status</label>
            <select id="editScheduleStatus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <div class="flex gap-3 mt-4">
            <button onclick="updateSchedule()" class="flex-1 bg-gradient-to-br from-primary to-primary-container text-white font-bold py-2.5 px-4 rounded-xl transition duration-200">Save Changes</button>
            <button onclick="closeEditModal()" class="flex-1 bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-bold py-2.5 px-4 rounded-xl transition duration-200">Cancel</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let schedules = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    window.onload = function () {
        loadSchedules();
        // Set default date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const dateInput = document.getElementById('travelDate');
        if (dateInput) {
            dateInput.value = tomorrow.toISOString().split('T')[0];
        }
    };

    async function loadSchedules() {
        try {
            const response = await fetch('/api/schedules', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load schedules');
            }

            schedules = await response.json();
            displaySchedules();
            updateStats();
        } catch (error) {
            showAlert('Unable to load schedules right now.', 'danger');
        }
    }

    function displaySchedules() {
        const tbody = document.getElementById('schedulesTableBody');
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value;

        let filtered = schedules;

        if (searchTerm) {
            filtered = filtered.filter(s =>
                s.routeName.toLowerCase().includes(searchTerm) ||
                s.busNumber.toLowerCase().includes(searchTerm)
            );
        }

        if (filterStatus !== 'all') {
            filtered = filtered.filter(s => s.status === filterStatus);
        }

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-zinc-500">No schedules found.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(schedule => `
            <tr class="hover:bg-zinc-50/80 transition-colors">
                <td class="px-6 py-5 font-headline font-bold text-on-surface">${schedule.routeName}</td>
                <td class="px-6 py-5 text-zinc-600">${schedule.busNumber}</td>
                <td class="px-6 py-5 text-zinc-600">${schedule.departureTime}</td>
                <td class="px-6 py-5 text-zinc-600">${schedule.travelDate ? new Date(schedule.travelDate).toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }) : '-'}</td>
                <td class="px-6 py-5">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${schedule.status === 'active' ? 'bg-green-100 text-green-700' : schedule.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}">
                        ${schedule.status.charAt(0).toUpperCase() + schedule.status.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-5 text-right">
                    <button onclick="openEditModal(${schedule.id})" class="px-3 py-1.5 bg-surface-container-high rounded-md text-[10px] font-black uppercase tracking-widest text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-colors">Edit</button>
                    <button onclick="deleteSchedule(${schedule.id})" class="ml-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-md text-[10px] font-black uppercase tracking-widest hover:bg-red-200 transition-colors">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    async function addSchedule() {
        const routeId = document.getElementById('scheduleRoute').value;
        const busId = document.getElementById('scheduleBus').value;
        const departureTime = document.getElementById('departureTime').value;
        const travelDate = document.getElementById('travelDate').value;
        const days = document.getElementById('scheduleDays').value;
        const status = document.getElementById('scheduleStatus').value;

        if (!routeId) { showAlert('Please select a route.', 'danger'); return; }
        if (!busId) { showAlert('Please select a bus.', 'danger'); return; }
        if (!departureTime) { showAlert('Please enter a departure time.', 'danger'); return; }
        if (!travelDate) { showAlert('Please select a travel date.', 'danger'); return; }

        try {
            const response = await fetch('/api/schedules', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    route_id: routeId,
                    bus_id: busId,
                    departure_time: departureTime,
                    travel_date: travelDate,
                    days: days,
                    status: status
                })
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to create schedule');
            }

            document.getElementById('scheduleRoute').value = '';
            document.getElementById('scheduleBus').value = '';
            document.getElementById('departureTime').value = '08:00';
            document.getElementById('scheduleStatus').value = 'active';

            // Reset date to tomorrow
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 1);
            document.getElementById('travelDate').value = tomorrow.toISOString().split('T')[0];

            showAlert('Schedule created successfully!', 'success');
            await loadSchedules();
        } catch (error) {
            showAlert(error.message || 'Unable to create schedule.', 'danger');
        }
    }

    function openEditModal(id) {
        const schedule = schedules.find(s => s.id === id);
        if (!schedule) return;

        document.getElementById('editScheduleId').value = schedule.id;
        document.getElementById('editScheduleRoute').value = schedule.routeId;
        document.getElementById('editScheduleBus').value = schedule.busId;
        document.getElementById('editDepartureTime').value = schedule.departureTime;
        document.getElementById('editTravelDate').value = schedule.travelDate;
        document.getElementById('editScheduleDays').value = schedule.days || 'Daily';
        document.getElementById('editScheduleStatus').value = schedule.status;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    async function updateSchedule() {
        const id = parseInt(document.getElementById('editScheduleId').value);
        const routeId = document.getElementById('editScheduleRoute').value;
        const busId = document.getElementById('editScheduleBus').value;
        const departureTime = document.getElementById('editDepartureTime').value;
        const travelDate = document.getElementById('editTravelDate').value;
        const days = document.getElementById('editScheduleDays').value;
        const status = document.getElementById('editScheduleStatus').value;

        if (!routeId) { showAlert('Please select a route.', 'danger'); return; }
        if (!busId) { showAlert('Please select a bus.', 'danger'); return; }
        if (!departureTime) { showAlert('Please enter a departure time.', 'danger'); return; }
        if (!travelDate) { showAlert('Please select a travel date.', 'danger'); return; }

        try {
            const response = await fetch(`/api/schedules/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    route_id: routeId,
                    bus_id: busId,
                    departure_time: departureTime,
                    travel_date: travelDate,
                    days: days,
                    status: status
                })
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to update schedule');
            }

            showAlert('Schedule updated successfully!', 'success');
            closeEditModal();
            await loadSchedules();
        } catch (error) {
            showAlert(error.message || 'Unable to update schedule.', 'danger');
        }
    }

    async function deleteSchedule(id) {
        if (!confirm('Delete this schedule?')) {
            return;
        }

        try {
            const response = await fetch(`/api/schedules/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to delete schedule');
            }

            showAlert('Schedule deleted successfully.', 'success');
            await loadSchedules();
        } catch (error) {
            showAlert(error.message || 'Unable to delete schedule.', 'danger');
        }
    }

    function updateStats() {
        const total = schedules.length;
        const active = schedules.filter(s => s.status === 'active').length;
        const uniqueRoutes = new Set(schedules.map(s => s.routeId)).size;
        const uniqueBuses = new Set(schedules.map(s => s.busId)).size;

        document.getElementById('totalSchedules').textContent = total;
        document.getElementById('routesCovered').textContent = uniqueRoutes;
        document.getElementById('busesAssigned').textContent = uniqueBuses;
    }

    function searchSchedules() { displaySchedules(); }
    function filterSchedules() { displaySchedules(); }

    function showAlert(message, type) {
        const alertDiv = document.getElementById('alertMessage');
        alertDiv.classList.remove('hidden');
        alertDiv.className = `p-4 rounded-lg ${type === 'success' ? 'bg-green-100 text-green-700 border-l-4 border-green-500' : 'bg-red-100 text-red-700 border-l-4 border-red-500'}`;
        alertDiv.textContent = message;
        setTimeout(() => alertDiv.classList.add('hidden'), 3000);
    }

    window.onclick = function (event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            closeEditModal();
        }
    };
</script>
@endpush
