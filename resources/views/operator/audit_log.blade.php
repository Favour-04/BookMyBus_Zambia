<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.bookings.index') }}"><span class="material-symbols-outlined">book_online</span><span>All Bookings</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.customers.index') }}"><span class="material-symbols-outlined">people</span><span>Customers</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.revenue') }}"><span class="material-symbols-outlined">payments</span><span>Revenue</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.audit-log.index') }}"><span class="material-symbols-outlined">history</span><span>Audit Log</span></a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="{{ route('operator.profile') }}"><span class="material-symbols-outlined">settings</span><span>Settings</span></a>
        </div>
    </aside>
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Audit Log</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-sm">history</span></div>
            </div>
        </header>
        <div class="p-8">
            <!-- Stats -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($totalActions) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Actions</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ number_format($actionsToday) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Actions Today</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($uniqueEvents) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Unique Events</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-sm font-headline font-extrabold truncate text-on-surface">{{ $lastActivity ? $lastActivity->created_at->diffForHumans() : 'N/A' }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Last Activity</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4 mb-6">
                <form method="GET" action="{{ route('operator.audit-log.index') }}" class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="text-xs font-bold text-on-surface-variant uppercase mb-1 block">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search descriptions..."
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    </div>
                    <div class="w-44">
                        <label class="text-xs font-bold text-on-surface-variant uppercase mb-1 block">Event Type</label>
                        <select name="event" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                            <option value="">All Events</option>
                            @foreach($eventTypes as $type)
                                <option value="{{ $type }}" {{ request('event') === $type ? 'selected' : '' }}>{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-40">
                        <label class="text-xs font-bold text-on-surface-variant uppercase mb-1 block">From</label>
                        <input type="date" name="from" value="{{ request('from') }}"
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    </div>
                    <div class="w-40">
                        <label class="text-xs font-bold text-on-surface-variant uppercase mb-1 block">To</label>
                        <input type="date" name="to" value="{{ request('to') }}"
                               class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/15 rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-lg text-sm font-bold hover:bg-primary/90 transition-colors">Filter</button>
                        <a href="{{ route('operator.audit-log.index') }}" class="px-4 py-2 bg-surface-container border border-outline-variant/15 rounded-lg text-sm font-bold text-on-surface-variant hover:bg-surface-container-highest transition-colors">Clear</a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Timestamp</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Event</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Description</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">IP Address</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Details</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($logs as $log)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4 text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold
                                        @php
                                            $eventColor = match(true) {
                                                str_contains($log->event, 'login') || str_contains($log->event, 'logout') => 'bg-primary/10 text-primary',
                                                str_contains($log->event, 'cancelled') || str_contains($log->event, 'deleted') => 'bg-error-container/20 text-error',
                                                str_contains($log->event, 'created') => 'bg-tertiary/10 text-tertiary',
                                                str_contains($log->event, 'updated') || str_contains($log->event, 'changed') => 'bg-amber-100 text-amber-800',
                                                str_contains($log->event, 'viewed') || str_contains($log->event, 'exported') => 'bg-sky-100 text-sky-800',
                                                default => 'bg-surface-container text-on-surface-variant',
                                            };
                                        @endphp
                                    ">{{ $log->eventLabel() }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm max-w-xs truncate" title="{{ $log->description }}">{{ $log->description ?? '—' }}</td>
                                <td class="px-5 py-4 text-sm font-mono text-on-surface-variant">{{ $log->ip_address ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    @if($log->old_values || $log->new_values)
                                        <span class="inline-flex items-center gap-1 text-xs text-on-surface-variant">
                                            <span class="material-symbols-outlined text-xs">database</span>
                                            Data
                                        </span>
                                    @else
                                        <span class="text-xs text-outline">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('operator.audit-log.show', $log->id) }}"
                                       class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-block">
                                        <span class="material-symbols-outlined" style="font-size:18px">open_in_new</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-16 text-center">
                                <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">history</span>
                                <p class="font-medium text-on-surface-variant">No audit log entries found.</p>
                                <p class="text-sm text-on-surface-variant mt-1">Actions performed in the portal will appear here.</p>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-sm text-on-surface-variant">Showing {{ $logs->firstItem() ?? 0 }} - {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries</span>
                    <div class="flex items-center gap-1">
                        @if($logs->previousPageUrl())
                        <a href="{{ $logs->previousPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_left</span></a>
                        @endif
                        <span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-sm font-bold">{{ $logs->currentPage() }}</span>
                        @if($logs->nextPageUrl())
                        <a href="{{ $logs->nextPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_right</span></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>