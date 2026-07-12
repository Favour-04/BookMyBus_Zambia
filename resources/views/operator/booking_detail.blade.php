<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking #{{ $booking->reference_id }} | BookMyBus Zambia</title>
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.bookings.index') }}">
                <span class="material-symbols-outlined">book_online</span><span>All Bookings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="#">
                <span class="material-symbols-outlined">payments</span><span>Revenue</span>
            </a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="#">
                <span class="material-symbols-outlined">settings</span><span>Settings</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Booking Details</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">person</span>
                </div>
            </div>
        </header>

        <div class="p-8">

            <!-- Back link -->
            <a href="{{ route('operator.bookings.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to All Bookings
            </a>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column: Booking Info -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Status & Reference Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider">Reference ID</p>
                                <p class="font-headline text-2xl font-extrabold mt-1">{{ $booking->reference_id }}</p>
                            </div>
                            @php
                                $statusColor = match($booking->status) {
                                    'confirmed' => 'bg-primary/10 text-primary',
                                    'pending' => 'bg-tertiary/10 text-tertiary',
                                    'cancelled' => 'bg-error-container/20 text-error',
                                    'expired' => 'bg-surface-container-high text-on-surface-variant',
                                    default => 'bg-surface-container text-on-surface-variant',
                                };
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider {{ $statusColor }}">
                                {{ $booking->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 pt-6 border-t border-dashed border-outline-variant/20">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Seat Number</p>
                                <p class="font-headline font-extrabold text-xl mt-1">#{{ $booking->seat_number }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Amount</p>
                                <p class="font-headline font-extrabold text-xl mt-1">ZMW {{ number_format($booking->amount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Booked On</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->created_at->format('d M Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Held Until</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->held_until ? $booking->held_until->format('d M Y H:i') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Passenger Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Passenger Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Name</p>
                                <p class="font-bold text-base">{{ $booking->passenger_name ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Phone Number</p>
                                <p class="font-bold text-base">{{ $booking->phone_number ?? 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">ID Number</p>
                                <p class="font-bold text-base">{{ $booking->id_number ?? 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trip Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Trip Information</h3>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex-1">
                                <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider">From</p>
                                <p class="font-headline font-extrabold text-xl mt-1">{{ $booking->route->origin }}</p>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="material-symbols-outlined text-outline">arrow_forward</span>
                            </div>
                            <div class="flex-1 text-right">
                                <p class="text-xs font-bold uppercase text-on-surface-variant tracking-wider">To</p>
                                <p class="font-headline font-extrabold text-xl mt-1">{{ $booking->route->destination }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-6 border-t border-dashed border-outline-variant/20">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Travel Date</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->travel_date instanceof \Carbon\Carbon ? $booking->route->travel_date->format('d M Y') : \Carbon\Carbon::parse($booking->route->travel_date)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Departure</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->departure_time }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Operator</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->operator->company_name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider">Bus</p>
                                <p class="font-bold text-sm mt-1">{{ $booking->route->bus->registration_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Payment Information</h3>
                        @if($booking->payment)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Payment Method</p>
                                    <p class="font-bold text-base">{{ str_replace('_', ' ', ucfirst($booking->payment->payment_method)) }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Transaction Reference</p>
                                    <p class="font-mono font-bold text-sm">{{ $booking->payment->transaction_reference ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Payment Status</p>
                                    @php
                                        $paymentStatusColor = match($booking->payment->status) {
                                            'successful' => 'bg-primary/10 text-primary',
                                            'pending' => 'bg-tertiary/10 text-tertiary',
                                            'failed' => 'bg-error-container/20 text-error',
                                            'refunded' => 'bg-surface-container-high text-on-surface-variant',
                                            default => 'bg-surface-container text-on-surface-variant',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $paymentStatusColor }}">
                                        {{ ucfirst($booking->payment->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1">Paid At</p>
                                    <p class="font-bold text-sm">{{ $booking->payment->paid_at ? $booking->payment->paid_at->format('d M Y H:i') : 'N/A' }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center gap-3 py-4">
                                <span class="material-symbols-outlined text-outline-variant">payments</span>
                                <p class="text-on-surface-variant">No payment record found for this booking.</p>
                            </div>
                        @endif
                    </div>

                </div>

                <!-- Right Column: Actions -->
                <div class="space-y-6">

                    <!-- Actions Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('operator.trips.bookings', $booking->route_id) }}"
                               class="flex items-center gap-3 w-full px-4 py-3 bg-surface-container-low rounded-xl text-sm font-bold text-on-surface hover:bg-surface-container-highest transition-colors">
                                <span class="material-symbols-outlined text-primary">group</span>
                                View All Trip Bookings
                            </a>
                            <a href="{{ route('operator.trips.seat-map', $booking->route_id) }}"
                               class="flex items-center gap-3 w-full px-4 py-3 bg-surface-container-low rounded-xl text-sm font-bold text-on-surface hover:bg-surface-container-highest transition-colors">
                                <span class="material-symbols-outlined text-primary">event_seat</span>
                                View Seat Map
                            </a>
                            @if($booking->status === 'pending' && !$booking->isExpired())
                            <form method="POST" action="{{ route('operator.trips.cancel', $booking->route_id) }}" onsubmit="return confirm('Cancel this booking? This cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-3 bg-error-container/10 rounded-xl text-sm font-bold text-error hover:bg-error-container/20 transition-colors">
                                    <span class="material-symbols-outlined text-error">cancel</span>
                                    Cancel Booking
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Info Card -->
                    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                        <h3 class="font-headline font-bold text-lg mb-4">Quick Info</h3>
                        <div class="space-y-4 text-sm">
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Reference</span>
                                <span class="font-mono font-bold">{{ $booking->reference_id }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Status</span>
                                <span class="font-bold">{{ ucfirst($booking->status) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Seat</span>
                                <span class="font-bold">#{{ $booking->seat_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Amount</span>
                                <span class="font-bold text-primary">ZMW {{ number_format($booking->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-on-surface-variant">Age</span>
                                <span class="font-bold">{{ $booking->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </main>

</body>
</html>