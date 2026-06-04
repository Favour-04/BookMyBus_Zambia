<!-- TODO: This view should extend layouts.app when a layout is available -->
<!-- TODO: Loop through 'trips' from Routes table to display upcoming trips -->

<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Operator Dashboard - BookMyBus Zambia</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;700;800&family=Inter:wght@400;500;600&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "on-secondary": "#ffffff",
                      "on-secondary-container": "#632f00",
                      "on-secondary-fixed": "#301400",
                      "inverse-surface": "#2f3133",
                      "on-primary-container": "#b1ffb1",
                      "secondary-container": "#ff8921",
                      "primary-fixed-dim": "#7edb83",
                      "inverse-primary": "#7edb83",
                      "background": "#f9f9fc",
                      "inverse-on-surface": "#f0f0f3",
                      "tertiary-container": "#d1200f",
                      "secondary-fixed": "#ffdcc6",
                      "surface-container-lowest": "#ffffff",
                      "outline-variant": "#bfcaba",
                      "on-tertiary": "#ffffff",
                      "surface": "#f9f9fc",
                      "on-primary": "#ffffff",
                      "primary-fixed": "#99f89d",
                      "surface-variant": "#e2e2e5",
                      "secondary-fixed-dim": "#ffb784",
                      "on-primary-fixed": "#002106",
                      "surface-bright": "#f9f9fc",
                      "on-error-container": "#93000a",
                      "surface-dim": "#dadadc",
                      "outline": "#6f7a6c",
                      "on-secondary-fixed-variant": "#713700",
                      "primary": "#00601f",
                      "on-primary-fixed-variant": "#00531a",
                      "surface-container-low": "#f3f3f6",
                      "on-surface-variant": "#3f493e",
                      "surface-container-high": "#e8e8ea",
                      "on-surface": "#1a1c1e",
                      "primary-container": "#197b30",
                      "error-container": "#ffdad6",
                      "tertiary-fixed": "#ffdad4",
                      "on-tertiary-fixed": "#400100",
                      "tertiary-fixed-dim": "#ffb4a7",
                      "surface-container-highest": "#e2e2e5",
                      "on-tertiary-container": "#ffe7e3",
                      "on-tertiary-fixed-variant": "#920600",
                      "surface-tint": "#006e25",
                      "error": "#ba1a1a",
                      "surface-container": "#eeeef0",
                      "secondary": "#954a00",
                      "tertiary": "#a80800",
                      "on-error": "#ffffff",
                      "on-background": "#1a1c1e"
              },
              "borderRadius": {
                      "DEFAULT": "0.125rem",
                      "lg": "0.25rem",
                      "xl": "0.5rem",
                      "full": "0.75rem"
              },
              "fontFamily": {
                      "headline": ["Manrope"],
                      "body": ["Inter"],
                      "label": ["Inter"]
              }
            },
          },
        }
    </script>
