@extends('layouts.app')

@section('title', 'BookMyBus Zambia | Search Results')
@section('main-class', 'pb-20 px-4 md:px-6 max-w-7xl mx-auto')

@push('styles')
<style>
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
        background: var(--color-primary);
        border: 2px solid var(--color-surface-container-lowest);
        box-shadow: 0 1px 3px var(--color-shadow);
        cursor: pointer;
        margin-top: 0;
    }

    .range-slider-wrap input[type="range"]::-moz-range-thumb {
        pointer-events: auto;
        width: 16px;
        height: 16px;
        border-radius: 9999px;
        background: var(--color-primary);
        border: 2px solid var(--color-surface-container-lowest);
        box-shadow: 0 1px 3px var(--color-shadow);
        cursor: pointer;
    }

    .range-slider-wrap input[type="range"]::-webkit-slider-runnable-track {
        background: transparent;
    }

    .range-slider-wrap input[type="range"]::-moz-range-track {
        background: transparent;
    }

        /* SVG icon color — theme-aware. SVGs using stroke="currentColor" or
           fill="currentColor" inherit this automatically. */
        .icon {
            color: var(--color-on-surface);
            transition: color 0.2s ease;
        }
        .icon-primary { color: var(--color-primary); }
        .icon-secondary { color: var(--color-secondary); }
        .icon-muted { color: var(--color-on-surface-variant); }
        .icon-error { color: var(--color-error); }
        .icon-on-primary { color: var(--color-on-primary); }
</style>
@endpush

