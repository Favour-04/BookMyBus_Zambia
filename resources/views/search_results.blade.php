<!-- TODO: This view should extend layouts.app when a layout is available -->

<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>BookMyBus Zambia | Search Results</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
    rel="stylesheet" />
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
    }

    body {
      font-family: 'Inter', sans-serif;
    }

    h1,
    h2,
    h3 {
      font-family: 'Manrope', sans-serif;
    }

    /* Dual-handle price range slider */
    .range-slider-wrap {
      position: relative;
      height: 4px;
    }

    .range-slider-wrap input[type="range"] {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 4px;
      margin: 0;
      background: transparent;
      pointer-events: none;
      -webkit-appearance: none;
      appearance: none;
    }

    .range-slider-wrap input[type="range"]::-webkit-slider-thumb {
      pointer-events: auto;
      -webkit-appearance: none;
      appearance: none;
      width: 16px;
      height: 16px;
      border-radius: 9999px;
      background: #00601f;
      border: 2px solid white;
      box-shadow: 0 1px 3px rgba(0,0,0,0.3);
      cursor: pointer;
      margin-top: 0;
    }

    .range-slider-wrap input[type="range"]::-moz-range-thumb {
      pointer-events: auto;
      width: 16px;
      height: 16px;
      border-radius: 9999px;
      background: #00601f;
      border: 2px solid white;
      box-shadow: 0 1px 3px rgba(0,0,0,0.3);
      cursor: pointer;
    }

    .range-slider-wrap input[type="range"]::-webkit-slider-runnable-track {
      background: transparent;
    }

    .range-slider-wrap input[type="range"]::-moz-range-track {
      background: transparent;
    }
  </style>
</head>

