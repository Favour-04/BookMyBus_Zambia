<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Passenger List - BookMyBus Zambia</title>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              colors: {
                "on-secondary": "#ffffff",
                "on-secondary-container": "#632f00",
                "on-secondary-fixed": "#301400",
                "inverse-surface": "#2f3133",
                "on-primary-container": "#b1ffb1",
                "secondary-container": "#ff8921",
                "primary-fixed-dim": "#7edb83",
                "inverse-primary": "#7edb83",
                background: "#f9f9fc",
                "inverse-on-surface": "#f0f0f3",
                "tertiary-container": "#d1200f",
                "secondary-fixed": "#ffdcc6",
                "surface-container-lowest": "#ffffff",
                "outline-variant": "#bfcaba",
                "on-tertiary": "#ffffff",
                surface: "#f9f9fc",
                "on-primary": "#ffffff",
                "primary-fixed": "#99f89d",
                "surface-variant": "#e2e2e5",
                "secondary-fixed-dim": "#ffb784",
                "on-primary-fixed": "#002106",
                "surface-bright": "#f9f9fc",
                "on-error-container": "#93000a",
                "surface-dim": "#dadadc",
                outline: "#6f7a6c",
                "on-secondary-fixed-variant": "#713700",
                primary: "#00601f",
                "on-primary-fixed-variant": "#00531a",
                "surface-container-low": "#f3f3f6",
                "on-surface-variant": "#3f493e",
                "surface-container-high": "#e8e8ea",
                "on-surface": "#1a1c1e",
                "primary-container": "#197b30",
                error: "#ba1a1a",
                "surface-container": "#eeeef0",
                secondary: "#954a00",
                tertiary: "#a80800",
                "on-error": "#ffffff",
                "on-background": "#1a1c1e"
              },
              borderRadius: {
                DEFAULT: "0.125rem",
                lg: "0.25rem",
                xl: "0.5rem",
                full: "0.75rem"
              },
              fontFamily: {
                headline: ["Manrope"],
                body: ["Inter"],
                label: ["Inter"]
              }
            },
          },
        }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">

