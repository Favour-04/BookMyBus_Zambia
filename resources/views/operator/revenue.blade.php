<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue & Reports | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
        .chart-bar { transition: height 0.4s ease; }
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.trips.index') }}">
                <span class="material-symbols-outlined">directions_bus</span><span>Manage Trips</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.bookings.index') }}">
                <span class="material-symbols-outlined">book_online</span><span>All Bookings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.revenue') }}">
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
            <h2 class="font-headline text-base font-bold text-primary">Revenue & Reports</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">person</span>
                </div>
            </div>
        </header>

        <div class="p-8">

            <!-- Period Filter -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1">
                    @php $currentPeriod = request('period', 'this_month'); @endphp
                    @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', 'this_week' => 'This Week', 'this_month' => 'This Month', 'last_month' => 'Last Month', 'this_year' => 'This Year'] as $val => $label)
                    <a href="{{ route('operator.revenue', ['period' => $val]) }}"
                       class="px-3 py-1.5 rounded-lg text-sm font-bold transition-all
                              {{ ($currentPeriod === $val) ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
                <span class="text-xs text-on-surface-variant">
                    <span class="font-bold">{{ Carbon\Carbon::parse($dateFrom)->format('d M Y') }}</span>
                    —
                    <span class="font-bold">{{ Carbon\Carbon::parse($dateTo)->format('d M Y') }}</span>
                </span>
            </div>

            <!-- Session Messages -->
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-primary/5 border border-primary/10 rounded-xl text-primary font-bold text-sm">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
            @endif

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">payments</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Period Revenue</p>
                            <p class="font-headline text-2xl font-extrabold text-primary">ZMW {{ number_format($periodRevenue, 2) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-on-surface-variant">
                        <span>{{ $confirmedCount }} confirmed bookings</span>
                        <span class="mx-1">•</span>
                        <span>{{ $tripsCount }} trips</span>
                    </div>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 rounded-xl bg-primary/5 flex items-center justify-center">
                            <span class="material-symbols-outlined text-primary">trending_up</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Avg per Trip</p>
                            <p class="font-headline text-2xl font-extrabold">ZMW {{ number_format($avgPerTrip, 2) }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-on-surface-variant">Revenue divided by total trips</div>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 rounded-xl bg-tertiary/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-tertiary">hourglass_bottom</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Pending Revenue</p>
                            <p class="font-headline text-2xl font-extrabold text-tertiary">ZMW {{ number_format($pendingAmount, 2) }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-on-surface-variant">Unconfirmed bookings awaiting payment</div>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="h-10 w-10 rounded-xl bg-error-container/20 flex items-center justify-center">
                            <span class="material-symbols-outlined text-error">cancel</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Cancelled</p>
                            <p class="font-headline text-2xl font-extrabold text-error">ZMW {{ number_format($cancelledAmount, 2) }}</p>
                        </div>
                    </div>
                    <div class="text-xs text-on-surface-variant">Total value of cancelled bookings</div>
                </div>
            </div>

            <!-- Additional KPIs Row -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Revenue (All Time)</p>
                        <p class="font-headline font-extrabold text-lg mt-1">ZMW {{ number_format($totalRevenue, 2) }}</p>
                    </div>
                    <span class="material-symbols-outlined text-outline-variant text-3xl">account_balance</span>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Today's Revenue</p>
                        <p class="font-headline font-extrabold text-lg mt-1">ZMW {{ number_format($revenueToday, 2) }}</p>
                    </div>
                    <span class="material-symbols-outlined text-outline-variant text-3xl">today</span>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Top Route</p>
                        <p class="font-headline font-extrabold text-sm mt-1 truncate max-w-[180px]">{{ $topRouteName }}</p>
                    </div>
                    <span class="material-symbols-outlined text-outline-variant text-3xl">route</span>
                </div>
                <div class="bg-surface-container-lowest rounded-xl border border-outline-variant/15 p-4 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Top Route Revenue</p>
                        <p class="font-headline font-extrabold text-lg mt-1">ZMW {{ number_format($topRouteRevenue, 2) }}</p>
                    </div>
                    <span class="material-symbols-outlined text-outline-variant text-3xl">emoji_events</span>
                </div>
            </div>

            <!-- Daily Revenue Chart & Payment Methods -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Daily Revenue Chart -->
                <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h3 class="font-headline font-bold text-lg mb-4">Daily Revenue</h3>
                    <div class="flex items-end gap-1.5 h-48" style="min-height: 12rem;">
                        @foreach($dailyRevenue as $day)
                        <div class="flex-1 flex flex-col items-center justify-end h-full">
                            <div class="w-full bg-primary/10 rounded-t-md relative group cursor-pointer"
                                 style="height: {{ $day['total'] > 0 ? max(4, ($day['total'] / $chartMax) * 100) : 2 }}%; min-height: 4px;"
                                 title="ZMW {{ number_format($day['total'], 2) }}">
                                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-on-surface text-surface text-[10px] font-bold px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    ZMW {{ number_format($day['total'], 0) }}
                                </div>
                            </div>
                            <span class="text-[9px] text-on-surface-variant mt-1 {{ strlen($day['date']) > 5 ? 'text-[7px]' : '' }}">{{ $day['date'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Revenue by Payment Method -->
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h3 class="font-headline font-bold text-lg mb-4">Payment Methods</h3>
                    @if(count($revenueByMethod) > 0)
                        @php $totalMethodRevenue = collect($revenueByMethod)->sum('total'); @endphp
                        <div class="space-y-4">
                            @foreach($revenueByMethod as $method)
                            @php $pct = $totalMethodRevenue > 0 ? round(($method['total'] / $totalMethodRevenue) * 100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-sm font-medium">{{ $method['method'] }}</span>
                                    <span class="text-sm font-bold">ZMW {{ number_format($method['total'], 2) }}</span>
                                </div>
                                <div class="h-2.5 bg-surface-container-highest rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width: {{ $pct }}%"></div>
                                </div>
                                <div class="flex justify-between text-[10px] text-on-surface-variant mt-0.5">
                                    <span>{{ $method['count'] }} transaction(s)</span>
                                    <span>{{ $pct }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-dashed border-outline-variant/20 flex justify-between">
                            <span class="text-sm font-bold">Total</span>
                            <span class="text-sm font-bold text-primary">ZMW {{ number_format($totalMethodRevenue, 2) }}</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center py-8">
                            <span class="material-symbols-outlined text-4xl text-outline-variant mb-2">credit_card</span>
                            <p class="text-sm text-on-surface-variant">No payment data for this period.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Revenue by Route & Recent Transactions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Revenue by Route -->
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h3 class="font-headline font-bold text-lg mb-4">Top Routes</h3>
                    @if(count($revenueByRoute) > 0)
                        @php $totalRouteRevenue = collect($revenueByRoute)->sum('revenue'); @endphp
                        <div class="space-y-3">
                            @foreach($revenueByRoute as $index => $route)
                            @php $routePct = $totalRouteRevenue > 0 ? round(($route['revenue'] / $totalRouteRevenue) * 100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-[10px] font-bold text-on-surface-variant w-4">{{ $index + 1 }}.</span>
                                        <span class="text-sm font-medium truncate">{{ $route['route'] }}</span>
                                    </div>
                                    <span class="text-sm font-bold">ZMW {{ number_format($route['revenue'], 0) }}</span>
                                </div>
                                <div class="h-2 bg-surface-container-highest rounded-full overflow-hidden ml-6">
                                    <div class="h-full rounded-full {{ $index === 0 ? 'bg-primary' : ($index === 1 ? 'bg-tertiary' : 'bg-outline-variant') }}"
                                         style="width: {{ $routePct }}%"></div>
                                </div>
                                <div class="text-[10px] text-on-surface-variant ml-6 mt-0.5">{{ $route['bookings'] }} bookings</div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center py-8">
                            <span class="material-symbols-outlined text-4xl text-outline-variant mb-2">signpost</span>
                            <p class="text-sm text-on-surface-variant">No route revenue for this period.</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Transactions -->
                <div class="lg:col-span-2 bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-headline font-bold text-lg">Recent Transactions</h3>
                        <a href="{{ route('operator.bookings.index') }}" class="text-xs font-bold text-primary hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-outline-variant/15">
                                    <th class="pb-3 text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Ref</th>
                                    <th class="pb-3 text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Passenger</th>
                                    <th class="pb-3 text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Route</th>
                                    <th class="pb-3 text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Method</th>
                                    <th class="pb-3 text-[10px] font-bold uppercase text-on-surface-variant tracking-wider text-right">Amount</th>
                                    <th class="pb-3 text-[10px] font-bold uppercase text-on-surface-variant tracking-wider text-right">Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-outline-variant/10">
                                @forelse($recentTransactions as $tx)
                                <tr class="hover:bg-surface-container-low/60 transition-colors">
                                    <td class="py-3 pr-3">
                                        <a href="{{ route('operator.bookings.show', $tx['booking_id']) }}" class="font-mono text-xs font-bold text-primary hover:underline">{{ $tx['reference'] }}</a>
                                    </td>
                                    <td class="py-3 pr-3 text-sm font-medium">{{ $tx['passenger'] }}</td>
                                    <td class="py-3 pr-3 text-sm text-on-surface-variant max-w-[150px] truncate">{{ $tx['route'] }}</td>
                                    <td class="py-3 pr-3 text-sm text-on-surface-variant">{{ $tx['method'] }}</td>
                                    <td class="py-3 pr-3 text-sm font-bold text-right">ZMW {{ number_format($tx['amount'], 2) }}</td>
                                    <td class="py-3 text-xs text-on-surface-variant text-right whitespace-nowrap">{{ $tx['date'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-sm text-on-surface-variant">No recent transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>