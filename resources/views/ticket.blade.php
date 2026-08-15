<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Ticket | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
        }
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
                        "outline": "#6f7a6c", "tertiary": "#7c0400", "error": "#ba1a1a",
                        "error-container": "#ffdad6", "secondary": "#954a00",
                    },
                    fontFamily: { 'headline': ['Manrope', 'sans-serif'], 'body': ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-surface text-on-surface min-h-screen">
    <!-- Navigation Bar -->
    <header class="h-16 bg-surface-container-low border-b border-outline-variant/15 flex items-center px-8 sticky top-0 z-20 no-print">
        <a href="{{ route('home') }}" class="font-headline text-xl font-extrabold text-primary tracking-tighter">
            BookMyBus<span class="text-on-surface"> Zambia</span>
        </a>
        <nav class="ml-auto flex items-center gap-6">
            <a href="{{ route('trips.search') }}" class="text-sm font-bold text-on-surface-variant hover:text-primary transition-colors">Book a Trip</a>
            <a href="{{ route('booking.lookup') }}" class="text-sm font-bold text-on-surface-variant hover:text-primary transition-colors">My Booking</a>
        </nav>
    </header>

    <main class="max-w-2xl mx-auto px-4 py-10">
        @php
            $route = $booking->route;
        @endphp

        <!-- Heading -->
        <div class="text-center mb-8">
            <span class="material-symbols-outlined text-6xl text-primary block mb-3">confirmation_number</span>
            <h1 class="font-headline text-3xl font-extrabold text-on-surface">Your Digital Ticket</h1>
            <p class="text-on-surface-variant mt-1 text-sm">Present this QR code at boarding.</p>
        </div>

        <!-- Ticket card -->
        <div class="bg-surface-container-lowest rounded-2xl border-2 border-primary overflow-hidden shadow-lg">
            <!-- Header -->
            <div class="bg-primary/5 p-6 border-b border-outline-variant/15">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase bg-primary/10 text-primary">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                    <span class="font-mono text-sm font-bold text-on-surface-variant">{{ $booking->reference_id }}</span>
                </div>
            </div>

            <!-- Body -->
            <div class="p-6 space-y-6">
                <!-- Route -->
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-on-surface-variant">From</p>
                        <p class="font-headline font-extrabold text-xl">{{ $route->origin }}</p>
                    </div>
                    <span class="material-symbols-outlined text-primary">arrow_forward</span>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-on-surface-variant">To</p>
                        <p class="font-headline font-extrabold text-xl">{{ $route->destination }}</p>
                    </div>
                </div>

                <!-- Passenger & Seat -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-dashed border-outline-variant/20">
                    <div>
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Passenger</p>
                        <p class="font-bold">{{ $booking->passenger_name ?? 'N/A' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Seat</p>
                        <p class="font-headline font-extrabold text-xl text-secondary">#{{ $booking->seat_number }}</p>
                    </div>
                </div>

                <!-- Departure & Amount -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-dashed border-outline-variant/20">
                    <div>
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Departure</p>
                        <p class="font-bold">
                            @if($route->travel_date instanceof \Carbon\Carbon)
                                {{ $route->travel_date->format('d M Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($route->travel_date)->format('d M Y') }}
                            @endif
                        </p>
                        <p class="text-sm text-on-surface-variant">{{ $route->departure_time }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Amount</p>
                        <p class="font-headline font-extrabold text-xl text-primary">ZMW {{ number_format($booking->amount, 2) }}</p>
                    </div>
                </div>

                <!-- Operator & Bus -->
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-dashed border-outline-variant/20">
                    <div>
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Operator</p>
                        <p class="font-bold text-sm">{{ $route->operator->company_name ?? 'BookMyBus Operator' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Bus</p>
                        <p class="font-bold text-sm">{{ $route->bus->registration_number ?? 'N/A' }}</p>
                    </div>
                </div>

                <!-- Issued -->
                <div class="pt-4 border-t border-dashed border-outline-variant/20">
                    <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Issued</p>
                    <p class="text-sm text-on-surface-variant">{{ $ticket->issued_at->format('d M Y, H:i') }}</p>
                </div>

                <!-- QR Code -->
                <div class="flex flex-col items-center pt-4 border-t border-dashed border-outline-variant/20">
                    <div class="p-3 bg-surface-container-low rounded-xl">
                        <img alt="Ticket QR Code" class="w-32 h-32"
                             src="https://api.qrserver.com/v1/create-qr-code/?size=128x128&data={{ urlencode($ticket->qr_code) }}" />
                    </div>
                    <p class="mt-3 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest">Scan at boarding</p>
                    <p class="mt-1 text-[10px] text-on-surface-variant break-all">{{ $ticket->qr_code }}</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex gap-4 justify-center no-print">
            <a href="{{ route('trips.search') }}"
               class="px-6 py-3 border border-outline-variant/30 rounded-xl text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors">
                Book Another Trip
            </a>
            <button onclick="window.print()"
                    class="px-6 py-3 bg-primary text-on-primary rounded-xl text-sm font-bold hover:brightness-110 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">print</span>
                Print Ticket
            </button>
        </div>
    </main>
</body>
</html>