<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Calendar | {{ $currentDate->format('F Y') }} | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
        .calendar-cell { min-height: 100px; }
        .trip-chip { transition: all 0.15s ease; }
        .trip-chip:hover { transform: translateY(-1px); box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
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

    <!-- Sidebar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline text-xl font-extrabold text-primary uppercase tracking-tighter">{{ $operator->company_name ?? 'Operator' }}</h1>
            <p class="text-xs text-on-surface-variant opacity-70">Operator Portal</p>
        </div>
        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.trips.index') }}">
                <span class="material-symbols-outlined">directions_bus</span><span>Manage Trips</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.trips.calendar') }}">
                <span class="material-symbols-outlined">calendar_month</span><span>Trip Calendar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.bookings.index') }}">
                <span class="material-symbols-outlined">book_online</span><span>All Bookings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.revenue') }}">
                <span class="material-symbols-outlined">payments</span><span>Revenue</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.audit-log.index') }}">
                <span class="material-symbols-outlined">history</span><span>Audit Log</span>
            </a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="{{ route('operator.profile') }}">
                <span class="material-symbols-outlined">settings</span><span>Settings</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Trip Calendar</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">person</span>
                </div>
            </div>
        </header>

        <div class="p-8">

            <!-- Month Navigation & Summary -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('operator.trips.calendar', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                       class="p-2 rounded-xl text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">chevron_left</span>
                    </a>
                    <h3 class="font-headline text-2xl font-extrabold text-on-surface">{{ $currentDate->format('F Y') }}</h3>
                    <a href="{{ route('operator.trips.calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                       class="p-2 rounded-xl text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">chevron_right</span>
                    </a>
                    <a href="{{ route('operator.trips.calendar') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                        Today
                    </a>
                </div>
                <div class="flex items-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-primary/20 border border-primary/40"></span>
                        <span class="text-on-surface-variant">Scheduled</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-tertiary/20 border border-tertiary/40"></span>
                        <span class="text-on-surface-variant">On Route</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-surface-container-highest border border-outline-variant"></span>
                        <span class="text-on-surface-variant">Completed</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-error-container/30 border border-error/40"></span>
                        <span class="text-on-surface-variant">Cancelled</span>
                    </div>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-4 gap-4 mb-6">
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ $totalTrips }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Trips</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ $totalBookings }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Bookings</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold">ZMW {{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Revenue</p>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ $currentDate->format('F') }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">{{ $currentDate->format('Y') }}</p>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <!-- Day headers -->
                <div class="grid grid-cols-7 border-b border-outline-variant/15">
                    @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                    <div class="px-3 py-3 text-center text-[10px] font-bold uppercase text-on-surface-variant tracking-wider bg-surface-container-low">
                        {{ $day }}
                    </div>
                    @endforeach
                </div>

                <!-- Calendar weeks -->
                <div class="divide-y divide-outline-variant/10">
                    @foreach($calendar as $week)
                    <div class="grid grid-cols-7">
                        @foreach($week as $cell)
                            @if($cell === null)
                                <div class="calendar-cell p-2 bg-surface-container-low/30 border-r border-outline-variant/5"></div>
                            @else
                                @php
                                    $cellBg = $cell['is_today'] ? 'bg-primary/5' : 'bg-surface-container-lowest';
                                    $cellBorder = $cell['is_today'] ? 'border-2 border-primary/30' : 'border-r border-outline-variant/10';
                                @endphp
                                <div class="calendar-cell p-2 {{ $cellBg }} {{ $cellBorder }} relative">
                                    <!-- Day number -->
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-sm font-bold {{ $cell['is_today'] ? 'text-primary' : 'text-on-surface' }}">
                                            {{ $cell['day'] }}
                                        </span>
                                        @if($cell['trip_count'] > 0)
                                        <span class="text-[10px] font-bold text-on-surface-variant bg-surface-container px-1.5 py-0.5 rounded-full">
                                            {{ $cell['trip_count'] }}
                                        </span>
                                        @endif
                                    </div>

                                    <!-- Trip chips -->
                                    <div class="space-y-1 max-h-[80px] overflow-y-auto custom-scrollbar">
                                        @foreach($cell['trips'] as $trip)
                                            @php
                                                $chipColor = match($trip['status_type']) {
                                                    'scheduled' => 'bg-primary/10 text-primary border-primary/20',
                                                    'on_route' => 'bg-tertiary/10 text-tertiary border-tertiary/20',
                                                    'completed' => 'bg-surface-container-high text-on-surface-variant border-outline-variant/30',
                                                    'cancelled' => 'bg-error-container/20 text-error border-error/20',
                                                    default => 'bg-surface-container text-on-surface-variant border-outline-variant/20',
                                                };
                                            @endphp
                                            <a href="{{ route('operator.trips.bookings', $trip['id']) }}"
                                               class="trip-chip block px-1.5 py-1 rounded-md border text-[10px] leading-tight {{ $chipColor }} truncate">
                                                <span class="font-bold">{{ $trip['departure'] }}</span>
                                                <span class="ml-0.5">{{ $trip['origin'] }}→{{ $trip['destination'] }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Legend & Quick Links -->
            <div class="flex items-center justify-between mt-6">
                <div class="flex items-center gap-4 text-xs text-on-surface-variant">
                    <span>Click a trip chip to view its bookings</span>
                    <span>•</span>
                    <span>Today is highlighted in green</span>
                </div>
                <a href="{{ route('operator.trips.index') }}"
                   class="flex items-center gap-1 text-sm font-bold text-primary hover:underline">
                    <span class="material-symbols-outlined text-sm">list_alt</span>
                    List View
                </a>
            </div>

        </div>
    </main>

</body>
</html>