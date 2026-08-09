<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard — BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#00601f", "primary-container": "#197b30",
                        "on-primary": "#ffffff", surface: "#f9f9fc",
                        "surface-container-low": "#f3f3f6", "surface-container-lowest": "#ffffff",
                        "on-surface": "#1a1c1e", "on-surface-variant": "#40493e",
                        outline: "#6f7a6c", "outline-variant": "#bfcaba",
                        error: "#ba1a1a", "error-container": "#ffdad6",
                        tertiary: "#7c0400",
                    },
                    fontFamily: { headline: ["Manrope"], body: ["Inter"] },
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background: #f9f9fc; }
    </style>
</head>
<body class="bg-surface text-on-surface">

    <!-- Sidebar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline text-xl font-extrabold text-primary uppercase tracking-tighter">BookMyBus</h1>
            <p class="text-xs text-on-surface-variant opacity-70">Admin Portal</p>
        </div>
        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
            </a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-error transition-colors w-full">
                    <span class="material-symbols-outlined">logout</span><span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Dashboard</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">admin_panel_settings</span>
                </div>
            </div>
        </header>

        <div class="p-8">

            @if(session('status'))
                <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary mb-6">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Users</p>
                            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['total_users']) }}</p>
                        </div>
                        <span class="material-symbols-outlined text-primary text-3xl">group</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Operators</p>
                            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['total_operators']) }}</p>
                        </div>
                        <span class="material-symbols-outlined text-primary text-3xl">directions_bus</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Pending Verifications</p>
                            <p class="font-headline font-extrabold text-3xl mt-2 {{ $stats['pending_verifications'] > 0 ? 'text-tertiary' : '' }}">{{ number_format($stats['pending_verifications']) }}</p>
                        </div>
                        <span class="material-symbols-outlined text-primary text-3xl">verified_user</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Bookings</p>
                            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['total_bookings']) }}</p>
                        </div>
                        <span class="material-symbols-outlined text-primary text-3xl">book_online</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Confirmed Bookings</p>
                            <p class="font-headline font-extrabold text-3xl mt-2">{{ number_format($stats['confirmed_bookings']) }}</p>
                        </div>
                        <span class="material-symbols-outlined text-primary text-3xl">check_circle</span>
                    </div>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Total Revenue</p>
                            <p class="font-headline font-extrabold text-3xl mt-2">ZMW {{ number_format($stats['total_revenue'], 2) }}</p>
                        </div>
                        <span class="material-symbols-outlined text-primary text-3xl">payments</span>
                    </div>
                </div>
            </div>

            <!-- Recent Operators -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-8">
                <h3 class="font-headline font-bold text-lg mb-4">Recent Operators</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-outline-variant/20">
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Company</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Email</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_operators as $operator)
                            <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                                <td class="py-3 px-2 font-bold">{{ $operator->company_name ?? $operator->name ?? 'N/A' }}</td>
                                <td class="py-3 px-2">{{ $operator->email }}</td>
                                <td class="py-3 px-2">
                                    @if($operator->is_verified)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary">Verified</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-tertiary/10 text-tertiary">Pending</span>
                                    @endif
                                </td>
                                <td class="py-3 px-2 text-on-surface-variant">{{ $operator->created_at->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-on-surface-variant">No operators registered yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                <h3 class="font-headline font-bold text-lg mb-4">Recent Bookings</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-outline-variant/20">
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Reference</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Passenger</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Route</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Amount</th>
                                <th class="text-left py-3 px-2 font-bold text-on-surface-variant text-[10px] uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_bookings as $booking)
                            <tr class="border-b border-outline-variant/10 hover:bg-surface-container-low">
                                <td class="py-3 px-2 font-mono text-xs">{{ $booking->reference_id }}</td>
                                <td class="py-3 px-2">{{ $booking->passenger_name ?? $booking->user->full_name ?? 'Guest' }}</td>
                                <td class="py-3 px-2">{{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}</td>
                                <td class="py-3 px-2 font-bold">ZMW {{ number_format($booking->amount, 2) }}</td>
                                <td class="py-3 px-2">
                                    @php
                                        $badgeClass = match($booking->status) {
                                            'confirmed' => 'bg-primary/10 text-primary',
                                            'pending' => 'bg-tertiary/10 text-tertiary',
                                            'cancelled' => 'bg-error/10 text-error',
                                            default => 'bg-surface-container-high text-on-surface-variant',
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $badgeClass }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-on-surface-variant">No bookings yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>