@extends('layouts.app')

@section('title', 'My Bookings')

@push('head')
<style>
    .hero-gradient { background: linear-gradient(135deg, #00601f 0%, #197b30 100%); }
    .editorial-shadow { box-shadow: 0 24px 40px rgba(26, 28, 30, 0.05); }
</style>
@endpush

@section('content')

    <!-- Page Header -->
    <div class="flex items-center gap-4 mb-8">
        <div class="h-14 w-14 rounded-2xl bg-primary-container flex items-center justify-center flex-shrink-0">
            <span class="material-symbols-outlined text-white text-3xl">confirmation_number</span>
        </div>
        <div>
            <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">My Bookings</h1>
            <p class="text-on-surface-variant text-sm font-body mt-1">Your trip history and upcoming journeys.</p>
        </div>
    </div>

    <!-- Status messages (e.g. after cancelling a booking) -->
    @if(session('status'))
      <div class="mb-6 p-4 rounded-xl bg-primary-fixed/30 border border-primary-container/30 flex items-start gap-3">
        <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
        <p class="text-on-surface text-sm font-body">{{ session('status') }}</p>
      </div>
    @endif

    @if($errors->any())
      <div class="mb-6 p-4 rounded-xl bg-error-container border border-error/20 flex items-start gap-3">
        <span class="material-symbols-outlined text-error text-sm mt-0.5">error</span>
        <div>
          @foreach($errors->all() as $error)
            <p class="text-error text-sm font-body">{{ $error }}</p>
          @endforeach
        </div>
      </div>
    @endif

    <!-- Booking List -->
    <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 editorial-shadow overflow-hidden">

        @forelse($bookings as $booking)
          <div class="flex items-center justify-between px-6 py-5 border-b border-outline-variant/10 last:border-0 hover:bg-surface-container-low transition-colors">
            <div class="flex items-center gap-4">
              <div class="h-12 w-12 rounded-xl bg-primary-fixed/40 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-primary">directions_bus</span>
              </div>
              <div>
                <p class="font-headline font-bold text-on-surface">
                  {{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}
                </p>
                <p class="text-on-surface-variant text-xs font-body mt-1">
                  Ref: {{ $booking->reference_id }} · Seat {{ $booking->seat_number }} ·
                  {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-sm font-bold text-on-surface">ZMW {{ number_format($booking->amount, 2) }}</span>
              @php
                $statusStyles = [
                  'confirmed' => 'bg-primary-fixed/40 text-on-primary-fixed-variant',
                  'pending'   => 'bg-secondary-container/30 text-on-secondary-container',
                  'cancelled' => 'bg-error-container text-error',
                ];
                $departure = $booking->route
                    ? \Carbon\Carbon::parse($booking->route->travel_date)->setTimeFromTimeString((string) $booking->route->departure_time)
                    : now();
                $canCancel = in_array($booking->status, ['pending', 'confirmed'], true) && now()->lt($departure);
              @endphp
              <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full {{ $statusStyles[$booking->status] ?? 'bg-surface-container text-on-surface-variant' }}">
                {{ $booking->status }}
              </span>
              @if($booking->ticket)
              <a href="{{ route('tickets.show', $booking->ticket->qr_code) }}"
                 class="px-3 py-1.5 rounded-lg border border-primary/30 text-primary text-xs font-bold uppercase tracking-wider hover:bg-primary-fixed/20 transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">ticket</span>
                View Ticket
              </a>
              @endif
              @if($canCancel)
              <form method="POST" action="{{ route('bookings.cancel', $booking) }}" class="inline-block"
                    onsubmit="return confirm('Cancel this booking?{{ $booking->status === 'confirmed' ? ' A refund will be calculated based on the operator cancellation policy.' : '' }}');">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-lg border border-error/30 text-error text-xs font-bold uppercase tracking-wider hover:bg-error-container/40 transition-colors flex items-center gap-1">
                  <span class="material-symbols-outlined text-sm">cancel</span>
                  Cancel
                </button>
              </form>
              @endif
            </div>
          </div>
        @empty
          <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <span class="material-symbols-outlined text-5xl text-outline mb-3">confirmation_number</span>
            <p class="font-headline font-bold text-on-surface mb-1">No bookings yet</p>
            <p class="text-on-surface-variant text-sm font-body mb-5">Your trip history will show up here once you book a seat.</p>
            <a href="{{ route('home') }}" class="hero-gradient text-white font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 transition-all">
              Find a Trip
            </a>
          </div>
        @endforelse
    </div>

    <!-- Secondary actions -->
    <div class="mt-6 flex flex-wrap gap-4 justify-center">
        <a href="{{ route('home') }}"
           class="px-6 py-3 rounded-xl bg-primary text-on-primary text-sm font-bold hover:brightness-110 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">search</span>
            Find a Trip
        </a>
        <a href="{{ route('booking.lookup') }}"
           class="px-6 py-3 rounded-xl border border-outline-variant/30 text-on-surface-variant text-sm font-bold hover:bg-surface-container-low transition-colors flex items-center gap-2">
            <span class="material-symbols-outlined text-sm">manage_search</span>
            Find a Booking by Reference
        </a>
    </div>
@endsection
