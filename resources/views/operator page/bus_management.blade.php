@extends('layouts.operator')

@section('title', 'Bus Management - BookMyBus Zambia')

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
            <h1 class="font-headline font-bold text-2xl text-on-surface tracking-tight">Bus Management</h1>
            <p class="font-label text-xs text-zinc-500 uppercase tracking-widest">Manage your bus fleet</p>
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
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Buses</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-primary" id="totalBuses">0</span>
                    <span class="text-xs text-green-600 font-bold">Fleet</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Active Buses</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-secondary" id="activeBuses">0</span>
                    <span class="text-xs text-zinc-400 font-medium">Operational</span>
                </div>
            </div>
            <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-zinc-50">
                <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Capacity</p>
                <div class="flex items-baseline gap-2 mt-3">
                    <span class="text-3xl font-headline font-extrabold text-on-surface" id="totalCapacity">0</span>
                    <span class="text-xs text-zinc-400 font-medium">Seats</span>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="grid grid-cols-1 xl:grid-cols-[1.2fr_0.8fr] gap-8 items-start">
            <div class="space-y-6">
                <!-- Add Bus Form -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="font-headline font-bold text-xl text-on-surface">Add New Bus</h2>
                            <p class="text-sm text-zinc-500 mt-1">Add a new bus to your fleet.</p>
                        </div>
                        <div class="px-3 py-1.5 bg-primary/10 text-primary rounded-full text-[10px] font-black uppercase tracking-widest">Fleet Management</div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Bus Number</label>
                            <input type="text" id="busNumber" placeholder="e.g., JUL-101" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Bus Type</label>
                            <select id="busType" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="Luxury">Luxury</option>
                                <option value="Standard">Standard</option>
                                <option value="Executive">Executive</option>
                                <option value="Economy">Economy</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Capacity (Seats)</label>
                            <input type="number" id="busCapacity" placeholder="50" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Registration Plate</label>
                            <input type="text" id="busPlate" placeholder="e.g., ABZ 1234" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Status</label>
                            <select id="busStatus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <button onclick="addBus()" class="mt-6 bg-gradient-to-br from-primary to-primary-container text-white py-2.5 px-5 rounded-xl font-bold text-sm shadow-md hover:opacity-90 transition-all">Add Bus</button>
                </div>

                <!-- Bus List -->
                <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 overflow-hidden">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 px-6 py-5 border-b border-zinc-100">
                        <div>
                            <h2 class="font-headline font-bold text-xl text-on-surface">Bus Fleet</h2>
                            <p class="text-sm text-zinc-500 mt-1">Search, filter, and manage your buses.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <input type="text" id="searchInput" onkeyup="searchBuses()" placeholder="Search buses..." class="px-4 py-2 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                            <select id="filterStatus" onchange="filterBuses()" class="px-4 py-2 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                                <option value="all">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-zinc-50/70">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Bus Number</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Type</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Capacity</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Plate</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100">Status</th>
                                <th class="px-6 py-4 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100 text-right">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="busesTableBody" class="divide-y divide-zinc-50">
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-zinc-500">No buses added yet. Add your first bus above.</td>
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
                        <h3 class="font-headline font-bold text-lg text-on-surface">Fleet guidance</h3>
                        <p class="text-sm text-zinc-500">Manage your buses efficiently.</p>
                    </div>
                </div>
                <div class="rounded-xl bg-zinc-50 p-4 text-sm text-zinc-600 space-y-2">
                    <p>• Add all buses in your fleet to the system.</p>
                    <p>• Set accurate capacity for seat selection.</p>
                    <p>• Mark buses as "Maintenance" when not available.</p>
                    <p>• Only active buses appear in search results.</p>
                </div>
                <div class="rounded-xl border border-dashed border-zinc-200 p-4 text-sm text-zinc-500">
                    Tip: Assign buses to routes in Schedule Management after adding them here.
                </div>
            </div>
        </section>
    </div>
</main>