<body class="bg-surface text-on-surface">
  <!-- TopNavBar -->
  <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none">
    <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
      <span class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter"><a href="/">BookMyBus
          Zambia</a></span>
      <div class="hidden md:flex items-center gap-8 font-manrope tracking-tight font-bold text-sm">
        <a class="text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1" href="{{ route('home') }}">Find Trips</a>
        <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="{{ route('booking.lookup') }}">My Bookings</a>
        <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="{{ route('operator.login') }}">Operator Portal</a>
        <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="{{ route('support.page') }}">Support</a>
      </div>
      <div class="flex items-center gap-4">
        @auth
        <a href="{{ route('profile') }}"
          class="material-symbols-outlined text-zinc-600 cursor-pointer">account_circle</a>
        @else
        <a href="{{ route('login') }}"
          class="material-symbols-outlined text-zinc-600 cursor-pointer">account_circle</a>
        @endauth
        @guest
        <a href="{{ route('login') }}"
          class="bg-primary text-on-primary px-4 py-2 rounded-lg font-bold text-sm hover:opacity-90 active:scale-95 transition-all">Sign
          In</a>
        @endguest
      </div>
    </div>
  </nav>
  <main class="pt-24 pb-20 px-4 md:px-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      <!-- Filters Sidebar -->
      <aside class="lg:col-span-3 space-y-8">
        <form id="filterForm" method="GET" action="{{ route('trips.search') }}" class="bg-surface-container-low p-6 rounded-2xl">
          <div class="flex items-center justify-between mb-6">
            <h3 class="font-headline font-extrabold text-lg">Filters</h3>
            @if(request()->hasAny(['min_price', 'max_price', 'time_of_day', 'operators']))
            <a href="{{ route('trips.search', request()->only(['origin', 'destination', 'travel_date', 'passengers'])) }}"
              class="text-[10px] font-bold uppercase tracking-wide text-secondary hover:underline">Clear</a>
            @endif
          </div>

          <!-- Preserve the original search criteria across filter submissions -->
          <input type="hidden" name="origin" value="{{ request('origin') }}" />
          <input type="hidden" name="destination" value="{{ request('destination') }}" />
          <input type="hidden" name="travel_date" value="{{ request('travel_date') }}" />
          <input type="hidden" name="passengers" value="{{ request('passengers', 1) }}" />

          <!-- Time of Day -->
          <div class="mb-8">
            <label class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-4 block">Time of Day</label>
            <div class="grid grid-cols-2 gap-2">
              @php
                $periods = [
                  'dawn' => ['icon' => 'wb_twilight', 'label' => 'Dawn'],
                  'morning' => ['icon' => 'light_mode', 'label' => 'Morning'],
                  'afternoon' => ['icon' => 'wb_sunny', 'label' => 'Afternoon'],
                  'night' => ['icon' => 'bedtime', 'label' => 'Night'],
                ];
                $selectedPeriods = (array) request('time_of_day', []);
              @endphp
              @foreach($periods as $key => $period)
                @php $isActive = in_array($key, $selectedPeriods); @endphp
                <label
                  class="time-of-day-btn flex flex-col items-center justify-center p-3 rounded-lg cursor-pointer transition-colors group {{ $isActive ? 'bg-primary/10 text-primary' : 'bg-surface-container-lowest hover:bg-primary/5' }}">
                  <input type="checkbox" name="time_of_day[]" value="{{ $key }}" class="time-of-day-checkbox sr-only"
                    {{ $isActive ? 'checked' : '' }} />
                  <span
                    class="material-symbols-outlined mb-1 {{ $isActive ? '' : 'text-zinc-400 group-hover:text-primary' }}">{{ $period['icon'] }}</span>
                  <span class="text-[10px] font-bold">{{ $period['label'] }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <!-- Price Range -->
          <div class="mb-8">
            <label class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-4 block">Price Range
              (ZMW)</label>
            @php
              $priceFloor = 150;
              $priceCeil = 800;
              $minPrice = (int) request('min_price', $priceFloor);
              $maxPrice = (int) request('max_price', $priceCeil);
            @endphp
            <div class="range-slider-wrap mt-4">
              <div class="absolute inset-0 h-1 bg-zinc-300 rounded-lg"></div>
              <div id="priceRangeTrack" class="absolute h-1 bg-primary rounded-lg"></div>
              <input type="range" id="minPriceSlider" name="min_price" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
                step="10" value="{{ $minPrice }}" />
              <input type="range" id="maxPriceSlider" name="max_price" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
                step="10" value="{{ $maxPrice }}" />
            </div>
            <div class="flex justify-between mt-3 text-xs font-bold text-zinc-600">
              <span id="minPriceLabel">K{{ $minPrice }}</span>
              <span id="maxPriceLabel">K{{ $maxPrice }}</span>
            </div>
          </div>

          <!-- Operators -->
          <div>
            <label class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-4 block">Preferred
              Operator</label>
            <div class="space-y-3">
              @php
                $selectedOperators = array_map('intval', (array) request('operators', []));
              @endphp
              @forelse($allOperators ?? [] as $operator)
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" name="operators[]" value="{{ $operator->id }}" class="filter-auto-submit w-5 h-5 rounded accent-primary"
                  {{ in_array($operator->id, $selectedOperators) ? 'checked' : '' }} />
                <span class="text-sm font-medium">{{ $operator->company_name }}</span>
              </label>
              @empty
              <p class="text-xs text-zinc-500">No operators available</p>
              @endforelse
            </div>
          </div>
        </form>
        <!-- Quick Map View Ad -->
        <div class="relative overflow-hidden rounded-2xl h-48 group cursor-pointer">
          <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
            data-alt="Stylized map of Zambia with transit routes highlighted in glowing green lines over a dark navy background"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAMGarMYuWSoIz79hw-lrj4rTEZzaAayT2NfzYd847uEPdX9SjVkM5num8flMVZKuGbOxOksjxoT6ghZ-4TdZ_ALVB6S2mQMaoYAHNllu4FzkcqgMJmang2ck2IUPCNmF6Rw5FQjlQ4ljrQCqyuD2PB_gvT8_X_HPqBXJXEbetsUrXOmrVHNZ28VNuGXl_g3cQzxBKH_3fkGlA1Q0IyAlIrH3bZpBeEaLJRQcz_ea-Qg-6UGP3iZGX4QeZ6kYb8bMXIWZz-r3dYmyHq" />
          <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 to-transparent p-6 flex flex-col justify-end">
            <span class="text-white font-bold text-sm">View Route on Map</span>
            <p class="text-white/70 text-[10px]">Track real-time bus locations</p>
          </div>
        </div>
      </aside>
      <!-- Results Section -->
      <div class="lg:col-span-9 space-y-6">
        <!-- Search Summary Header -->
        <header class="mb-10">
          <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div>
              <nav class="flex items-center gap-2 text-xs uppercase tracking-widest text-secondary font-bold mb-2">
                <span>One Way</span>
                <span class="w-1 h-1 rounded-full bg-secondary"></span>
                <span>{{ $request->passengers ?? 1 }} Passenger{{ ($request->passengers ?? 1) > 1 ? 's' : '' }}</span>
              </nav>
              <h1 class="text-4xl md:text-5xl font-black text-on-surface tracking-tighter">
                {{ $trips->first()->origin ?? 'N/A' }} <span class="text-primary-container">&rarr;</span> {{ $trips->first()->destination ?? 'N/A' }}
              </h1>
              <p class="text-zinc-500 font-medium mt-1">{{ $trips->first() ? \Carbon\Carbon::parse($trips->first()->travel_date)->format('l, d F Y') : 'N/A' }} • {{ $trips->count() }}
                departures available</p>
            </div>
            <a href="/" class="flex items-center gap-2 bg-surface-container-low px-5 py-3 rounded-xl font-bold text-sm hover:translate-x-1 transition-transform cursor-pointer">
              <span class="material-symbols-outlined text-sm">edit</span>
              Modify Search
            </a>
          </div>
        </header>

        @forelse($trips as $trip)
        <div
          class="bg-surface-container-lowest group relative overflow-hidden rounded-2xl hover:shadow-xl hover:shadow-primary/5 transition-all duration-300">
          <div class="p-6 md:p-8 flex flex-col md:flex-row gap-8 items-center">
            <!-- Operator Info -->
            <div class="flex md:flex-col items-center md:items-start gap-4 md:gap-2 w-full md:w-32 flex-shrink-0">
              <div class="w-14 h-14 bg-surface-container-high rounded-full flex items-center justify-center p-2">
                <img class="w-full h-full object-contain rounded-full" alt="{{ $trip->operator->company_name ?? 'Operator' }} logo"
                  src="https://placehold.co/56x56?text={{ urlencode($trip->operator->company_name ?? 'Operator') }}" />
              </div>
              <div>
                <h4 class="font-headline font-extrabold text-sm text-on-surface">{{ $trip->operator->company_name ?? 'Unknown Operator' }}</h4>
                <div class="flex items-center gap-1 text-[10px] text-zinc-400 font-bold uppercase">
                  <span class="material-symbols-outlined text-[12px] text-secondary"
                    style="font-variation-settings: 'FILL' 1;">star</span>
                  <span>{{ $trip->operator->rating ?? '4.8' }}</span>
                </div>
              </div>
            </div>
            <!-- Trip Details -->
            <div class="flex-grow grid grid-cols-1 md:grid-cols-3 gap-6 items-center w-full">
              <!-- Departure -->
              <div class="text-center md:text-left">
                <span class="block text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-1">Departure</span>
                <h2 class="text-3xl font-black text-on-surface">{{ \Carbon\Carbon::parse($trip->departure_time)->format('H:i') }}</h2>
                <p class="text-sm font-medium text-zinc-500">{{ $trip->origin ?? 'Terminal' }}</p>
              </div>
              <!-- Visual Route -->
              <div class="flex flex-col items-center px-4">
                <span class="text-[10px] font-bold text-primary mb-2">{{ round($trip->distance_km, 0) }} km • {{ \Carbon\Carbon::parse($trip->departure_time)->diff(\Carbon\Carbon::parse($trip->arrival_time))->format('%Hh %Im') }}</span>
                <div class="w-full flex items-center gap-2">
                  <div class="w-2 h-2 rounded-full border-2 border-primary"></div>
                  <div class="flex-grow border-t-2 border-dashed border-zinc-200 relative">
                    <span
                      class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-primary bg-white text-sm">directions_bus</span>
                  </div>
                  <div class="w-2 h-2 rounded-full bg-primary"></div>
                </div>
                <span class="text-[10px] font-medium text-zinc-400 mt-2">Direct Trip</span>
              </div>
              <!-- Arrival -->
              <div class="text-center md:text-right">
                <span class="block text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-1">Arrival</span>
                <h2 class="text-3xl font-black text-on-surface">{{ \Carbon\Carbon::parse($trip->arrival_time)->format('H:i') }}</h2>
                <p class="text-sm font-medium text-zinc-500">{{ $trip->destination ?? 'Station' }}</p>
              </div>
            </div>
            <!-- Price & CTA -->
            <div
              class="w-full md:w-48 bg-surface-container-low md:bg-transparent p-4 md:p-0 rounded-xl flex md:flex-col items-center md:items-end justify-between gap-4">
              <div class="text-right">
                <div class="flex items-baseline gap-1 md:justify-end">
                  <span class="text-[10px] uppercase font-bold text-zinc-400">ZMW</span>
                  <span class="text-3xl font-black text-on-surface">{{ number_format($trip->fare, 0) }}</span>
                </div>
                @php
                  $availableSeats = $trip->availableSeatsCount();
                  $isSoldOut = $availableSeats === 0;
                  $isLowSeats = $availableSeats > 0 && $availableSeats <= 4;
                @endphp
                <p class="text-[10px] font-bold {{ $isSoldOut ? 'text-red-500' : ($isLowSeats ? 'text-secondary' : 'text-primary-container') }} uppercase tracking-tight">
                  {{ $isSoldOut ? 'Sold Out' : $availableSeats . ' Seats left' }}
                </p>
              </div>
              <a href="{{ route('booking.seats', ['route' => $trip->id, 'passengers' => request('passengers', 1)]) }}"
                class="inline-block bg-gradient-to-br from-primary to-primary-container text-white px-8 md:w-full py-3 rounded-lg font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform text-center">
                View Seats
              </a>
            </div>
          </div>
        </div>
        @empty
        <div class="bg-surface-container-lowest p-12 rounded-2xl text-center">
          <span class="material-symbols-outlined text-6xl text-zinc-300 mb-4">search_off</span>
          <h3 class="font-headline text-2xl font-bold text-on-surface mb-2">No buses found for this route today</h3>
          <p class="text-zinc-500">Try searching for a different route or date.</p>
        </div>
        @endforelse

        <!-- Bento Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-12">
          <div class="bg-primary/5 p-6 rounded-2xl border border-primary/10">
            <span class="material-symbols-outlined text-primary mb-4"
              style="font-variation-settings: 'FILL' 1;">security</span>
            <h4 class="font-headline font-bold text-on-surface mb-2">Verified Operators</h4>
            <p class="text-xs text-zinc-500 leading-relaxed">All bus operators on our platform pass a 24-point safety
              and cleanliness inspection.</p>
          </div>
          <div class="bg-secondary/5 p-6 rounded-2xl border border-secondary/10">
            <span class="material-symbols-outlined text-secondary mb-4"
              style="font-variation-settings: 'FILL' 1;">confirmation_number</span>
            <h4 class="font-headline font-bold text-on-surface mb-2">Instant Ticket</h4>
            <p class="text-xs text-zinc-500 leading-relaxed">Receive your boarding QR code via SMS and WhatsApp
              immediately after payment.</p>
          </div>
          <div class="bg-zinc-100 p-6 rounded-2xl">
            <span class="material-symbols-outlined text-zinc-600 mb-4"
              style="font-variation-settings: 'FILL' 1;">support_agent</span>
            <h4 class="font-headline font-bold text-on-surface mb-2">24/7 Support</h4>
            <p class="text-xs text-zinc-500 leading-relaxed">Our Zambian-based support team is available via phone and
              chat for any trip issues.</p>
          </div>
        </div>
      </div>
    </div>
  </main>
  <!-- Footer -->
  <footer class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
      <div>
        <span class="font-manrope font-bold text-zinc-900 dark:text-zinc-100 block mb-2">BookMyBus Zambia</span>
        <p class="font-inter text-xs text-zinc-500 dark:text-zinc-400">© 2024 BookMyBus Zambia. Premium Travel
          Excellence.</p>
      </div>
      <div class="flex flex-wrap gap-6 md:justify-end">
        <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
          href="{{ route('privacy-policy') }}">Privacy Policy</a>
        <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
          href="{{ route('terms-of-service') }}">Terms of Service</a>
        <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
          href="{{ route('carrier-partners') }}">Carrier Partners</a>
        <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
          href="{{ route('contact-us') }}">Contact Us</a>
      </div>
    </div>
  </footer>
  <!-- Mobile Navigation Mockup -->
  <div
    class="md:hidden fixed bottom-0 left-0 w-full bg-white border-t border-zinc-100 px-6 py-3 flex justify-between items-center z-50">
    <button class="flex flex-col items-center text-primary">
      <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">search</span>
      <span class="text-[10px] font-bold">Find</span>
    </button>
    <button class="flex flex-col items-center text-zinc-400">
      <span class="material-symbols-outlined">confirmation_number</span>
      <span class="text-[10px] font-bold">Trips</span>
    </button>
    <button class="flex flex-col items-center text-zinc-400">
      <span class="material-symbols-outlined">account_circle</span>
      <span class="text-[10px] font-bold">Profile</span>
    </button>
  </div>

  <script>
    (function () {
      var form = document.getElementById('filterForm');

      // --- Time of day toggle buttons: restyle on check/uncheck, then re-submit ---
      document.querySelectorAll('.time-of-day-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
          var label = checkbox.closest('.time-of-day-btn');
          var icon = label.querySelector('.material-symbols-outlined');

          if (checkbox.checked) {
            label.classList.remove('bg-surface-container-lowest', 'hover:bg-primary/5');
            label.classList.add('bg-primary/10', 'text-primary');
            icon.classList.remove('text-zinc-400', 'group-hover:text-primary');
          } else {
            label.classList.add('bg-surface-container-lowest', 'hover:bg-primary/5');
            label.classList.remove('bg-primary/10', 'text-primary');
            icon.classList.add('text-zinc-400', 'group-hover:text-primary');
          }

          form.submit();
        });
      });

      // --- Operator checkboxes: re-submit on change ---
      document.querySelectorAll('.filter-auto-submit').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
          form.submit();
        });
      });

      // --- Dual-handle price range slider ---
      var minSlider = document.getElementById('minPriceSlider');
      var maxSlider = document.getElementById('maxPriceSlider');
      var minLabel = document.getElementById('minPriceLabel');
      var maxLabel = document.getElementById('maxPriceLabel');
      var track = document.getElementById('priceRangeTrack');
      var gap = 10; // minimum ZMW gap kept between the two handles

      function updateTrack() {
        var min = parseInt(minSlider.value, 10);
        var max = parseInt(maxSlider.value, 10);
        var floor = parseInt(minSlider.min, 10);
        var ceil = parseInt(minSlider.max, 10);
        var range = ceil - floor;

        var left = ((min - floor) / range) * 100;
        var right = ((max - floor) / range) * 100;

        track.style.left = left + '%';
        track.style.width = Math.max(right - left, 0) + '%';

        minLabel.textContent = 'K' + min;
        maxLabel.textContent = 'K' + max;
      }

      minSlider.addEventListener('input', function () {
        if (parseInt(minSlider.value, 10) > parseInt(maxSlider.value, 10) - gap) {
          minSlider.value = parseInt(maxSlider.value, 10) - gap;
        }
        updateTrack();
      });

      maxSlider.addEventListener('input', function () {
        if (parseInt(maxSlider.value, 10) < parseInt(minSlider.value, 10) + gap) {
          maxSlider.value = parseInt(minSlider.value, 10) + gap;
        }
        updateTrack();
      });

      // Only submit once the user releases the handle, not on every pixel of drag
      minSlider.addEventListener('change', function () { form.submit(); });
      maxSlider.addEventListener('change', function () { form.submit(); });

      updateTrack();
    })();
  </script>
</body>

</html>
