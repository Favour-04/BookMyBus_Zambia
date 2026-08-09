<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $bus->registration_number }} | Bus Details | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#004614", "on-primary": "#ffffff",
                        "surface-container-low": "#f3f3f6", "surface-container": "#edeef1",
                        "surface-container-highest": "#e2e2e5", "surface-container-lowest": "#ffffff",
                        "surface": "#f9f9fc", "on-surface": "#1a1c1e",
                        "on-surface-variant": "#40493e", "outline-variant": "#bfcaba",
                        "outline": "#6f7a6c", "tertiary": "#7c0400",
                        "error-container": "#ffdad6", "error": "#ba1a1a",
                    },
                    fontFamily: { 'headline': ['Manrope', 'sans-serif'], 'body': ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-surface text-on-surface">
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline text-xl font-extrabold text-primary uppercase tracking-tighter">{{ $operator->company_name ?? 'Operator' }}</h1>
            <p class="text-xs text-on-surface-variant opacity-70">Operator Portal</p>
        </div>
        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.dashboard') }}"><span class="material-symbols-outlined">dashboard</span><span>Dashboard</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.trips.index') }}"><span class="material-symbols-outlined">directions_bus</span><span>Manage Trips</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.buses.index') }}"><span class="material-symbols-outlined">fleet</span><span>Fleet</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.bookings.index') }}"><span class="material-symbols-outlined">book_online</span><span>All Bookings</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.customers.index') }}"><span class="material-symbols-outlined">people</span><span>Customers</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.revenue') }}"><span class="material-symbols-outlined">payments</span><span>Revenue</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.audit-log.index') }}"><span class="material-symbols-outlined">history</span><span>Audit Log</span></a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="{{ route('operator.profile') }}"><span class="material-symbols-outlined">settings</span><span>Settings</span></a>
        </div>
    </aside>

    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Bus Details</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-sm">person</span></div>
            </div>
        </header>

        <div class="p-8">
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-primary/5 border border-primary/10 rounded-xl text-primary font-bold text-sm">
                <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-error-container/20 border border-error-container/30 rounded-xl text-error font-bold text-sm">
                <span class="material-symbols-outlined">error</span>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <a href="{{ route('operator.buses.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>Back to Fleet
            </a>

            <!-- Bus Profile Card -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
                <div class="flex items-center gap-5">
                    <div class="h-16 w-16 rounded-2xl bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-3xl">directions_bus</span>
                    </div>
                    <div class="flex-grow">
                        <div class="flex items-center gap-3">
                            <h3 class="font-headline text-2xl font-extrabold">{{ $bus->registration_number }}</h3>
                            @if($bus->is_active)
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">Active</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-error-container/20 text-error">Inactive</span>
                            @endif
                        </div>
                        <p class="text-on-surface-variant mt-1">{{ $bus->model ?? 'No model specified' }} · {{ ucfirst($bus->bus_class) }} class</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openEditDrawer()" class="flex items-center gap-2 px-4 py-2 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined text-sm">edit</span> Edit
                        </button>
                        <form method="POST" action="{{ route('operator.buses.toggle-status', $bus->id) }}" onsubmit="return confirm('{{ $bus->is_active ? 'Deactivate' : 'Activate' }} this bus?')">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-bold {{ $bus->is_active ? 'bg-error-container/20 text-error hover:bg-error-container/30' : 'bg-primary/10 text-primary hover:bg-primary/20' }} transition-colors">
                                <span class="material-symbols-outlined text-sm">{{ $bus->is_active ? 'toggle_off' : 'toggle_on' }}</span>
                                {{ $bus->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ $bus->seat_capacity }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Seat Capacity</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ $stats['total_trips'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Trips</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ $stats['upcoming_trips'] }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Upcoming Trips</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold">ZMW {{ number_format($stats['total_revenue'], 0) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Revenue</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1 mb-6 w-fit">
                <button onclick="setTab('upcoming', this)" class="tab-btn px-4 py-2 rounded-lg text-sm font-bold bg-primary text-on-primary transition-all">Upcoming Trips</button>
                <button onclick="setTab('history', this)" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">Trip History</button>
                <button onclick="setTab('details', this)" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">Bus Details</button>
            </div>

            <!-- Upcoming Trips Tab -->
            <div id="tab-upcoming" class="tab-panel active">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-surface-container-low border-b border-outline-variant/15">
                                <tr>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Trip ID</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Route</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Date</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Departure</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Bookings</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Fare</th>
                                    <th class="px-5 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                                @forelse($upcomingTrips as $trip)
                                <tr class="hover:bg-surface-container-low/60 transition-colors">
                                    <td class="px-5 py-4 font-mono font-bold text-sm">{{ $trip['id'] }}</td>
                                    <td class="px-5 py-4">{{ $trip['origin'] }} → {{ $trip['destination'] }}</td>
                                    <td class="px-5 py-4 text-sm">{{ $trip['date'] }}</td>
                                    <td class="px-5 py-4 text-sm">{{ $trip['departure'] }}</td>
                                    <td class="px-5 py-4"><span class="font-bold">{{ $trip['bookings'] }}</span></td>
                                    <td class="px-5 py-4 font-bold">ZMW {{ number_format($trip['fare'], 2) }}</td>
                                    <td class="px-5 py-4">
                                        <a href="{{ route('operator.trips.bookings', $trip['route_id']) }}" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-block">
                                            <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">event</span>
                                    <p class="font-medium text-on-surface-variant">No upcoming trips for this bus.</p>
                                </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Trip History Tab -->
            <div id="tab-history" class="tab-panel">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-surface-container-low border-b border-outline-variant/15">
                                <tr>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Trip ID</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Route</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Date</th>
                                    <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Bookings</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                                @forelse($tripHistory as $trip)
                                <tr class="hover:bg-surface-container-low/60 transition-colors">
                                    <td class="px-5 py-4 font-mono font-bold text-sm">{{ $trip['id'] }}</td>
                                    <td class="px-5 py-4">{{ $trip['origin'] }} → {{ $trip['destination'] }}</td>
                                    <td class="px-5 py-4 text-sm">{{ $trip['date'] }}</td>
                                    <td class="px-5 py-4"><span class="font-bold">{{ $trip['bookings'] }}</span></td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">history</span>
                                    <p class="font-medium text-on-surface-variant">No trip history for this bus.</p>
                                </td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bus Details Tab -->
            <div id="tab-details" class="tab-panel">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h4 class="font-headline font-bold text-lg mb-4">Bus Information</h4>
                    <dl class="grid grid-cols-2 gap-6">
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Registration Number</dt>
                            <dd class="font-bold text-base mt-1">{{ $bus->registration_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Model</dt>
                            <dd class="font-bold text-base mt-1">{{ $bus->model ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Class</dt>
                            <dd class="font-bold text-base mt-1 capitalize">{{ $bus->bus_class }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Seat Capacity</dt>
                            <dd class="font-bold text-base mt-1">{{ $bus->seat_capacity }} seats</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Status</dt>
                            <dd class="mt-1">
                                @if($bus->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">Active</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-error-container/20 text-error">Inactive</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Amenities</dt>
                            <dd class="mt-1">
                                @if($bus->amenities)
                                    <div class="flex gap-1 flex-wrap mt-1">
                                        @foreach($bus->amenities as $amenity)
                                            <span class="px-2 py-0.5 bg-surface-container rounded text-xs font-bold text-on-surface-variant">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-on-surface-variant">None listed</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Last Maintenance</dt>
                            <dd class="font-bold text-base mt-1">{{ $bus->last_maintenance_date ? $bus->last_maintenance_date->format('d M Y') : 'Not recorded' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Next Maintenance</dt>
                            <dd class="font-bold text-base mt-1">{{ $bus->next_maintenance_date ? $bus->next_maintenance_date->format('d M Y') : 'Not scheduled' }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Mileage</dt>
                            <dd class="font-bold text-base mt-1">{{ $bus->mileage_km ? number_format($bus->mileage_km) . ' km' : 'Not recorded' }}</dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Notes</dt>
                            <dd class="text-base mt-1">{{ $bus->notes ?? 'No notes' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Bus Drawer -->
    <div id="edit-backdrop" class="drawer-backdrop fixed inset-0 bg-on-surface/30 z-30" onclick="closeEditDrawer()" style="display:none"></div>
    <aside id="edit-bus-drawer" class="drawer-panel fixed top-0 right-0 h-full w-[480px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl" style="display:none">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline font-bold text-lg text-on-surface">Edit Bus</h3>
                <p class="text-sm text-on-surface-variant mt-0.5">{{ $bus->registration_number }}</p>
            </div>
            <button onclick="closeEditDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('operator.buses.update', $bus->id) }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Registration Number *</label>
                <input type="text" name="registration_number" value="{{ old('registration_number', $bus->registration_number) }}" required
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Model</label>
                <input type="text" name="model" value="{{ old('model', $bus->model) }}"
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Seat Capacity *</label>
                    <input type="number" name="seat_capacity" value="{{ old('seat_capacity', $bus->seat_capacity) }}" required min="1" max="100"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Class *</label>
                    <select name="bus_class" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                        @foreach(['economy' => 'Economy', 'business' => 'Business', 'luxury' => 'Luxury'] as $val => $label)
                        <option value="{{ $val }}" {{ old('bus_class', $bus->bus_class) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Amenities</label>
                <div class="grid grid-cols-2 gap-2">
                    @php $amenitiesList = ['wifi' => 'WiFi', 'ac' => 'Air Conditioning', 'usb_charging' => 'USB Charging', 'entertainment' => 'Entertainment', 'refreshments' => 'Refreshments', 'restroom' => 'Restroom']; @endphp
                    @foreach($amenitiesList as $key => $label)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-container-low text-sm cursor-pointer hover:bg-surface-container transition-colors">
                        <input type="checkbox" name="amenities[]" value="{{ $key }}" {{ in_array($key, $bus->amenities ?? []) ? 'checked' : '' }} class="accent-primary rounded">
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Last Maintenance</label>
                    <input type="date" name="last_maintenance_date" value="{{ $bus->last_maintenance_date ? $bus->last_maintenance_date->format('Y-m-d') : '' }}"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Next Maintenance</label>
                    <input type="date" name="next_maintenance_date" value="{{ $bus->next_maintenance_date ? $bus->next_maintenance_date->format('Y-m-d') : '' }}"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Mileage (km)</label>
                <input type="number" name="mileage_km" min="0" value="{{ old('mileage_km', $bus->mileage_km) }}"
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Notes</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all resize-none">{{ old('notes', $bus->notes) }}</textarea>
            </div>
            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeEditDrawer()"
                    class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit"
                    class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-sm font-bold hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">save</span> Save Changes
                </button>
            </div>
        </form>
    </aside>

    <style>
        .drawer-panel { transform: translateX(100%); transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .drawer-panel.open { transform: translateX(0); }
        .drawer-backdrop { opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
        .drawer-backdrop.open { opacity: 1; pointer-events: auto; }
    </style>

    <script>
        function setTab(tab, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary', 'font-bold');
                b.classList.add('text-on-surface-variant', 'font-medium');
            });
            btn.classList.add('bg-primary', 'text-on-primary', 'font-bold');
            btn.classList.remove('text-on-surface-variant', 'font-medium');
        }

        function openEditDrawer() {
            document.getElementById('edit-bus-drawer').style.display = 'flex';
            document.getElementById('edit-backdrop').style.display = 'block';
            setTimeout(() => {
                document.getElementById('edit-bus-drawer').classList.add('open');
                document.getElementById('edit-backdrop').classList.add('open');
            }, 10);
        }
        function closeEditDrawer() {
            document.getElementById('edit-bus-drawer').classList.remove('open');
            document.getElementById('edit-backdrop').classList.remove('open');
            setTimeout(() => {
                document.getElementById('edit-bus-drawer').style.display = 'none';
                document.getElementById('edit-backdrop').style.display = 'none';
            }, 250);
        }
    </script>
</body>
</html>