<!-- Navigation -->
<nav class="bg-white border-b border-zinc-100 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center">
                <a href="{{ url('/operator-dashboard') }}" class="text-xl font-extrabold text-green-900 tracking-tighter">🚌 BookMyBus Zambia</a>
            </div>
            <div class="hidden md:flex items-center gap-6">
                <a href="{{ url('/operator-dashboard') }}" class="text-sm font-medium text-zinc-600 hover:text-primary transition-colors">Dashboard</a>
                <a href="{{ url('/route-management') }}" class="text-sm font-medium text-zinc-600 hover:text-primary transition-colors">Routes</a>
                <a href="{{ url('/bus-management') }}" class="text-sm font-medium text-zinc-600 hover:text-primary transition-colors">Buses</a>
                <a href="{{ url('/fare-management') }}" class="text-sm font-medium text-zinc-600 hover:text-primary transition-colors">Fares</a>
                <a href="{{ url('/schedule-management') }}" class="text-sm font-medium text-zinc-600 hover:text-primary transition-colors">Schedules</a>
                <a href="{{ url('/booking-management') }}" class="text-sm font-medium text-zinc-600 hover:text-primary transition-colors">Bookings</a>
                <a href="{{ url('/passenger-list') }}" class="text-sm font-medium text-primary border-b-2 border-primary pb-1">Passengers</a>
                <a href="{{ url('/logout') }}" class="text-sm font-medium text-red-600 hover:text-red-700 transition-colors">Logout</a>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-medium text-zinc-600">{{ auth()->user()->name ?? 'Operator' }}</span>
                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">account_circle</span>
                </div>
            </div>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-headline font-extrabold text-on-surface">Passenger List</h1>
        <p class="text-zinc-500 mt-1">View all passengers on your buses</p>
    </div>

    <!-- Alert Messages -->
    <div id="alertMessage" class="hidden p-4 rounded-lg mb-6"></div>

    <!-- Stats -->
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-zinc-100">
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Total Passengers</p>
            <p class="text-2xl font-headline font-extrabold text-primary" id="totalPassengers">0</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-zinc-100">
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">Today's Passengers</p>
            <p class="text-2xl font-headline font-extrabold text-secondary" id="todayPassengers">0</p>
        </div>
        <div class="bg-surface-container-lowest p-4 rounded-xl shadow-sm border border-zinc-100">
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-tighter">This Week</p>
            <p class="text-2xl font-headline font-extrabold text-on-surface" id="weekPassengers">0</p>
        </div>
    </section>

    <!-- Filters -->
    <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Route</label>
                <select id="filterRoute" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option value="">All Routes</option>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->origin }} → {{ $route->destination }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Travel Date</label>
                <input type="date" id="filterDate" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Bus</label>
                <select id="filterBus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option value="">All Buses</option>
                    @foreach($buses as $bus)
                        <option value="{{ $bus->id }}">{{ $bus->bus_number }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-on-surface-variant mb-1">Status</label>
                <select id="filterStatus" class="w-full px-4 py-2.5 border border-zinc-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <option value="">All Status</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
        </div>
        <button onclick="applyFilters()" class="mt-4 bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all">Apply Filters</button>
        <button onclick="clearFilters()" class="mt-4 ml-2 bg-zinc-200 text-zinc-700 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-zinc-300 transition-all">Clear Filters</button>
        <button onclick="exportList()" class="mt-4 ml-2 bg-secondary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:opacity-90 transition-all float-right">⬇ Export List</button>
    </div>

    <!-- Passenger List Container -->
    <div id="passengerContainer">
        <!-- Passengers will be loaded here by JavaScript -->
    </div>
</main>

<!-- Passenger Details Modal -->
<div id="passengerModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-headline font-bold text-primary">🧑 Passenger Details</h3>
            <span onclick="closePassengerModal()" class="cursor-pointer text-2xl text-zinc-500 hover:text-zinc-800">&times;</span>
        </div>
        <div id="passengerDetails" class="space-y-3"></div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-zinc-50 border-t border-zinc-200 py-6 mt-10">
    <div class="max-w-7xl mx-auto px-4 text-center text-sm text-zinc-500">
        &copy; {{ date('Y') }} BookMyBus Zambia. All rights reserved.
    </div>
</footer>

<script>
    let passengers = [];
    let filteredPassengers = [];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    window.onload = function () {
        loadPassengers();
        setDefaultDate();
    };

    function setDefaultDate() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('filterDate').value = today;
    }

    async function loadPassengers() {
        try {
            const response = await fetch('/api/passengers', {
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Unable to load passengers');
            }

            passengers = await response.json();
            applyFilters();
        } catch (error) {
            showAlert('Unable to load passengers right now.', 'danger');
        }
    }

    function applyFilters() {
        const routeId = document.getElementById('filterRoute').value;
        const date = document.getElementById('filterDate').value;
        const busId = document.getElementById('filterBus').value;
        const status = document.getElementById('filterStatus').value;

        filteredPassengers = passengers.filter(p => {
            let match = true;

            if (routeId && p.routeId != routeId) match = false;
            if (date && p.travelDate != date) match = false;
            if (busId && p.busId != busId) match = false;
            if (status && p.status != status) match = false;

            return match;
        });

        displayPassengers();
        updateStats();
    }

    function clearFilters() {
        document.getElementById('filterRoute').value = '';
        document.getElementById('filterDate').value = '';
        document.getElementById('filterBus').value = '';
        document.getElementById('filterStatus').value = '';
        applyFilters();
    }

    function displayPassengers() {
        const container = document.getElementById('passengerContainer');

        if (filteredPassengers.length === 0) {
            container.innerHTML = `
                <div class="bg-surface-container-lowest p-12 rounded-2xl text-center border border-zinc-100">
                    <span class="material-symbols-outlined text-6xl text-zinc-300 mb-4">people_outline</span>
                    <h3 class="font-headline text-2xl font-bold text-on-surface mb-2">No passengers found</h3>
                    <p class="text-zinc-500">Try adjusting your filters.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = filteredPassengers.map(passenger => `
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-zinc-100 p-6 mb-4 hover:shadow-md transition-shadow">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2 flex-wrap">
                            <span class="text-lg font-headline font-extrabold text-on-surface">${passenger.passengerName}</span>
                            <span class="text-xs text-zinc-400">#${passenger.bookingId || 'BK' + passenger.id}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold ${passenger.status === 'confirmed' ? 'bg-green-100 text-green-700' : passenger.status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'}">
                                ${(passenger.status || 'confirmed').toUpperCase()}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">Route</p>
                                <p class="font-medium">${passenger.origin} → ${passenger.destination}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">Date</p>
                                <p class="font-medium">${formatDate(passenger.travelDate)}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">Time</p>
                                <p class="font-medium">${passenger.departureTime || '08:00'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">Bus</p>
                                <p class="font-medium">${passenger.busNumber || 'Bus'}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">Seats</p>
                                <p class="font-medium">${passenger.seats ? passenger.seats.join(', ') : 'N/A'}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-sm">
                            <span class="text-[10px] text-zinc-400 uppercase tracking-wider font-bold">Contact</span>
                            <span class="text-sm text-zinc-600">📞 ${passenger.phone || 'N/A'}</span>
                            <span class="text-sm text-zinc-600">📧 ${passenger.email || 'N/A'}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="viewPassenger(${passenger.id})" class="px-4 py-2 bg-primary/10 text-primary rounded-xl font-bold text-xs hover:bg-primary hover:text-white transition-colors">View Details</button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function viewPassenger(id) {
        const passenger = filteredPassengers.find(p => p.id === id);
        if (!passenger) {
            showAlert('Passenger not found.', 'danger');
            return;
        }

        const detailsHtml = `
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Name</span>
                    <span class="font-semibold">${passenger.passengerName}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Booking ID</span>
                    <span class="font-semibold">${passenger.bookingId || 'BK' + passenger.id}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Route</span>
                    <span class="font-semibold">${passenger.origin} → ${passenger.destination}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Date</span>
                    <span class="font-semibold">${formatDate(passenger.travelDate)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Time</span>
                    <span class="font-semibold">${passenger.departureTime || '08:00'}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Bus</span>
                    <span class="font-semibold">${passenger.busNumber || 'Bus'}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Seats</span>
                    <span class="font-semibold">${passenger.seats ? passenger.seats.join(', ') : 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Total Price</span>
                    <span class="font-semibold text-primary">ZMW ${passenger.totalPrice || passenger.price}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Status</span>
                    <span class="font-semibold ${passenger.status === 'confirmed' ? 'text-green-600' : passenger.status === 'cancelled' ? 'text-red-600' : 'text-yellow-600'}">
                        ${(passenger.status || 'confirmed').toUpperCase()}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Phone</span>
                    <span class="font-semibold">${passenger.phone || 'N/A'}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-zinc-400">Email</span>
                    <span class="font-semibold">${passenger.email || 'N/A'}</span>
                </div>
            </div>
        `;

        document.getElementById('passengerDetails').innerHTML = detailsHtml;
        document.getElementById('passengerModal').classList.remove('hidden');
    }

    function closePassengerModal() {
        document.getElementById('passengerModal').classList.add('hidden');
    }

    function updateStats() {
        const total = filteredPassengers.length;
        const today = new Date().toISOString().split('T')[0];
        const todayCount = filteredPassengers.filter(p => p.travelDate === today).length;

        // Week count (last 7 days)
        const weekAgo = new Date();
        weekAgo.setDate(weekAgo.getDate() - 7);
        const weekAgoStr = weekAgo.toISOString().split('T')[0];
        const weekCount = filteredPassengers.filter(p => p.travelDate >= weekAgoStr).length;

        document.getElementById('totalPassengers').textContent = total;
        document.getElementById('todayPassengers').textContent = todayCount;
        document.getElementById('weekPassengers').textContent = weekCount;
    }

    function exportList() {
        if (filteredPassengers.length === 0) {
            showAlert('No passengers to export.', 'danger');
            return;
        }

        // Create CSV
        let csv = 'Name,Booking ID,Route,Date,Time,Seats,Status,Phone,Email\n';
        filteredPassengers.forEach(p => {
            csv += `${p.passengerName},${p.bookingId || 'BK' + p.id},${p.origin} → ${p.destination},${p.travelDate},${p.departureTime || '08:00'},"${p.seats ? p.seats.join(', ') : 'N/A'}",${p.status || 'confirmed'},${p.phone || 'N/A'},${p.email || 'N/A'}\n`;
        });

        // Download
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'passenger-list.csv';
        a.click();
        window.URL.revokeObjectURL(url);

        showAlert('Passenger list exported successfully!', 'success');
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-GB', {
            weekday: 'short',
            day: '2-digit',
            month: 'short',
            year: 'numeric'
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
        const modal = document.getElementById('passengerModal');
        if (event.target === modal) {
            closePassengerModal();
        }
    };
</script>
</body>
</html>
