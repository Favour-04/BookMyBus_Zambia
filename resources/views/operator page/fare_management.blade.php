@extends('layouts.operator')

@section('title', 'Fare Management - BookMyBus Zambia')

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
            <h1 class="font-headline font-bold text-2xl text-on-surface tracking-tight">Fare Management</h1>
            <p class="font-label text-xs text-zinc-500 uppercase tracking-widest">Route pricing & fare control</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 px-4 py-2 bg-surface-container-low rounded-full border border-outline-variant/15">
                <span class="material-symbols-outlined text-zinc-400 text-sm">calendar_today</span>
                <span class="text-sm font-medium text-on-surface-variant">{{ now()->format('M dd, Y') }}</span>
            </div>
        </div>
    </div>

    <div class="p-8 max-w-7xl mx-auto space-y-8">
        <div id="alertMessage" class="hidden p-4 rounded-lg"></div>

        <!-- Stats Cards -->
        <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Fares</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-primary" id="totalFares">0</span>
                    <span class="text-xs text-green-600 font-bold">Live</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Active Fares</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-secondary" id="activeFares">0</span>
                    <span class="text-xs text-zinc-400 font-medium">Enabled routes</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Cheapest Fare</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-on-surface" id="cheapestFare">ZMW 0</span>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-8 items-start">
            <div class="space-y-6">
                <!-- Add Fare Form -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="font-headline font-bold text-xl text-on-surface">Create or update a fare</h2>
                            <p class="text-sm text-zinc-500 mt-1">Set pricing for each route and keep it visible to travellers.</p>
                        </div>
                        <div class="px-3 py-1.5 bg-primary/10 text-primary rounded-full text-[10px] font-black uppercase tracking-widest">Route Pricing</div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Route</label>
                            <select id="fareRoute" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="">Select a route</option>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->origin }} → {{ $route->destination }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Price (ZMW)</label>
                            <input type="number" id="farePrice" placeholder="150" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Departure Time</label>
                            <input type="time" id="departureTime" value="08:00" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Status</label>
                            <select id="fareStatus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <button onclick="addFare()" class="mt-6 bg-gradient-to-br from-primary to-primary-container text-white py-2.5 px-5 rounded-xl font-bold text-sm shadow-md hover:opacity-90 transition-all">Add Fare</button>
                </div>

                <!-- Fare List -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 overflow-hidden">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 border-b border-zinc-100">
                        <div>
                            <h2 class="font-headline font-bold text-xl text-on-surface">Fare list</h2>
                            <p class="text-sm text-zinc-500 mt-1">Search, filter, and edit saved fares.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="searchInput" onkeyup="searchFares()" placeholder="Search fares..." class="px-4 py-2 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                            <select id="filterStatus" onchange="filterFares()" class="px-4 py-2 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-zinc-50/70">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Route</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Price</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Departure</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="faresTableBody" class="divide-y divide-zinc-50">
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-zinc-500">No fares added yet. Add your first fare above.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pricing Guidance -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">info</span>
                    </div>
                    <div>
                        <h3 class="font-headline font-bold text-lg text-on-surface">Pricing guidance</h3>
                        <p class="text-sm text-zinc-500">Keep fares competitive and consistent.</p>
                    </div>
                </div>
                <div class="rounded-xl bg-zinc-50 p-4 text-sm text-zinc-600 space-y-2">
                    <p>• Review fares for peak and off-peak travel.</p>
                    <p>• Turn off fares instantly when a route is paused.</p>
                    <p>• Keep the cheapest fare visible for customer comparison.</p>
                </div>
                <div class="rounded-xl border border-dashed border-zinc-200 p-4 text-sm text-zinc-500">
                    Tip: use the same route names across the booking flow for clearer passenger experience.
                </div>
            </div>
        </section>
    </div>
</main>

@push('scripts')
<script>
    let fares = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    window.onload = function () {
        loadFares();
    };

    async function loadFares() {
        try {
            const response = await fetch('/api/fares', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load fares');
            }

            fares = await response.json();
            displayFares();
            updateStats();
        } catch (error) {
            showAlert('Unable to load fares right now.', 'danger');
        }
    }

    function displayFares() {
        const tbody = document.getElementById('faresTableBody');
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value;

        let filtered = fares;

        if (searchTerm) {
            filtered = filtered.filter(f => f.routeName.toLowerCase().includes(searchTerm));
        }

        if (filterStatus !== 'all') {
            filtered = filtered.filter(f => f.status === filterStatus);
        }

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-10 text-center text-zinc-500">No fares found.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(fare => `
            <tr class="hover:bg-zinc-50/80 transition-colors">
                <td class="px-6 py-5 font-headline font-bold text-on-surface">${fare.routeName}</td>
                <td class="px-6 py-5 font-headline font-extrabold text-primary">ZMW ${fare.price}</td>
                <td class="px-6 py-5 text-zinc-600">${fare.departureTime || '08:00'}</td>
                <td class="px-6 py-5">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${fare.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                        ${fare.status === 'active' ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-6 py-5 text-right">
                    <button onclick="openEditModal(${fare.id})" class="px-3 py-1.5 bg-surface-container-high rounded-md text-[10px] font-black uppercase tracking-widest text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-colors">Edit</button>
                    <button onclick="deleteFare(${fare.id})" class="ml-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-md text-[10px] font-black uppercase tracking-widest hover:bg-red-200 transition-colors">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    async function addFare() {
        const routeId = document.getElementById('fareRoute').value;
        const price = document.getElementById('farePrice').value;
        const departureTime = document.getElementById('departureTime').value;
        const status = document.getElementById('fareStatus').value;

        if (!routeId) { showAlert('Please select a route.', 'danger'); return; }
        if (!price || price <= 0) { showAlert('Please enter a valid price.', 'danger'); return; }

        try {
            const response = await fetch('/api/fares', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    route_id: routeId,
                    price: parseFloat(price),
                    departure_time: departureTime || '08:00',
                    status
                })
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to add fare');
            }

            document.getElementById('fareRoute').value = '';
            document.getElementById('farePrice').value = '';
            document.getElementById('departureTime').value = '08:00';
            document.getElementById('fareStatus').value = 'active';

            showAlert('Fare added successfully!', 'success');
            await loadFares();
        } catch (error) {
            showAlert(error.message || 'Unable to add fare.', 'danger');
        }
    }

    function openEditModal(id) {
        const fare = fares.find(f => f.id === id);
        if (!fare) return;

        document.getElementById('editFareId').value = fare.id;
        document.getElementById('editRoute').value = fare.routeId;
        document.getElementById('editPrice').value = fare.price;
        document.getElementById('editDepartureTime').value = fare.departureTime || '08:00';
        document.getElementById('editStatus').value = fare.status;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    async function updateFare() {
        const id = parseInt(document.getElementById('editFareId').value);
        const routeId = document.getElementById('editRoute').value;
        const price = document.getElementById('editPrice').value;
        const departureTime = document.getElementById('editDepartureTime').value;
        const status = document.getElementById('editStatus').value;

        if (!routeId) { showAlert('Please select a route.', 'danger'); return; }
        if (!price || price <= 0) { showAlert('Please enter a valid price.', 'danger'); return; }

        try {
            const response = await fetch(`/api/fares/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    route_id: routeId,
                    price: parseFloat(price),
                    departure_time: departureTime || '08:00',
                    status
                })
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to update fare');
            }

            showAlert('Fare updated successfully!', 'success');
            closeEditModal();
            await loadFares();
        } catch (error) {
            showAlert(error.message || 'Unable to update fare.', 'danger');
        }
    }

    async function deleteFare(id) {
        if (!confirm('Delete this fare?')) {
            return;
        }

        try {
            const response = await fetch(`/api/fares/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to delete fare');
            }

            showAlert('Fare deleted successfully.', 'success');
            await loadFares();
        } catch (error) {
            showAlert(error.message || 'Unable to delete fare.', 'danger');
        }
    }

    function updateStats() {
        const total = fares.length;
        const active = fares.filter(f => f.status === 'active').length;
        document.getElementById('totalFares').textContent = total;
        document.getElementById('activeFares').textContent = active;

        const activePrices = fares.filter(f => f.status === 'active').map(f => f.price);
        document.getElementById('cheapestFare').textContent = activePrices.length > 0 ? `ZMW ${Math.min(...activePrices)}` : 'ZMW 0';
    }

    function searchFares() { displayFares(); }
    function filterFares() { displayFares(); }

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
