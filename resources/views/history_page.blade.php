<!-- TODO: This view should extend layouts.app when a layout is available -->
<!-- Digital Ticket View - Displays a single confirmed booking -->

<!doctype html>

<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Digital Ticket | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
      href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
      rel="stylesheet"
    />
    <script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "on-secondary": "#ffffff",
              "on-secondary-container": "#632f00",
              "on-secondary-fixed": "#301400",
              "inverse-surface": "#2f3133",
              "on-primary-container": "#b1ffb1",
              "secondary-container": "#ff8921",
              "primary-fixed-dim": "#7edb83",
              "inverse-primary": "#7edb83",
              background: "#f9f9fc",
              "inverse-on-surface": "#f0f0f3",
              "tertiary-container": "#d1200f",
              "secondary-fixed": "#ffdcc6",
              "surface-container-lowest": "#ffffff",
              "outline-variant": "#bfcaba",
              "on-tertiary": "#ffffff",
              surface: "#f9f9fc",
              "on-primary": "#ffffff",
              "primary-fixed": "#99f89d",
              "surface-variant": "#e2e2e5",
              "secondary-fixed-dim": "#ffb784",
              "on-primary-fixed": "#002106",
              "surface-bright": "#f9f9fc",
              "on-error-container": "#93000a",
              "surface-dim": "#dadadc",
              outline: "#6f7a6c",
              "on-secondary-fixed-variant": "#713700",
              primary: "#00601f",
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
              error: "#ba1a1a",
              "surface-container": "#eeeef0",
              secondary: "#954a00",
              tertiary: "#a80800",
              "on-error": "#ffffff",
              "on-background": "#1a1c1e",
            },
            borderRadius: {
              DEFAULT: "0.125rem",
              lg: "0.25rem",
              xl: "0.5rem",
              full: "0.75rem",
            },
            fontFamily: {
              headline: ["Manrope"],
              body: ["Inter"],
              label: ["Inter"],
            },
          },
        },
      };
    </script>
    <style>
      .material-symbols-outlined {
        font-variation-settings:
          "FILL" 0,
          "wght" 400,
          "GRAD" 0,
          "opsz" 24;
      }
      .hero-gradient {
        background: linear-gradient(135deg, #00601f 0%, #197b30 100%);
      }
      .editorial-shadow {
        box-shadow: 0 24px 40px rgba(26, 28, 30, 0.05);
      }
    </style>
  </head>
  <body class="bg-surface font-body text-on-surface">
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md shadow-sm">
      <div
        class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full"
      >
        <div
          class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline"
        >
          <a href="{{ route('home') }}">BookMyBus Zambia</a>
        </div>
        <div class="hidden md:flex items-center gap-8">
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="{{ route('home') }}"
            >Find Trips</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-green-900 border-b-2 border-orange-600 pb-1"
            href="{{ route('booking.lookup') }}"
            >My Bookings</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="{{ route('operator.login') }}"
            >Operator Portal</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="{{ route('support.page') }}"
            >Support</a
          >
        </div>
        <div class="flex items-center gap-4">
          @auth
          <a href="{{ route('profile') }}"
            class="material-symbols-outlined text-zinc-600 cursor-pointer"
            >account_circle</a
          >
          @else
          <a href="{{ route('login') }}"
            class="material-symbols-outlined text-zinc-600 cursor-pointer"
            >account_circle</a
          >
          <a href="{{ route('login') }}"
            class="text-green-800 font-headline font-bold text-sm px-4 py-2 hover:bg-zinc-50 rounded-lg transition-colors"
          >
            Sign In
          </a>
          @endauth
        </div>
      </div>
    </nav>

    <main class="pt-24 pb-16">
      @php
          $ticket = $booking->ticket ?? null;
          $qrData = $ticket?->qr_code ?? $booking->reference_id;
      @endphp
      @if($booking && $booking->isConfirmed())
      <!-- Payment Successful Banner -->
      <section class="bg-primary-container py-6 mb-8">
        <div class="max-w-7xl mx-auto px-6">
          <div class="flex items-center justify-center gap-3">
            <span class="material-symbols-outlined text-primary text-3xl" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            <h2 class="text-2xl font-extrabold font-headline text-on-primary-container">
              PAYMENT SUCCESSFUL
            </h2>
          </div>
        </div>
      </section>
      @endif

      <!-- Digital Ticket Display -->
      <section class="max-w-3xl mx-auto px-6">
        <div class="relative">
          <!-- Top Notch -->
          <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-surface rounded-b-full z-10"></div>
          
          <div class="bg-surface-container-lowest rounded-2xl shadow-xl overflow-hidden relative">
            <!-- Visual Header -->
            <div class="h-32 relative">
              <img class="w-full h-full object-cover" 
                   data-alt="Modern coach bus driving through a scenic Zambian highway landscape" 
                   src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtACWHf96GQ2Q6lm8qydU0xrhEVka89gUOGcMoH974Us9bOlDAAtmr10nk6iIVJ98jsWJWTii8Y8xLjDrFPGlxmNfkI3FGH_6VBr36Xy3f7LlCdbSg2-0_ZP_SiM83Ez88uCg3arvxEVQaOe61WNm9VIt3cvWqw1dkKoQHxHtajf-ws6BRAPpzQED8jlxcNOuEO_ywfSvtmIzSz9cKzbFuJfqHi-ADDDJ6v4amXeggpOH57W9NqViHzH1pa8mfsF4oaXs1MVj_wgc4"/>
              <div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest to-transparent"></div>
              <div class="absolute bottom-4 left-6">
                <span class="px-3 py-1 bg-primary text-white text-[10px] font-bold uppercase tracking-widest rounded-full">
                  {{ $booking->isConfirmed() ? 'Confirmed' : 'Pending' }}
                </span>
              </div>
            </div>

            <div class="p-8 space-y-8">
              <!-- Passenger & Seat Info -->
              <div class="flex justify-between items-center">
                <div class="space-y-1">
                  <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">Passenger</p>
                  <p class="font-headline font-extrabold text-xl">{{ $booking->passenger_name ?? 'John Mulenga' }}</p>
                </div>
                <div class="text-right space-y-1">
                  <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">Seat</p>
                  <p class="font-headline font-extrabold text-xl text-secondary">{{ $booking->seat_number }}</p>
                </div>
              </div>

              <!-- Route Info -->
              <div class="flex items-center gap-6 justify-between relative">
                <div class="flex-1">
                  <p class="font-black text-2xl font-headline">{{ $booking->route->origin }}</p>
                  <p class="text-xs font-medium text-on-surface-variant">{{ $booking->route->origin }}</p>
                </div>
                <div class="flex flex-col items-center gap-1">
                  <span class="material-symbols-outlined text-primary">directions_bus</span>
                  <div class="h-[2px] w-12 bg-surface-container-highest"></div>
                </div>
                <div class="flex-1 text-right">
                  <p class="font-black text-2xl font-headline">{{ $booking->route->destination }}</p>
                  <p class="text-xs font-medium text-on-surface-variant">{{ $booking->route->destination }}</p>
                </div>
              </div>

              <!-- Departure & Booking ID -->
              <div class="grid grid-cols-2 gap-6 pt-6 border-t border-dashed border-outline-variant">
                <div>
                  <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">Departure</p>
                  <p class="font-bold text-sm">{{ $booking->route->travel_date->format('d M, Y') }}</p>
                  <p class="text-xs text-on-surface-variant">{{ $booking->route->departure_time }}</p>
                </div>
                <div class="text-right">
                  <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">Booking ID</p>
                  <p class="font-bold text-sm">{{ $booking->reference_id }}</p>
                  <p class="text-xs text-on-surface-variant">{{ $booking->route->bus->bus_class ?? 'Premium Class' }}</p>
                </div>
              </div>

              <!-- Operator & Bus Info -->
              <div class="grid grid-cols-2 gap-6 pt-4 border-t border-dashed border-outline-variant">
                <div>
                  <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">Operator</p>
                  <p class="font-bold text-sm">{{ $booking->route->operator->company_name ?? 'BookMyBus Operator' }}</p>
                </div>
                <div class="text-right">
                  <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">Bus Number</p>
                  <p class="font-bold text-sm">{{ $booking->route->bus->registration_number ?? 'ABC-123' }}</p>
                </div>
              </div>

              <!-- QR Code Area -->
              <div class="flex flex-col items-center pt-8">
                <div class="p-4 bg-surface-container-low rounded-xl">
                  <div class="w-32 h-32 bg-white flex items-center justify-center border-4 border-white">
                    <img alt="Ticket QR Code" 
                         class="w-full h-full" 
                         src="https://api.qrserver.com/v1/create-qr-code/?size=128x128&data={{ urlencode($qrData) }}"/>
                  </div>
                </div>
                <p class="mt-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em]">Scan at boarding</p>
              </div>
            </div>
          </div>

          <!-- Bottom Notch -->
          <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-surface rounded-t-full z-10"></div>
        </div>
      </section>

      <!-- Fare Summary -->
      <section class="max-w-3xl mx-auto px-6 mt-8">
        <div class="bg-surface-container-lowest rounded-2xl p-6 border border-surface-container-highest">
          <div class="flex justify-between items-center">
            <div>
              <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">Total Fare</p>
              <p class="text-2xl font-extrabold font-headline text-primary">ZMW {{ number_format($booking->amount, 2) }}</p>
            </div>
            <div class="flex items-center gap-3">
              @if($ticket)
              <a href="{{ route('tickets.show', $ticket->qr_code) }}"
                 class="px-5 py-3 rounded-xl border border-primary text-primary font-bold text-sm hover:bg-primary/5 transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">ticket</span>
                View Ticket
              </a>
              @endif
              <button onclick="window.print()"
                      class="px-5 py-3 rounded-xl border border-outline-variant/30 text-on-surface-variant font-bold text-sm hover:bg-surface-container-low transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">print</span>
                Print
              </button>
              <a href="{{ route('trips.search') }}"
                 class="px-6 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-container transition-colors">
                Book Another Trip
              </a>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>