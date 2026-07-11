<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zambia Transit | Manage Trips</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Manrope:wght@700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap"
        rel="stylesheet" />

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }

        /* Drawer transition */
        #trip-drawer {
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #trip-drawer.open { transform: translateX(0); }
        #drawer-backdrop {
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #drawer-backdrop.open { opacity: 1; pointer-events: auto; }

        /* Row hover action reveal */
        .trip-row .row-actions { opacity: 0; transition: opacity 0.15s ease; }
        .trip-row:hover .row-actions { opacity: 1; }
    </style>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "seat-available": "#00601f",
                        "on-tertiary-fixed-variant": "#920600",
                        "on-primary-fixed": "#002106",
                        "inverse-primary": "#86d989",
                        "surface-container-low": "#f3f3f6",
                        "primary-fixed": "#a1f6a3",
                        "surface-variant": "#e2e2e5",
                        "primary-container": "#197b30",
                        "on-primary": "#ffffff",
                        "outline-variant": "#bfcaba",
                        "outline": "#6f7a6c",
                        "tertiary-fixed-dim": "#ffb4a7",
                        "on-secondary-fixed": "#301400",
                        "surface-dim": "#d9dadd",
                        "seat-booked": "#a80800",
                        "on-background": "#1a1c1e",
                        "surface-container-high": "#e8e8eb",
                        "surface": "#f9f9fc",
                        "on-primary-fixed-variant": "#00531a",
                        "surface-container": "#edeef1",
                        "primary-fixed-dim": "#86d989",
                        "on-secondary-container": "#6f3600",
                        "surface-container-highest": "#e2e2e5",
                        "inverse-on-surface": "#f0f0f3",
                        "error-container": "#ffdad6",
                        "secondary-fixed-dim": "#ffb785",
                        "on-tertiary-container": "#ffb3a7",
                        "seat-selected": "#0077b6",
                        "surface-container-lowest": "#ffffff",
                        "background": "#f9f9fc",
                        "error": "#ba1a1a",
                        "tertiary-fixed": "#ffdad4",
                        "on-surface": "#1a1c1e",
                        "on-tertiary": "#ffffff",
                        "tertiary-container": "#a80800",
                        "secondary-container": "#fd9c53",
                        "primary": "#004614",
                        "on-secondary-fixed-variant": "#713700",
                        "surface-tint": "#176d2a",
                        "secondary-fixed": "#ffdcc6",
                        "tertiary": "#7c0400",
                        "on-tertiary-fixed": "#400100",
                        "on-surface-variant": "#40493e",
                        "secondary": "#954a00",
                        "on-error": "#ffffff",
                        "on-primary-container": "#85d988",
                        "on-secondary": "#ffffff",
                        "on-error-container": "#93000a",
                        "surface-bright": "#f9f9fc",
                        "inverse-surface": "#2f3133"
                    },
                    "fontFamily": {
                        "headline-lg": ["Manrope"],
                        "headline-md": ["Manrope"],
                        "headline-sm": ["Manrope"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-caps": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "800"}],
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

    <!-- Sidebar Navigation -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low dark:bg-surface-dim flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline-md text-headline-md font-extrabold text-primary dark:text-primary-fixed uppercase tracking-tighter">
                {{ $operator->company_name ?? 'Power Tools Bus' }}
            </h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">Operator Portal</p>
        </div>

        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="{{ route('operator.dashboard') }}">
                <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                <span class="font-body-md text-body-md">Dashboard</span>
            </a>

            <!-- Active: Manage Trips -->
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary dark:text-primary-fixed font-bold border-r-4 border-primary dark:border-primary-fixed bg-surface-container-high dark:bg-surface-container transition-all duration-150"
                href="#">
                <span class="material-symbols-outlined" data-icon="directions_bus">directions_bus</span>
                <span class="font-body-md text-body-md">Manage Trips</span>
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="#">
                <span class="material-symbols-outlined" data-icon="event_seat">event_seat</span>
                <span class="font-body-md text-body-md">Seat Maps</span>
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="#">
                <span class="material-symbols-outlined" data-icon="payments">payments</span>
                <span class="font-body-md text-body-md">Revenue</span>
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="{{route('operator.profile')}}">
                <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
                <span class="font-body-md text-body-md">Profile</span>
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-outline-variant/20 space-y-1">
            <button onclick="openDrawer()"
                class="w-full bg-primary text-on-primary py-3 rounded-xl font-bold flex items-center justify-center gap-2 hover:brightness-110 transition-all mb-6">
                <span class="material-symbols-outlined" data-icon="add_circle">add_circle</span>
                <span class="font-body-md text-body-md">New Trip</span>
            </button>
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="#">
                <span class="material-symbols-outlined" data-icon="settings">settings</span>
                <span class="font-body-md text-body-md">Settings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="#">
                <span class="material-symbols-outlined" data-icon="help">help</span>
                <span class="font-body-md text-body-md">Support</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">

        <!-- Header -->
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low dark:bg-surface-container border-b border-outline-variant/15 z-10">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary">Manage Trips</h2>
            </div>
            <div class="flex items-center gap-6">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
                    <input
                        class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-full w-64 text-body-sm focus:ring-2 focus:ring-primary transition-all"
                        placeholder="Search trips, buses, routes..."
                        type="text"
                        id="search-input"
                        oninput="filterTrips()" />
                </div>
                <div class="flex items-center gap-4">
                    <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors relative" data-icon="notifications">
                        notifications
                        <span class="absolute top-0 right-0 w-2 h-2 bg-tertiary rounded-full"></span>
                    </button>
                    <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors" data-icon="schedule">schedule</button>
                    <div class="h-8 w-8 rounded-full bg-primary-container flex items-center justify-center overflow-hidden border border-primary/20 cursor-pointer">
                        <a href="{{ route('operator.profile')}}">
                            <img class="w-full h-full object-cover"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCC_um6BOHoYSxo-sV9I20VWjGhb-zV38rw6PHYfGSX0QMjIeGSDnnne5VQ14Dzbl5_QrVJX5_vf9Oa8BbR14Quv_NiAyAwDiq0kE0DcDQJOphyR4LIeKHbMnH-h8ZMG6u6DR33RnE2cyaFw6ZAbOHdAOBnCN1v0jT6IZfFuEKJpPprVP7AizC0wx979g01rNJ1E_sy6EkF-9fnN8eQsFlOzY2E0gyGlEvPmSuK6usojfMoTbfn4_z-_fOxaQj3y4d_f9TRJqctFUM"
                                alt="Profile avatar">
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Canvas -->
        <div class="p-8">

            <!-- Filter + Action Bar -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <!-- Status filter pills -->
                    <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1">
                        <button onclick="setFilter('all', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-bold bg-primary text-on-primary transition-all">
                            All trips
                        </button>
                        <button onclick="setFilter('scheduled', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            Scheduled
                        </button>
                        <button onclick="setFilter('on_route', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            On route
                        </button>
                        <button onclick="setFilter('delayed', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            Delayed
                        </button>
                        <button onclick="setFilter('completed', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            Completed
                        </button>
                    </div>

                    <!-- Date filter -->
                    <select class="pl-3 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-body-sm text-on-surface-variant focus:ring-2 focus:ring-primary">
                        <option>Today — {{ date('d M') }}</option>
                        <option>Yesterday</option>
                        <option>This week</option>
                        <option>This month</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button class="flex items-center gap-2 px-4 py-2 border border-outline-variant/30 rounded-xl text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-sm">download</span>
                        Export
                    </button>
                    <button onclick="openDrawer()"
                        class="flex items-center gap-2 px-5 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                        <span class="material-symbols-outlined text-sm">add</span>
                        New trip
                    </button>
                </div>
            </div>

            <!-- Trips Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="trips-table">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Trip</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Route</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Date &amp; time</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Bus</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Occupancy</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Fare</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10" id="trips-tbody">
                            @forelse($trips as $trip)
                            @php
                                $occ_pct = round(($trip['booked'] / max($trip['capacity'], 1)) * 100);
                                $badge   = $status_styles[$trip['status_type']] ?? 'bg-surface-container text-on-surface-variant';
                            @endphp
                            <tr class="trip-row hover:bg-surface-container-low/60 transition-colors group"
                                data-status="{{ $trip['status_type'] }}"
                                data-search="{{ strtolower($trip['id'] . ' ' . $trip['route_from'] . ' ' . $trip['route_to'] . ' ' . $trip['bus']) }}">
                                <td class="px-5 py-4">
                                    <span class="font-bold text-body-sm text-on-surface tracking-wide">{{ $trip['id'] }}</span>
                                    <span class="block text-body-sm text-on-surface-variant capitalize mt-0.5">{{ $trip['class'] }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 font-bold text-body-md text-on-surface">
                                        {{ $trip['route_from'] }}
                                        <span class="material-symbols-outlined text-sm text-outline">arrow_forward</span>
                                        {{ $trip['route_to'] }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium text-body-sm text-on-surface">{{ $trip['date'] }}</span>
                                    <span class="block text-body-sm text-on-surface-variant">{{ $trip['departure'] }} hrs</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-primary/8 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-primary" style="font-size:15px">directions_bus</span>
                                        </div>
                                        <span class="font-medium text-body-sm text-on-surface">{{ $trip['bus'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-20 bg-surface-container-highest rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-primary h-1.5 rounded-full transition-all" style="width: {{ $occ_pct }}%"></div>
                                        </div>
                                        <span class="text-body-sm font-semibold text-on-surface-variant whitespace-nowrap">
                                            {{ $trip['booked'] }}/{{ $trip['capacity'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-body-sm text-on-surface">ZMW {{ number_format($trip['fare']) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $badge }}">
                                        {{ $trip['status'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="row-actions flex items-center gap-1 justify-end">
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors" title="Edit trip">
                                            <span class="material-symbols-outlined" style="font-size:18px">edit</span>
                                        </button>
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors" title="View seat map">
                                            <span class="material-symbols-outlined" style="font-size:18px">event_seat</span>
                                        </button>
                                        @if(in_array($trip['status_type'], ['scheduled', 'delayed']))
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Cancel trip">
                                            <span class="material-symbols-outlined" style="font-size:18px">cancel</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">directions_bus</span>
                                    <p class="font-body-md text-body-md text-on-surface-variant">No trips found.</p>
                                    <button onclick="openDrawer()" class="mt-4 text-primary font-bold text-body-sm hover:underline">Schedule your first trip</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table footer -->
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-body-sm text-on-surface-variant">Showing {{ count($trips) }} trips</span>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors disabled:opacity-40" disabled>
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </button>
                        <span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-body-sm font-bold">1</span>
                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Drawer Backdrop -->
    <div id="drawer-backdrop"
        class="fixed inset-0 bg-on-surface/30 z-30"
        onclick="closeDrawer()">
    </div>

    <!-- New Trip Drawer -->
    <aside id="trip-drawer"
        class="fixed top-0 right-0 h-full w-[440px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">

        <!-- Drawer header -->
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">New trip</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Schedule a departure for your fleet</p>
            </div>
            <button onclick="closeDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Drawer form -->
        <form action="{{ route('operator.trips.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-6">
            @csrf

            <!-- Route -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Route</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">From</label>
                        <select name="origin" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                            @foreach($routes as $route)
                            <option value="{{ $route['from'] }}">{{ $route['from'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">To</label>
                        <select name="destination" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                            @foreach($routes as $route)
                            <option value="{{ $route['to'] }}">{{ $route['to'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Date &amp; time</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Departure date</label>
                        <input type="date" name="travel_date" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Departure time</label>
                        <input type="time" name="departure_time" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Bus assignment -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Assign bus</label>
                <div class="space-y-2">
                    @foreach($buses as $bus)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/20 bg-surface-container-low cursor-pointer hover:border-primary/40 hover:bg-surface-container transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                        <input type="radio" name="bus_id" value="{{ $bus['id'] }}" class="accent-primary">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary" style="font-size:16px">directions_bus</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-bold text-body-sm text-on-surface block">{{ $bus['plate'] }}</span>
                            <span class="text-body-sm text-on-surface-variant">{{ $bus['model'] }} · {{ $bus['capacity'] }} seats</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Trip class & Fare -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Trip class</label>
                    <select name="class" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                        <option value="economy">Economy</option>
                        <option value="business">Business</option>
                    </select>
                </div>
                <div>
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Fare (ZMW)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-body-sm text-body-sm text-on-surface-variant">K</span>
                        <input type="number" name="fare" placeholder="0.00"
                            class="w-full pl-7 rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Notes <span class="normal-case text-on-surface-variant/50">(optional)</span></label>
                <textarea name="notes" rows="3" placeholder="Driver assignment, stop notes, special instructions..."
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3 resize-none"></textarea>
            </div>

            <!-- Drawer footer -->
            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawer()"
                    class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-2 flex-grow-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Schedule trip
                </button>
            </div>
        </form>
    </aside>

    <script>
        // Drawer open/close
        function openDrawer() {
            document.getElementById('trip-drawer').classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }
        function closeDrawer() {
            document.getElementById('trip-drawer').classList.remove('open');
            document.getElementById('drawer-backdrop').classList.remove('open');
        }

        // Status filter
        let activeFilter = 'all';
        function setFilter(status, btn) {
            activeFilter = status;
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary');
                b.classList.add('text-on-surface-variant');
            });
            btn.classList.add('bg-primary', 'text-on-primary');
            btn.classList.remove('text-on-surface-variant');
            applyFilters();
        }

        // Search filter
        function filterTrips() { applyFilters(); }

        function applyFilters() {
            const q = document.getElementById('search-input').value.toLowerCase();
            document.querySelectorAll('.trip-row').forEach(row => {
                const matchStatus = activeFilter === 'all' || row.dataset.status === activeFilter;
                const matchSearch = !q || row.dataset.search.includes(q);
                row.style.display = matchStatus && matchSearch ? '' : 'none';
            });
        }

        // Micro-interactions
        document.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('mousedown', () => el.classList.add('scale-[0.98]'));
            el.addEventListener('mouseup',   () => el.classList.remove('scale-[0.98]'));
            el.addEventListener('mouseleave',() => el.classList.remove('scale-[0.98]'));
        });
    </script>
    
</body>
</html>