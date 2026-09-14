@extends('layouts.app')

@section('title', 'Search Results')

@push('head')
<style>
h1,
    h2,
    h3 {
      font-family: 'Manrope', sans-serif;
    }
</style>
@endpush

@section('content')

  <!-- TopNavBar -->
  
  
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      <!-- Filters Sidebar -->
      <aside class="lg:col-span-3 space-y-8">
        <div class="bg-surface-container-low p-6 rounded-2xl">
          <h3 class="font-headline font-extrabold text-lg mb-6">Filters</h3>
          <!-- Time of Day -->
          <div class="mb-8">
            <label class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-4 block">Time of Day</label>
            <div class="grid grid-cols-2 gap-2">
              <button
                class="flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-lowest hover:bg-primary/5 transition-colors group">
                <span class="material-symbols-outlined text-zinc-400 group-hover:text-primary mb-1">wb_twilight</span>
                <span class="text-[10px] font-bold">Dawn</span>
              </button>
              <button
                class="flex flex-col items-center justify-center p-3 rounded-lg bg-primary/10 text-primary transition-colors">
                <span class="material-symbols-outlined mb-1">light_mode</span>
                <span class="text-[10px] font-bold">Morning</span>
              </button>
              <button
                class="flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-lowest hover:bg-primary/5 transition-colors group">
                <span class="material-symbols-outlined text-zinc-400 group-hover:text-primary mb-1">wb_sunny</span>
                <span class="text-[10px] font-bold">Afternoon</span>
              </button>
              <button
                class="flex flex-col items-center justify-center p-3 rounded-lg bg-surface-container-lowest hover:bg-primary/5 transition-colors group">
                <span class="material-symbols-outlined text-zinc-400 group-hover:text-primary mb-1">bedtime</span>
                <span class="text-[10px] font-bold">Night</span>
              </button>
            </div>
          </div>
          <!-- Price Range -->
          <div class="mb-8">
            <label class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-4 block">Price Range
              (ZMW)</label>
            <input class="w-full accent-primary h-1 bg-zinc-300 rounded-lg appearance-none cursor-pointer" max="800"
              min="150" type="range" />
            <div class="flex justify-between mt-2 text-xs font-bold text-zinc-600">
              <span>K150</span>
              <span>K800</span>
            </div>
          </div>
          <!-- Operators -->
          <div>
            <label class="text-[10px] uppercase tracking-widest font-bold text-zinc-400 mb-4 block">Preferred
              Operator</label>
            <div class="space-y-3">
              @php
                $uniqueOperators = $trips->unique('operator_id')->pluck('operator');
              @endphp
              @forelse($uniqueOperators as $operator)
              <label class="flex items-center gap-3 cursor-pointer group">
                <input type="checkbox" class="w-5 h-5 rounded accent-primary" />
                <span class="text-sm font-medium">{{ $operator->name }}</span>
              </label>
              @empty
              <p class="text-xs text-zinc-500">No operators available</p>
              @endforelse
            </div>
          </div>
        </div>
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
                <img class="w-full h-full object-contain rounded-full" alt="{{ $trip->operator->name ?? 'Operator' }} logo"
                  src="https://placehold.co/56x56?text={{ urlencode($trip->operator->name ?? 'Operator') }}" />
              </div>
              <div>
                <h4 class="font-headline font-extrabold text-sm text-on-surface">{{ $trip->operator->name ?? 'Unknown Operator' }}</h4>
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
  
  <!-- Footer -->
  
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
@endsection
