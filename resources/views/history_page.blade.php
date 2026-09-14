@extends('layouts.app')

@section('title', 'Digital Ticket | BookMyBus Zambia')
@section('main-class', 'pb-16 w-full')

@push('styles')
<style>
  /* Theme-aware shadow token — deeper in dark mode so it's visible on dark surfaces */
  :root {
    --color-shadow: rgba(26, 28, 30, 0.05);
  }

  .dark {
    --color-shadow: rgba(0, 0, 0, 0.45);
  }

  .hero-gradient {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-container) 100%);
  }

  .editorial-shadow {
    box-shadow: 0 24px 40px var(--color-shadow);
  }
</style>
@endpush

@section('content')
@php
$partyBookings = ($groupBookings ?? collect([$booking]));
$isGroupBooking = $partyBookings->count() > 1;
$groupTotal = $partyBookings->sum('amount');
$ticket = $booking->ticket ?? null;
@endphp
@if($booking && $booking->isConfirmed())
<!-- Payment Successful Banner -->
<section class="bg-primary-container py-6 mb-8">
  <div class="max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-center gap-3">
      <span class="material-symbols-outlined text-primary text-3xl"
        style="font-variation-settings: 'FILL' 1;">check_circle</span>
      <h2 class="text-2xl font-extrabold font-headline text-on-primary-container">
        PAYMENT SUCCESSFUL
      </h2>
    </div>
    @if($isGroupBooking)
    <p class="text-center text-sm font-bold text-on-primary-container/80 mt-1">
      {{ $partyBookings->count() }} seats booked on this trip
    </p>
    @endif
  </div>
</section>
@endif

<!-- Digital Ticket Display(s) -->
@foreach($partyBookings as $partyBooking)
@php
$partyTicket = $partyBooking->ticket ?? null;
$partyQrData = $partyTicket?->qr_code ?? $partyBooking->reference_id;
@endphp
<section class="max-w-3xl mx-auto px-6 {{ !$loop->first ? 'mt-10' : '' }}">
  @if($isGroupBooking)
  <p class="text-xs font-bold uppercase tracking-widest text-on-surface-variant mb-3">
    Passenger {{ $loop->iteration }} of {{ $loop->count }}
  </p>
  @endif
  <div class="relative">
    <!-- Top Notch -->
    <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-surface rounded-b-full z-10"></div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-xl overflow-hidden relative">
      <!-- Visual Header -->
      <div class="h-32 relative">
        <img class="w-full h-full object-cover"
          data-alt="Modern coach bus driving through a scenic Zambian highway landscape"
          src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtACWHf96GQ2Q6lm8qydU0xrhEVka89gUOGcMoH974Us9bOlDAAtmr10nk6iIVJ98jsWJWTii8Y8xLjDrFPGlxmNfkI3FGH_6VBr36Xy3f7LlCdbSg2-0_ZP_SiM83Ez88uCg3arvxEVQaOe61WNm9VIt3cvWqw1dkKoQHxHtajf-ws6BRAPpzQED8jlxcNOuEO_ywfSvtmIzSz9cKzbFuJfqHi-ADDDJ6v4amXeggpOH57W9NqViHzH1pa8mfsF4oaXs1MVj_wgc4" />
        <div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest to-transparent"></div>
        <div class="absolute bottom-4 left-6">
          <span class="px-3 py-1 bg-primary text-white text-[10px] font-bold uppercase tracking-widest rounded-full">
            {{ $partyBooking->isConfirmed() ? 'Confirmed' : 'Pending' }}
          </span>
        </div>
      </div>

      <div class="p-8 space-y-8">
        <!-- Passenger & Seat Info -->
        <div class="flex justify-between items-center">
          <div class="space-y-1">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">Passenger</p>
            <p class="font-headline font-extrabold text-xl">{{ $partyBooking->passenger_name ?? 'John Mulenga' }}</p>
          </div>
          <div class="text-right space-y-1">
            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">Seat</p>
            <p class="font-headline font-extrabold text-xl text-secondary">{{ $partyBooking->seat_number }}</p>
          </div>
        </div>

        <!-- Route Info -->
        <div class="flex items-center gap-6 justify-between relative">
          <div class="flex-1">
            <p class="font-black text-2xl font-headline">{{ $booking->route->origin }}</p>
          </div>
          <div class="flex flex-col items-center gap-1">
            <span class="material-symbols-outlined text-primary">directions_bus</span>
            <div class="h-[2px] w-12 bg-surface-container-highest"></div>
          </div>
          <div class="flex-1 text-right">
            <p class="font-black text-2xl font-headline">{{ $booking->route->destination }}</p>
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
            <p class="font-bold text-sm">{{ $partyBooking->reference_id }}</p>
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
              <img alt="Ticket QR Code" class="w-full h-full"
                src="https://api.qrserver.com/v1/create-qr-code/?size=128x128&data={{ urlencode($partyQrData) }}" />
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
@endforeach

<!-- Fare Summary -->
<section class="max-w-3xl mx-auto px-6 mt-8">
  <div class="bg-surface-container-lowest rounded-2xl p-6 border border-surface-container-highest">
    <div class="flex justify-between items-center">
      <div>
        <p class="text-xs uppercase tracking-widest text-on-surface-variant font-bold">
          Total Fare @if($isGroupBooking)<span class="normal-case font-medium">&middot; {{ $partyBookings->count() }}
            seats</span>@endif
        </p>
        <p class="text-2xl font-extrabold font-headline text-primary">ZMW {{ number_format($groupTotal, 2) }}</p>
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
@endsection