@section('content')
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
        <label class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant mb-4 block">Time of Day</label>
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
                class="material-symbols-outlined mb-1 {{ $isActive ? '' : 'text-on-surface-variant group-hover:text-primary' }}">{{ $period['icon'] }}</span>
              <span class="text-[10px] font-bold">{{ $period['label'] }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <!-- Price Range -->
      <div class="mb-8">
        <label class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant mb-4 block">Price Range
          (ZMW)</label>
        @php
          $priceFloor = 150;
          $priceCeil = 800;
          $minPrice = (int) request('min_price', $priceFloor);
          $maxPrice = (int) request('max_price', $priceCeil);
        @endphp
        <div class="range-slider-wrap mt-4">
          <div class="absolute inset-0 h-1 bg-surface-container-highest rounded-lg"></div>
          <div id="priceRangeTrack" class="absolute h-1 bg-primary rounded-lg"></div>
          <input type="range" id="minPriceSlider" name="min_price" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
            step="10" value="{{ $minPrice }}" />
          <input type="range" id="maxPriceSlider" name="max_price" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
            step="10" value="{{ $maxPrice }}" />
        </div>
        <div class="flex justify-between mt-3 text-xs font-bold text-on-surface-variant">
          <span id="minPriceLabel">K{{ $minPrice }}</span>
          <span id="maxPriceLabel">K{{ $maxPrice }}</span>
        </div>
      </div>

      <!-- Operators -->
      <div>
        <label class="text-[10px] uppercase tracking-widest font-bold text-on-surface-variant mb-4 block">Preferred
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
          <p class="text-xs text-on-surface-variant">No operators available</p>
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
            <span>{{ request('passengers', 1) }} Passenger{{ request('passengers', 1) > 1 ? 's' : '' }}</span>
          </nav>
          <h1 class="text-4xl md:text-5xl font-black text-on-surface tracking-tighter">
            {{ $trips->first()->origin ?? 'N/A' }} <span class="text-primary-container">&rarr;</span> {{ $trips->first()->destination ?? 'N/A' }}
          </h1>
          <p class="text-on-surface-variant font-medium mt-1">{{ $trips->first() ? \Carbon\Carbon::parse($trips->first()->travel_date)->format('l, d F Y') : 'N/A' }} • {{ $trips->count() }}
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
            {{-- <img class="w-full h-full object-contain rounded-full" alt="{{ $trip->operator->company_name ?? 'Operator' }} logo"
              src="https://placehold.co/56x56?text={{ urlencode($trip->operator->company_name ?? 'Operator') }}" /> --}}
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" class="icon" viewBox="0 0 32 32" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M27 11h2v4h-2zM3 11h2v4H3zm17 9h2v2h-2zm-10 0h2v2h-2z"></path><path fill="currentColor" d="M21 4H11a5.006 5.006 0 0 0-5 5v14a2 2 0 0 0 2 2v3h2v-3h12v3h2v-3a2.003 2.003 0 0 0 2-2V9a5.006 5.006 0 0 0-5-5m3 6v6H8v-6ZM11 6h10a2.995 2.995 0 0 1 2.816 2H8.184A2.995 2.995 0 0 1 11 6M8 23v-5h16.001l.001 5Z"></path></svg>
          </div>
          <div>
            <h4 class="font-headline font-extrabold text-sm text-on-surface">{{ $trip->operator->company_name ?? 'Unknown Operator' }}</h4>
            <div class="flex items-center gap-1 text-[10px] text-on-surface-variant font-bold uppercase">
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
            <span class="block text-[10px] uppercase tracking-widest font-bold text-on-surface-variant mb-1">Departure</span>
            <h2 class="text-3xl font-black text-on-surface">{{ \Carbon\Carbon::parse($trip->departure_time)->format('H:i') }}</h2>
            <p class="text-sm font-medium text-on-surface-variant">{{ $trip->origin ?? 'Terminal' }}</p>
          </div>
          <!-- Visual Route -->
          <div class="flex flex-col items-center px-4">
            <span class="text-[10px] font-bold text-primary mb-2">{{ round($trip->distance_km, 0) }} km • {{ \Carbon\Carbon::parse($trip->departure_time)->diff(\Carbon\Carbon::parse($trip->arrival_time))->format('%Hh %Im') }}</span>
            <div class="w-full flex items-center gap-2">
              <div class="w-2 h-2 rounded-full border-2 border-primary"></div>
              <div class="flex-grow border-t-2 border-dashed border-outline-variant relative">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" class="icon" viewBox="0 0 512 512" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M47 145c-10 0-23 12.4-23 24.9v134.3l52.49 7.5C84.97 297 100.9 287 119 287c21 0 39 13.3 45.9 32h188.2c6.9-18.7 24.9-32 45.9-32s39 13.3 45.9 32H488v-77.2L456.5 145zm-9 14h405.6l25.6 82H296v64h-98v-64H38zm18 18v46h62v-46zm80 0v46h62v-46zm80 0v110h22V177zm40 0v110h22V177zm40 0v46h62v-46zm86.6 0v46h62.2l-14.4-46zM119 305c-17.2 0-31 13.8-31 31s13.8 31 31 31s31-13.8 31-31s-13.8-31-31-31m280 0c-17.2 0-31 13.8-31 31s13.8 31 31 31s31-13.8 31-31s-13.8-31-31-31m-280 23a8 8 0 0 1 8 8a8 8 0 0 1-8 8a8 8 0 0 1-8-8a8 8 0 0 1 8-8m280 0a8 8 0 0 1 8 8a8 8 0 0 1-8 8a8 8 0 0 1-8-8a8 8 0 0 1 8-8"></path></svg>
              </div>
              <div class="w-2 h-2 rounded-full bg-primary"></div>
            </div>
            <span class="text-[10px] font-medium text-on-surface-variant mt-2">Direct Trip</span>
          </div>
          <!-- Arrival -->
          <div class="text-center md:text-right">
            <span class="block text-[10px] uppercase tracking-widest font-bold text-on-surface-variant mb-1">Arrival</span>
            <h2 class="text-3xl font-black text-on-surface">{{ \Carbon\Carbon::parse($trip->arrival_time)->format('H:i') }}</h2>
            <p class="text-sm font-medium text-on-surface-variant">{{ $trip->destination ?? 'Station' }}</p>
          </div>
        </div>
        <!-- Price & CTA -->
        <div
          class="w-full md:w-48 bg-surface-container-low md:bg-transparent p-4 md:p-0 rounded-xl flex md:flex-col items-center md:items-end justify-between gap-4">
          <div class="text-right">
            <div class="flex items-baseline gap-1 md:justify-end">
              <span class="text-[10px] uppercase font-bold text-on-surface-variant">ZMW</span>
              <span class="text-3xl font-black text-on-surface">{{ number_format($trip->fare, 0) }}</span>
            </div>
            @php
              $availableSeats = $trip->availableSeatsCount();
              $isSoldOut = $availableSeats === 0;
              $isLowSeats = $availableSeats > 0 && $availableSeats <= 4;
            @endphp
            <p class="text-[10px] font-bold {{ $isSoldOut ? 'text-error' : ($isLowSeats ? 'text-secondary' : 'text-primary-container') }} uppercase tracking-tight">
              {{ $isSoldOut ? 'Sold Out' : $availableSeats . ' Seats left' }}
            </p>
          </div>
          <a href="{{ route('booking.seats', ['route' => $trip->id, 'passengers' => request('passengers', 1)]) }}"
            class="inline-block bg-gradient-to-br from-primary to-primary-container text-on-primary px-8 md:w-full py-3 rounded-lg font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] transition-transform text-center">
            View Seats
          </a>
        </div>
      </div>
    </div>
    @empty
    <div class="bg-surface-container-lowest p-12 rounded-2xl text-center">
      <span class="material-symbols-outlined text-6xl text-outline mb-4">search_off</span>
      <h3 class="font-headline text-2xl font-bold text-on-surface mb-2">No buses found for this route today</h3>
      <p class="text-on-surface-variant">Try searching for a different route or date.</p>
    </div>
    @endforelse

    <!-- Bento Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-12">
      <div class="bg-primary/5 p-6 rounded-2xl border border-primary/10">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" class="icon" viewBox="0 0 24 24" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><path fill="#85a4e6" d="M12 1L3 5v6c0 5.6 3.8 10.7 9 12c5.2-1.3 9-6.4 9-12V5zm0 11h7c-.5 4.1-3.3 7.8-7 8.9zH5V6.3l7-3.1z"></path><path fill="#5c85de" d="M12 1v22c5.2-1.3 9-6.4 9-12V5zm7 11c-.5 4.1-3.3 7.8-7 8.9V12z"></path><path fill="#3367d6" fill-rule="evenodd" d="M21 12h-2s0 .3-.1.6zM3 12h2v-.6z"></path></svg>
        <h4 class="font-headline font-bold text-on-surface mb-2">Verified Operators</h4>
        <p class="text-xs text-on-surface-variant leading-relaxed">All bus operators on our platform pass a 24-point safety
          and cleanliness inspection.</p>
      </div>
      <div class="bg-secondary/5 p-6 rounded-2xl border border-secondary/10">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" class="icon" viewBox="0 0 48 48" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><g fill="none" stroke-linecap="round" stroke-width="4"><path stroke="#000" stroke-linejoin="round" d="M9.00013 16.0001L34 6.00008L38.0004 16.0001"></path><path fill="#2F88FF" stroke="#000" stroke-linejoin="round" d="M4 16H44V22C41 22 38 24 38 27.5C38 31 41 34 44 34V40H4V34C7.00016 34 10 32 10 28C10 24 7 22 4 22V16Z"></path><path stroke="#fff" d="M17 25.3848H23"></path><path stroke="#fff" d="M17 31.3848H31"></path></g></svg>
        <h4 class="font-headline font-bold text-on-surface mb-2">Instant Ticket</h4>
        <p class="text-xs text-on-surface-variant leading-relaxed">Receive your boarding QR code via SMS and WhatsApp
          immediately after payment.</p>
      </div>
      <div class="bg-surface-container-low p-6 rounded-2xl">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" class="icon" viewBox="0 0 14 14" style="color: rgb(58, 118, 245); opacity: 1; transform: rotate(0deg);"><g fill="none" fill-rule="evenodd" clip-rule="evenodd"><path fill="#8fbffa" d="M11.883 4.477c-.257-1.85-1.229-3.043-2.562-3.7V.776L9.304.768L9.253.744a7 7 0 0 0-.861-.318A8.3 8.3 0 0 0 6.048.085c-.969 0-1.777.2-2.345.4a6 6 0 0 0-.849.373l-.051.03l-.012.006C1.144 1.82.298 3.531.298 5.877c0 1.241.162 2.103.44 2.826c.231.603.54 1.097.852 1.593l.162.26c.357.577.516 1.266.514 1.984l-.001.828a.5.5 0 0 0 .5.5h6.018a.5.5 0 0 0 .5-.5v-1.203c.03-.147.175-.284.402-.284h.493a2 2 0 0 0 2-2v-.897l.292-.007a1.3 1.3 0 0 0 1.06-.555c.292-.416.154-.896.016-1.21c-.15-.34-.398-.703-.635-1.036l-.197-.272c-.18-.25-.35-.486-.498-.717c-.206-.324-.312-.557-.333-.71"></path><path fill="#2859c5" d="M6.673 3.932V.11a8 8 0 0 0-.625-.024q-.325 0-.625.028v3.82c-.472.089-.886.287-1.209.61c-.459.459-.666 1.101-.666 1.833s.207 1.375.666 1.834s1.102.666 1.834.666q.313 0 .6-.05c.727.709 1.859 1.396 3.255 1.802a.625.625 0 0 0 .35-1.2C9.25 9.135 8.432 8.685 7.86 8.23l.02-.02c.46-.459.667-1.102.667-1.834S8.34 5.002 7.88 4.543c-.323-.323-.736-.521-1.208-.61"></path></g></svg>
        <h4 class="font-headline font-bold text-on-surface mb-2">24/7 Support</h4>
        <p class="text-xs text-on-surface-variant leading-relaxed">Our Zambian-based support team is available via phone and
          chat for any trip issues.</p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
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
          icon.classList.remove('text-on-surface-variant', 'group-hover:text-primary');
        } else {
          label.classList.add('bg-surface-container-lowest', 'hover:bg-primary/5');
          label.classList.remove('bg-primary/10', 'text-primary');
          icon.classList.add('text-on-surface-variant', 'group-hover:text-primary');
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
@endpush