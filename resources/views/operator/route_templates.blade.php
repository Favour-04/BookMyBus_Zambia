<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zambia Transit | Route Templates</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Manrope:wght@700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap"
        rel="stylesheet" />

    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }

        #template-drawer, #create-trip-drawer {
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #template-drawer.open, #create-trip-drawer.open { transform: translateX(0); }
        #drawer-backdrop {
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #drawer-backdrop.open { opacity: 1; pointer-events: auto; }
    </style>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-low": "#f3f3f6", "primary": "#004614",
                        "on-primary": "#ffffff", "outline-variant": "#bfcaba", "outline": "#6f7a6c",
                        "surface": "#f9f9fc", "surface-container-high": "#e8e8eb",
                        "surface-container": "#edeef1", "surface-container-highest": "#e2e2e5",
                        "error-container": "#ffdad6", "surface-container-lowest": "#ffffff",
                        "background": "#f9f9fc", "error": "#ba1a1a", "on-surface": "#1a1c1e",
                        "on-surface-variant": "#40493e", "tertiary": "#7c0400",
                    },
                    fontFamily: { "headline-md": ["Manrope"], "headline-sm": ["Manrope"], "body-md": ["Inter"], "body-sm": ["Inter"], "label-caps": ["Inter"] },
                    fontSize: {
                        "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "700"}],
                        "headline-sm": ["14px", {"lineHeight": "20px", "fontWeight": "700"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                        "body-sm": ["12px", {"lineHeight": "16px", "fontWeight": "400"}],
                        "label-caps": ["10px", {"lineHeight": "12px", "letterSpacing": "0.1em", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-background text-on-surface">

    <!-- Sidebar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline-md text-headline-md font-extrabold text-primary uppercase tracking-tighter">{{ $operator->company_name ?? 'Operator' }}</h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">Operator Portal</p>
        </div>
        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span><span class="font-body-md">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.trips.index') }}">
                <span class="material-symbols-outlined">directions_bus</span><span class="font-body-md">Manage Trips</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-high" href="{{ route('operator.route-templates.index') }}">
                <span class="material-symbols-outlined">route</span><span class="font-body-md">Route Templates</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.buses.index') }}">
                <span class="material-symbols-outlined">fleet</span><span class="font-body-md">Fleet</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.bookings.index') }}">
                <span class="material-symbols-outlined">book_online</span><span class="font-body-md">All Bookings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.revenue') }}">
                <span class="material-symbols-outlined">payments</span><span class="font-body-md">Revenue</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.fare-rules.index') }}">
                <span class="material-symbols-outlined">sell</span><span class="font-body-md">Fare Rules</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.promo-codes.index') }}">
                <span class="material-symbols-outlined">confirmation_number</span><span class="font-body-md">Promo Codes</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.audit-log.index') }}">
                <span class="material-symbols-outlined">history</span><span class="font-body-md">Audit Log</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors" href="{{ route('operator.profile') }}">
                <span class="material-symbols-outlined">account_circle</span><span class="font-body-md">Profile</span>
            </a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline-sm text-headline-sm text-primary">Route Templates</h2>
            <div class="flex items-center gap-4">
                <span class="text-body-sm text-on-surface-variant">Reusable route definitions</span>
            </div>
        </header>

        <div class="p-8">
            @if(session('success'))
            <div class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-xl text-body-sm text-primary font-medium">{{ session('success') }}</div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <p class="text-body-sm text-on-surface-variant">{{ count($templates) }} template(s) configured</p>
                <button onclick="openDrawer('template')" class="flex items-center gap-2 px-5 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    New Template
                </button>
            </div>

            <!-- Templates Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($templates as $template)
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="font-headline-md text-headline-md text-on-surface">{{ $template->name }}</h3>
                            <p class="text-body-sm text-on-surface-variant mt-1">
                                {{ $template->origin }} <span class="material-symbols-outlined text-sm text-outline">arrow_forward</span> {{ $template->destination }}
                            </p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-label-sm font-bold {{ $template->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                            {{ $template->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-4 border-t border-dashed border-outline-variant/20">
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Base Fare</p>
                            <p class="font-bold text-body-md text-on-surface">ZMW {{ number_format($template->base_fare, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Distance</p>
                            <p class="font-bold text-body-md text-on-surface">{{ $template->distance_km > 0 ? number_format($template->distance_km, 1) . ' km' : '--' }}</p>
                        </div>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Total Trips</p>
                            <p class="font-bold text-body-md text-on-surface">{{ $template->trips_count }}</p>
                        </div>
                        <div>
                            <p class="text-label-sm text-on-surface-variant">Upcoming</p>
                            <p class="font-bold text-body-md text-on-surface">{{ $template->recent_trips ?? 0 }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2 pt-4 border-t border-outline-variant/10">
                        <button onclick="openCreateTripDrawer('{{ $template->id }}', '{{ addslashes($template->name) }}', '{{ $template->origin }}', '{{ $template->destination }}', {{ $template->base_fare }})"
                            class="flex-1 px-3 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all text-center">
                            <span class="material-symbols-outlined text-sm">add</span> Schedule Trip
                        </button>
                        <form action="{{ route('operator.route-templates.destroy', $template->id) }}" method="POST" onsubmit="return confirm('Delete template "{{ $template->name }}"?')">
                            @csrf @method('DELETE')
                            <button class="p-2 rounded-xl text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Delete">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">route</span>
                    <p class="font-body-md text-body-md text-on-surface-variant">No route templates yet.</p>
                    <button onclick="openDrawer('template')" class="mt-4 text-primary font-bold text-body-sm hover:underline">Create your first template</button>
                </div>
                @endforelse
            </div>
        </div>
    </main>

    <!-- Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-on-surface/30 z-30" onclick="closeDrawers()"></div>

    <!-- Drawer: New/Edit Template -->
    <aside id="template-drawer" class="fixed top-0 right-0 h-full w-[420px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">New Route Template</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Define a reusable route</p>
            </div>
            <button onclick="closeDrawers()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('operator.route-templates.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5">
            @csrf
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Template Name</label>
                <input type="text" name="name" placeholder="e.g. Lusaka-Livingstone Express" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Origin</label>
                    <input type="text" name="origin" placeholder="e.g. Lusaka" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Destination</label>
                    <input type="text" name="destination" placeholder="e.g. Livingstone" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Distance (km) <span class="text-on-surface-variant/50">optional</span></label>
                    <input type="number" name="distance_km" placeholder="e.g. 472" min="0" step="0.1"
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Base Fare (ZMW)</label>
                    <input type="number" name="base_fare" placeholder="e.g. 150" min="0" step="0.01" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>
            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawers()" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all">Create Template</button>
            </div>
        </form>
    </aside>

    <!-- Drawer: Create Trip from Template -->
    <aside id="create-trip-drawer" class="fixed top-0 right-0 h-full w-[440px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface" id="drawer-trip-title">Schedule Trip</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5" id="drawer-trip-route">From template</p>
            </div>
            <button onclick="closeDrawers()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5" id="create-trip-form">
            @csrf
            <div class="p-4 bg-surface-container-low rounded-xl">
                <p class="text-body-sm text-on-surface-variant">Template</p>
                <p class="font-headline-md text-headline-md text-on-surface" id="drawer-template-name">--</p>
                <p class="text-body-sm text-on-surface-variant" id="drawer-template-route">-- → --</p>
                <p class="text-body-sm font-bold text-primary mt-1" id="drawer-template-fare">Base fare: ZMW 0.00</p>
            </div>

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Travel Date</label>
                <input type="date" name="travel_date" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Departure Time</label>
                    <input type="time" name="departure_time" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Arrival Time <span class="text-on-surface-variant/50">optional</span></label>
                    <input type="time" name="arrival_time"
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>

            <!-- Bus selection -->
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Assign Bus</label>
                <select name="bus_id" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    <option value="">Select a bus...</option>
                    @foreach(\App\Models\Bus::where('operator_id', $operator->id)->where('is_active', true)->get() as $bus)
                    <option value="{{ $bus->id }}">{{ $bus->registration_number }} ({{ $bus->seat_capacity }} seats)</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Fare (ZMW) <span class="text-on-surface-variant/50">leave empty for template default</span></label>
                <input type="number" name="fare" placeholder="Uses template default" min="0" step="0.01"
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>

            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawers()" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all">Schedule Trip</button>
            </div>
        </form>
    </aside>

    <script>
        function openDrawer(type) {
            document.getElementById(type + '-drawer').classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }
        function closeDrawers() {
            document.querySelectorAll('[id$="-drawer"]').forEach(el => el.classList.remove('open'));
            document.getElementById('drawer-backdrop').classList.remove('open');
        }

        function openCreateTripDrawer(id, name, origin, destination, fare) {
            document.getElementById('drawer-trip-title').textContent = 'Schedule Trip: ' + name;
            document.getElementById('drawer-trip-route').textContent = origin + ' → ' + destination;
            document.getElementById('drawer-template-name').textContent = name;
            document.getElementById('drawer-template-route').textContent = origin + ' → ' + destination;
            document.getElementById('drawer-template-fare').textContent = 'Base fare: ZMW ' + fare.toFixed(2);
            document.getElementById('create-trip-form').action = '/operator/route-templates/' + id + '/create-trip';
            document.getElementById('create-trip-drawer').classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }

        document.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('mousedown', () => el.classList.add('scale-[0.98]'));
            el.addEventListener('mouseup', () => el.classList.remove('scale-[0.98]'));
            el.addEventListener('mouseleave', () => el.classList.remove('scale-[0.98]'));
        });
    </script>
</body>
</html>