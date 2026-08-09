<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers | BookMyBus Zambia</title>
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.customers.index') }}"><span class="material-symbols-outlined">people</span><span>Customers</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.revenue') }}"><span class="material-symbols-outlined">payments</span><span>Revenue</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.audit-log.index') }}"><span class="material-symbols-outlined">history</span><span>Audit Log</span></a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="{{ route('operator.profile') }}"><span class="material-symbols-outlined">settings</span><span>Settings</span></a>
        </div>
    </aside>
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Customers</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-sm">person</span></div>
            </div>
        </header>
        <div class="p-8">
            <!-- Stats -->
            <div class="grid grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($totalCustomers) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Customers</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ number_format($totalCustomerBookings) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Bookings</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($newThisMonth) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">New This Month</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-tertiary">{{ number_format($repeatCustomers) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Repeat Customers</p>
                </div>
            </div>
            <!-- Search -->
            <div class="flex justify-end mb-6">
                <form method="GET" action="{{ route('operator.customers.index') }}" class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone..."
                           class="pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-full w-72 text-sm focus:ring-2 focus:ring-primary transition-all">
                </form>
            </div>
            <!-- Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Name</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Email</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Phone</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Bookings</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Joined</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($customers as $customer)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-primary text-sm">person</span>
                                        </div>
                                        <span class="font-medium">{{ $customer->full_name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-sm text-on-surface-variant">{{ $customer->email }}</td>
                                <td class="px-5 py-4 text-sm">{{ $customer->phone_number ?? '—' }}</td>
                                <td class="px-5 py-4">
                                    <span class="font-bold">{{ $customer->bookings_count }}</span>
                                </td>
                                <td class="px-5 py-4 text-sm text-on-surface-variant">{{ $customer->created_at->format('d M Y') }}</td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('operator.customers.show', $customer->id) }}"
                                       class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-block">
                                        <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-16 text-center">
                                <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">people</span>
                                <p class="font-medium text-on-surface-variant">No customers found.</p>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-sm text-on-surface-variant">Showing {{ $customers->firstItem() ?? 0 }} - {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</span>
                    <div class="flex items-center gap-1">
                        @if($customers->previousPageUrl())
                        <a href="{{ $customers->previousPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_left</span></a>
                        @endif
                        <span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-sm font-bold">{{ $customers->currentPage() }}</span>
                        @if($customers->nextPageUrl())
                        <a href="{{ $customers->nextPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_right</span></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>