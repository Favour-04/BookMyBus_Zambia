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
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" viewBox="0 0 32 32" style="color: rgb(0, 57, 16); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M16 3C8.8 3 3 8.8 3 16s5.8 13 13 13s13-5.8 13-13c0-1.4-.188-2.794-.688-4.094L26.688 13.5c.2.8.313 1.6.313 2.5c0 6.1-4.9 11-11 11S5 22.1 5 16S9.9 5 16 5c3 0 5.694 1.194 7.594 3.094L25 6.688C22.7 4.388 19.5 3 16 3m11.28 4.28L16 18.563l-4.28-4.28l-1.44 1.437l5 5l.72.686l.72-.687l12-12l-1.44-1.44z"></path></svg>
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
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="35" height="35" viewBox="0 0 512 512" style="color: rgb(0, 57, 16); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M47 145c-10 0-23 12.4-23 24.9v134.3l52.49 7.5C84.97 297 100.9 287 119 287c21 0 39 13.3 45.9 32h188.2c6.9-18.7 24.9-32 45.9-32s39 13.3 45.9 32H488v-77.2L456.5 145zm-9 14h405.6l25.6 82H296v64h-98v-64H38zm18 18v46h62v-46zm80 0v46h62v-46zm80 0v110h22V177zm40 0v110h22V177zm40 0v46h62v-46zm86.6 0v46h62.2l-14.4-46zM119 305c-17.2 0-31 13.8-31 31s13.8 31 31 31s31-13.8 31-31s-13.8-31-31-31m280 0c-17.2 0-31 13.8-31 31s13.8 31 31 31s31-13.8 31-31s-13.8-31-31-31m-280 23a8 8 0 0 1 8 8a8 8 0 0 1-8 8a8 8 0 0 1-8-8a8 8 0 0 1 8-8m280 0a8 8 0 0 1 8 8a8 8 0 0 1-8 8a8 8 0 0 1-8-8a8 8 0 0 1 8-8"></path></svg>
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
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="24" height="24" viewBox="0 0 48 48" style="color: rgb(0, 57, 16); opacity: 1; transform: rotate(315deg);"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="4"><path stroke-linejoin="round" d="M9 16L34 6l4 10M4 16h40v6c-3 0-6 2-6 5.5s3 6.5 6 6.5v6H4v-6c3 0 6-2 6-6s-3-6-6-6z"></path><path d="M17 25.385h6m-6 6h14"></path></g></svg>
          View Ticket
        </a>
        @endif
        <button onclick="window.print()"
          class="px-5 py-3 rounded-xl border border-outline-variant/30 text-on-surface-variant font-bold text-sm hover:bg-surface-container-low transition-colors flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="24" height="24" class="icon" viewBox="0 0 512 512" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M420 128.1V16H92v112.1A80.1 80.1 0 0 0 16 208v192h68v-32H48V208a48.054 48.054 0 0 1 48-48h320a48.054 48.054 0 0 1 48 48v160h-44v32h76V208a80.1 80.1 0 0 0-76-79.9m-32-.1H124V48h264Z"></path><path fill="currentColor" d="M396 200h32v32h-32zm-280 64H76v32h40v200h272V296h40v-32zm240 200H148V296h208Z"></path></svg>
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