<!-- Edit Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-5">
            <h3 class="text-xl font-bold text-primary">Edit Bus</h3>
            <span onclick="closeEditModal()" class="cursor-pointer text-2xl text-zinc-500 hover:text-zinc-800">&times;</span>
        </div>
        <input type="hidden" id="editBusId">
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Bus Number</label>
            <input type="text" id="editBusNumber" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Bus Type</label>
            <select id="editBusType" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="Luxury">Luxury</option>
                <option value="Standard">Standard</option>
                <option value="Executive">Executive</option>
                <option value="Economy">Economy</option>
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Capacity</label>
            <input type="number" id="editBusCapacity" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Registration Plate</label>
            <input type="text" id="editBusPlate" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-on-surface-variant mb-1">Status</label>
            <select id="editBusStatus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>
        <div class="flex gap-3 mt-4">
            <button onclick="updateBus()" class="flex-1 bg-gradient-to-br from-primary to-primary-container text-white font-bold py-2.5 px-4 rounded-xl transition duration-200">Save Changes</button>
            <button onclick="closeEditModal()" class="flex-1 bg-zinc-200 hover:bg-zinc-300 text-zinc-800 font-bold py-2.5 px-4 rounded-xl transition duration-200">Cancel</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let buses = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    window.onload = function () {
        loadBuses();
    };

    async function loadBuses() {
        try {
            const response = await fetch('/api/buses', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load buses');
            }

            buses = await response.json();
            displayBuses();
            updateStats();
        } catch (error) {
            showAlert('Unable to load buses right now.', 'danger');
        }
    }

    function displayBuses() {
        const tbody = document.getElementById('busesTableBody');
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const filterStatus = document.getElementById('filterStatus').value;

        let filtered = buses;

        if (searchTerm) {
            filtered = filtered.filter(b =>
                b.busNumber.toLowerCase().includes(searchTerm) ||
                b.plate.toLowerCase().includes(searchTerm)
            );
        }

        if (filterStatus !== 'all') {
            filtered = filtered.filter(b => b.status === filterStatus);
        }

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-10 text-center text-zinc-500">No buses found.</td></tr>';
            return;
        }

        tbody.innerHTML = filtered.map(bus => `
            <tr class="hover:bg-zinc-50/80 transition-colors">
                <td class="px-6 py-5 font-headline font-bold text-on-surface">${bus.busNumber}</td>
                <td class="px-6 py-5 text-zinc-600">${bus.type}</td>
                <td class="px-6 py-5 text-zinc-600">${bus.capacity}</td>
                <td class="px-6 py-5 text-zinc-600">${bus.plate}</td>
                <td class="px-6 py-5">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${bus.status === 'active' ? 'bg-green-100 text-green-700' : bus.status === 'maintenance' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'}">
                        ${bus.status.charAt(0).toUpperCase() + bus.status.slice(1)}
                    </span>
                </td>
                <td class="px-6 py-5 text-right">
                    <button onclick="openEditModal(${bus.id})" class="px-3 py-1.5 bg-surface-container-high rounded-md text-[10px] font-black uppercase tracking-widest text-on-surface-variant hover:bg-primary/10 hover:text-primary transition-colors">Edit</button>
                    <button onclick="deleteBus(${bus.id})" class="ml-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-md text-[10px] font-black uppercase tracking-widest hover:bg-red-200 transition-colors">Delete</button>
                </td>
            </tr>
        `).join('');
    }

    async function addBus() {
        const busNumber = document.getElementById('busNumber').value.trim();
        const type = document.getElementById('busType').value;
        const capacity = document.getElementById('busCapacity').value;
        const plate = document.getElementById('busPlate').value.trim();
        const status = document.getElementById('busStatus').value;

        if (!busNumber) { showAlert('Please enter a bus number.', 'danger'); return; }
        if (!capacity || capacity <= 0) { showAlert('Please enter a valid capacity.', 'danger'); return; }
        if (!plate) { showAlert('Please enter a registration plate.', 'danger'); return; }

        try {
            const response = await fetch('/api/buses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    bus_number: busNumber,
                    type: type,
                    capacity: parseInt(capacity),
                    plate: plate,
                    status: status
                })
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to add bus');
            }

            document.getElementById('busNumber').value = '';
            document.getElementById('busCapacity').value = '';
            document.getElementById('busPlate').value = '';
            document.getElementById('busStatus').value = 'active';

            showAlert('Bus added successfully!', 'success');
            await loadBuses();
        } catch (error) {
            showAlert(error.message || 'Unable to add bus.', 'danger');
        }
    }

    function openEditModal(id) {
        const bus = buses.find(b => b.id === id);
        if (!bus) return;

        document.getElementById('editBusId').value = bus.id;
        document.getElementById('editBusNumber').value = bus.busNumber;
        document.getElementById('editBusType').value = bus.type;
        document.getElementById('editBusCapacity').value = bus.capacity;
        document.getElementById('editBusPlate').value = bus.plate;
        document.getElementById('editBusStatus').value = bus.status;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementId('editModal').classList.add('hidden');
    }

    async function updateBus() {
        const id = parseInt(document.getElementById('editBusId').value);
        const busNumber = document.getElementById('editBusNumber').value.trim();
        const type = document.getElementById('editBusType').value;
        const capacity = document.getElementById('editBusCapacity').value;
        const plate = document.getElementById('editBusPlate').value.trim();
        const status = document.getElementById('editBusStatus').value;

        if (!busNumber) { showAlert('Please enter a bus number.', 'danger'); return; }
        if (!capacity || capacity <= 0) { showAlert('Please enter a valid capacity.', 'danger'); return; }
        if (!plate) { showAlert('Please enter a registration plate.', 'danger'); return; }

        try {
            const response = await fetch(`/api/buses/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    bus_number: busNumber,
                    type: type,
                    capacity: parseInt(capacity),
                    plate: plate,
                    status: status
                })
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to update bus');
            }

            showAlert('Bus updated successfully!', 'success');
            closeEditModal();
            await loadBuses();
        } catch (error) {
            showAlert(error.message || 'Unable to update bus.', 'danger');
        }
    }

    async function deleteBus(id) {
        if (!confirm('Delete this bus?')) {
            return;
        }

        try {
            const response = await fetch(`/api/buses/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.message || 'Unable to delete bus');
            }

            showAlert('Bus deleted successfully.', 'success');
            await loadBuses();
        } catch (error) {
            showAlert(error.message || 'Unable to delete bus.', 'danger');
        }
    }

    function updateStats() {
        const total = buses.length;
        const active = buses.filter(b => b.status === 'active').length;
        const capacity = buses.reduce((sum, b) => sum + (b.capacity || 0), 0);

        document.getElementById('totalBuses').textContent = total;
        document.getElementById('activeBuses').textContent = active;
        document.getElementById('totalCapacity').textContent = capacity;
    }

    function searchBuses() { displayBuses(); }
    function filterBuses() { displayBuses(); }

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