<style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        body { font-family: 'Inter', sans-serif; }
        h1, h2, h3, .font-headline { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="bg-surface text-on-surface">
<!-- SideNavBar (Shared Component) -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-zinc-50 dark:bg-zinc-950 border-r border-zinc-200/50 dark:border-zinc-800/50 flex flex-col h-full py-6 font-inter text-sm font-medium">
<div class="font-manrope font-black text-lg text-green-900 dark:text-green-100 px-6 mb-8">
            BookMyBus Zambia
        </div>
<nav class="flex-1 space-y-1">
<!-- Dashboard (Active) -->
<div class="bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-100 rounded-lg mx-2 px-4 py-3 cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3">
<span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
<span>Dashboard</span>
</div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3">
<span class="material-symbols-outlined" data-icon="directions_bus">directions_bus</span>
<span>Manage Trips</span>
</div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3">
<span class="material-symbols-outlined" data-icon="airline_seat_recline_extra">airline_seat_recline_extra</span>
<span>Seat Maps</span>
</div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3">
<span class="material-symbols-outlined" data-icon="payments">payments</span>
<span>Revenue</span>
</div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-3 rounded-lg cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3">
<span class="material-symbols-outlined" data-icon="dataset">dataset</span>
<span>Fleet Management</span>
</div>
</nav>
<div class="px-4 mb-6">
<button class="w-full bg-gradient-to-br from-primary to-primary-container text-white py-3 rounded-xl font-headline font-bold text-sm shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-sm" data-icon="add">add</span>
                Add New Trip
            </button>
</div>
<div class="border-t border-zinc-200/50 dark:border-zinc-800/50 pt-4 mt-auto">
<div class="flex items-center px-6 mb-6">
<div class="w-8 h-8 rounded-full bg-surface-container-highest overflow-hidden mr-3">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDYdD0MuvKteWZO8udxva74oGZX_HVmlL44CSe4Zywhn-w565oAJpB-yYK5frtToRdptj26IeKtmxnK63U3_teckn8XhS5t6A_DLT6eP2Q1BXBgjoy2SohJj0gEV1BBFj-YvUzMDrXMovoOgXzByi7pSVzn-fHS1EJWG2oZfjZ8TJng7jM3Y-0GbRqS8EhFPl-J3PA4pO_t9kjIRmh0a77iah4KoXcNJPHxsV4NT7MJgl0DLwutXXCXKNYVMF65iR6vXGvTtLicM3A8"/>
</div>
<div>
<p class="text-xs font-bold text-on-surface">Bus Operator</p>
<p class="text-[10px] text-zinc-500">Zambia Transit Hub</p>
</div>
</div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-2 cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-xs font-medium">
<span class="material-symbols-outlined" data-icon="settings">settings</span>
<span>Settings</span>
</div>
<div class="text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-900 mx-2 px-4 py-2 cursor-pointer transition-transform hover:translate-x-1 flex items-center gap-3 text-xs font-medium">
<span class="material-symbols-outlined" data-icon="logout">logout</span>
<span>Logout</span>
</div>
</div>
</aside>
<!-- Main Content Canvas -->
<main class="ml-64 p-8 min-h-screen">
<!-- Header Section -->
<header class="mb-10 flex justify-between items-end">
<div>
<span class="text-secondary font-headline font-bold tracking-widest text-[10px] uppercase">Operational Overview</span>
<h1 class="text-4xl font-headline font-extrabold text-on-surface tracking-tight mt-1">Dashboard</h1>
</div>
<div class="flex gap-4">
<div class="bg-surface-container-low px-4 py-2 rounded-xl flex items-center gap-2">
<span class="material-symbols-outlined text-zinc-400" data-icon="calendar_today">calendar_today</span>
<span class="text-sm font-medium text-on-surface-variant">Nov 14, 2024</span>
</div>
</div>
</header>
<!-- Metrics Bento Grid -->
<section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
<!-- Metric Card 1 -->
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm shadow-on-surface/5 flex flex-col justify-between h-40">
<div class="flex justify-between items-start">
<div class="bg-primary/10 p-2 rounded-lg">
<span class="material-symbols-outlined text-primary" data-icon="confirmation_number">confirmation_number</span>
</div>
<span class="text-primary font-bold text-xs">+12%</span>
</div>
<div>
<p class="text-on-surface-variant text-xs font-label">Total Bookings</p>
<p class="text-3xl font-headline font-extrabold text-on-surface">1,284</p>
</div>
</div>
<!-- Metric Card 2 -->
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm shadow-on-surface/5 flex flex-col justify-between h-40">
<div class="flex justify-between items-start">
<div class="bg-secondary/10 p-2 rounded-lg">
<span class="material-symbols-outlined text-secondary" data-icon="payments">payments</span>
</div>
<span class="text-primary font-bold text-xs">+8%</span>
</div>
<div>
<p class="text-on-surface-variant text-xs font-label">Revenue (ZMW)</p>
<p class="text-3xl font-headline font-extrabold text-on-surface">42,500</p>
</div>
</div>
<!-- Metric Card 3 -->
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm shadow-on-surface/5 flex flex-col justify-between h-40">
<div class="flex justify-between items-start">
<div class="bg-tertiary-container/10 p-2 rounded-lg">
<span class="material-symbols-outlined text-tertiary" data-icon="route">route</span>
</div>
<span class="text-zinc-400 font-bold text-xs">Stable</span>
</div>
<div>
<p class="text-on-surface-variant text-xs font-label">Active Trips</p>
<p class="text-3xl font-headline font-extrabold text-on-surface">24</p>
</div>
</div>
<!-- Metric Card 4: Occupancy Rate -->
<div class="bg-primary text-on-primary p-6 rounded-xl shadow-lg shadow-primary/20 flex flex-col justify-between h-40 relative overflow-hidden">
<div class="relative z-10">
<p class="text-primary-fixed-dim text-xs font-label">Avg. Occupancy</p>
<p class="text-4xl font-headline font-extrabold mt-1">82%</p>
</div>
<div class="relative z-10 mt-2">
<div class="w-full bg-white/20 h-1.5 rounded-full overflow-hidden">
<div class="bg-white h-full w-[82%]"></div>
</div>
</div>
<!-- Decorative element -->
<div class="absolute -right-4 -bottom-4 opacity-10">
<span class="material-symbols-outlined text-9xl" data-icon="trending_up">trending_up</span>
</div>
</div>
</section>
<!-- Main Dashboard Content Area -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
<!-- Upcoming Trips Table Area (2/3 width) -->
<section class="lg:col-span-2 space-y-6">
<div class="flex items-center justify-between">
<h2 class="text-xl font-headline font-bold text-on-surface">Upcoming Trips</h2>
<button class="text-secondary text-sm font-semibold flex items-center gap-1 hover:underline underline-offset-4 transition-all">
                        View Schedule <span class="material-symbols-outlined text-sm" data-icon="chevron_right">chevron_right</span>
</button>
</div>
<div class="bg-surface-container-lowest rounded-xl shadow-sm shadow-on-surface/5 overflow-hidden">
<table class="w-full text-left border-collapse">
<thead class="bg-surface-container-low border-b border-outline-variant/15">
<tr>
<th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Trip ID</th>
<th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Route</th>
<th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Departure</th>
<th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Load</th>
<th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-outline-variant/10">
{{-- TODO: Loop through 'trips' from Routes table to display upcoming trips --}}
@foreach($trips ?? [] as $trip)
<tr class="hover:bg-surface-container-low/50 transition-colors">
<td class="px-6 py-5">
<span class="text-xs font-mono font-bold text-zinc-400">#{{ $trip->id ?? 'BT-1022' }}</span>
</td>
<td class="px-6 py-5">
<div class="flex items-center gap-3">
<div class="flex flex-col">
<span class="text-sm font-bold text-on-surface">{{ $trip->origin ?? 'Lusaka' }}</span>
<span class="text-[10px] text-zinc-400">{{ $trip->origin_terminal ?? 'Inter-City Terminus' }}</span>
</div>
<span class="material-symbols-outlined text-zinc-300 text-sm" data-icon="trending_flat">trending_flat</span>
<div class="flex flex-col">
<span class="text-sm font-bold text-on-surface">{{ $trip->destination ?? 'Ndola' }}</span>
<span class="text-[10px] text-zinc-400">{{ $trip->destination_terminal ?? 'Main Bus Park' }}</span>
</div>
</div>
</td>
<td class="px-6 py-5">
<div class="flex flex-col">
<span class="text-sm font-bold text-on-surface">{{ $trip->departure_time ?? '08:30 AM' }}</span>
<span class="text-[10px] text-zinc-400">{{ $trip->departure_date ?? 'Today' }}</span>
</div>
</td>
<td class="px-6 py-5">
<div class="flex items-center gap-2">
<span class="text-sm font-medium text-on-surface">{{ $trip->booked_seats ?? 32 }}/{{ $trip->total_seats ?? 45 }}</span>
<div class="w-12 bg-zinc-100 h-1 rounded-full overflow-hidden">
<div class="bg-primary h-full w-[{{ $trip->occupancy_percentage ?? 71 }}%]"></div>
</div>
</div>
</td>
<td class="px-6 py-5 text-right">
<button class="bg-surface-container-low hover:bg-surface-container-high text-on-surface p-2 rounded-lg transition-colors">
<span class="material-symbols-outlined text-sm" data-icon="more_horiz">more_horiz</span>
</button>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
</section>
<!-- Revenue Chart & Notifications (1/3 width) -->
<aside class="space-y-8">
<!-- Mini Chart Card -->
<div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm shadow-on-surface/5">
<h3 class="text-sm font-bold text-on-surface mb-4">Revenue Weekly Trend</h3>
<div class="flex items-end gap-2 h-24 mb-4">
<div class="flex-1 bg-surface-container-low rounded-t-sm h-[40%]"></div>
<div class="flex-1 bg-surface-container-low rounded-t-sm h-[60%]"></div>
<div class="flex-1 bg-surface-container-low rounded-t-sm h-[45%]"></div>
<div class="flex-1 bg-primary/40 rounded-t-sm h-[80%]"></div>
<div class="flex-1 bg-primary rounded-t-sm h-[100%]"></div>
<div class="flex-1 bg-surface-container-low rounded-t-sm h-[30%]"></div>
<div class="flex-1 bg-surface-container-low rounded-t-sm h-[55%]"></div>
</div>
<div class="flex justify-between text-[10px] text-zinc-400 uppercase font-bold">
<span>Mon</span>
<span>Tue</span>
<span>Wed</span>
<span>Thu</span>
<span>Fri</span>
<span>Sat</span>
<span>Sun</span>
</div>
</div>
<!-- Active Alerts -->
<div class="space-y-4">
<h3 class="text-sm font-bold text-on-surface">Operational Alerts</h3>
<div class="bg-tertiary-container/5 p-4 rounded-xl flex gap-3">
<div class="bg-tertiary-container/10 p-2 rounded-lg h-fit">
<span class="material-symbols-outlined text-tertiary text-sm" data-icon="warning">warning</span>
</div>
<div>
<p class="text-xs font-bold text-on-surface">Bus Maintenance Due</p>
<p class="text-[10px] text-on-surface-variant mt-1">Bus #ZB-882 requires engine check within 24 hours.</p>
</div>
</div>
<div class="bg-secondary-container/5 p-4 rounded-xl flex gap-3">
<div class="bg-secondary-container/10 p-2 rounded-lg h-fit">
<span class="material-symbols-outlined text-secondary-container text-sm" data-icon="groups">groups</span>
</div>
<div>
<p class="text-xs font-bold text-on-surface">High Demand Route</p>
<p class="text-[10px] text-on-surface-variant mt-1">Lusaka-Ndola for tomorrow is already 90% booked. Add extra trip?</p>
</div>
</div>
</div>
<!-- Map Widget -->
<div class="bg-surface-container-lowest rounded-xl shadow-sm shadow-on-surface/5 p-4">
<div class="flex items-center justify-between mb-3">
<h3 class="text-xs font-bold text-on-surface">Fleet Location</h3>
<span class="text-[10px] bg-green-100 text-green-800 px-2 py-0.5 rounded-full font-bold">LIVE</span>
</div>
<div class="h-32 bg-zinc-100 rounded-lg overflow-hidden relative">
<img class="w-full h-full object-cover" data-alt="minimalist map view of a city grid with clean white and grey tones and subtle green markers indicating vehicle locations" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBLxYylGOMvilkWfbg5QPBR0S1RM4xETe3r8XcSUibjV6c_m7Y0AeoYtQ_rX7GJoNV7ViTgRwN_yi252CbBqjCeqvVpegvNUF1px9-DOXVkisw4SRADB_iqkHGpnA1wx2TfhIteTdXvajHGZOnUSfgQyGXI0kPzAMV4pFFgQOhM9HfTx_ZXeHLkfmKB66oq7Xnc_QhMHHHPEXv7vPh08CR4CeM9O1QWwINL2YsF_GE55Crq1m_xg8eowkOLqVIZed7TyMKxvpK1FFRO"/>
<!-- Markers -->
<div class="absolute top-1/4 left-1/3 w-3 h-3 bg-primary border-2 border-white rounded-full"></div>
<div class="absolute bottom-1/3 right-1/4 w-3 h-3 bg-primary border-2 border-white rounded-full"></div>
</div>
</div>
</aside>
</div>
<!-- Footer (Shared Component Adaptation) -->
<footer class="w-full py-12 mt-16 border-t border-zinc-200 dark:border-zinc-800 font-inter text-xs text-zinc-500 dark:text-zinc-400">
<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
<div>
<span class="font-manrope font-bold text-zinc-900 dark:text-zinc-100 block mb-2">BookMyBus Zambia</span>
<p>© 2024 BookMyBus Zambia. Premium Travel Excellence.</p>
</div>
<div class="flex md:justify-end gap-6">
<a class="hover:text-zinc-800 dark:hover:text-zinc-200 transition-opacity hover:opacity-80 underline underline-offset-4" href="#">Privacy Policy</a>
<a class="hover:text-zinc-800 dark:hover:text-zinc-200 transition-opacity hover:opacity-80 underline underline-offset-4" href="#">Terms of Service</a>
<a class="hover:text-zinc-800 dark:hover:text-zinc-200 transition-opacity hover:opacity-80 underline underline-offset-4" href="#">Carrier Partners</a>
<a class="hover:text-zinc-800 dark:hover:text-zinc-200 transition-opacity hover:opacity-80 underline underline-offset-4" href="#">Contact Us</a>
</div>
</div>
</footer>
</main>
</body></html>