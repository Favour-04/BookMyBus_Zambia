@extends('layouts.app')

@section('title', 'My Bookings | BookMyBus Zambia')
@section('main-class', 'pb-12 max-w-3xl mx-auto px-4')

@section('content')
    @auth
    {{-- ============================================
         SIGNED-IN DASHBOARD — bookings shown immediately,
         no search required. Scoped to Auth::id() via $trips
         (built in BookingController::paginatedTripsForCurrentUser). --}}
    <div class="mb-14">
        <h1 class="font-headline text-3xl font-extrabold text-on-surface mb-1">Your Bookings</h1>
        <p class="text-on-surface-variant text-sm mb-6">Everything you've booked, most recent first.</p>

        @php
          $statusStyles = [
            'confirmed' => 'bg-primary-fixed/40 text-on-primary-fixed-variant',
            'pending'   => 'bg-secondary/10 text-secondary',
            'cancelled' => 'bg-error-container text-error',
          ];
        @endphp

        <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 shadow-sm overflow-hidden">
            @forelse($trips as $trip)
              @php $primary = $trip->primary; $route = $primary->route; @endphp
              <div class="flex items-center justify-between px-6 py-5 border-b border-outline-variant/10 last:border-0 hover:bg-surface-container-low transition-colors">
                <div class="flex items-center gap-4">
                    <div class="h-11 w-11 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="35" height="35" viewBox="0 0 512 512" style="color: rgb(0, 57, 16); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M47 145c-10 0-23 12.4-23 24.9v134.3l52.49 7.5C84.97 297 100.9 287 119 287c21 0 39 13.3 45.9 32h188.2c6.9-18.7 24.9-32 45.9-32s39 13.3 45.9 32H488v-77.2L456.5 145zm-9 14h405.6l25.6 82H296v64h-98v-64H38zm18 18v46h62v-46zm80 0v46h62v-46zm80 0v110h22V177zm40 0v110h22V177zm40 0v46h62v-46zm86.6 0v46h62.2l-14.4-46zM119 305c-17.2 0-31 13.8-31 31s13.8 31 31 31s31-13.8 31-31s-13.8-31-31-31m280 0c-17.2 0-31 13.8-31 31s13.8 31 31 31s31-13.8 31-31s-13.8-31-31-31m-280 23a8 8 0 0 1 8 8a8 8 0 0 1-8 8a8 8 0 0 1-8-8a8 8 0 0 1 8-8m280 0a8 8 0 0 1 8 8a8 8 0 0 1-8 8a8 8 0 0 1-8-8a8 8 0 0 1 8-8"></path></svg>
                    </div>
                    <div>
                        <p class="font-headline font-bold text-on-surface">
                            {{ $route->origin ?? 'N/A' }} → {{ $route->destination ?? 'N/A' }}
                        </p>
                        <p class="text-on-surface-variant text-xs mt-1">
                            Ref: {{ $primary->reference_id }} ·
                            {{ $trip->is_group ? count($trip->seat_numbers) . ' seats' : 'Seat ' . $primary->seat_number }} ·
                            {{ \Carbon\Carbon::parse($trip->created_at)->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-bold text-on-surface">ZMW {{ number_format($trip->total_amount, 2) }}</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full {{ $statusStyles[$primary->status] ?? 'bg-surface-container text-on-surface-variant' }}">
                        {{ $primary->status }}
                    </span>
                    <a href="{{ route('booking.success', $primary->id) }}"
                       class="px-3 py-1.5 rounded-lg border border-primary/30 text-primary text-xs font-bold uppercase tracking-wider hover:bg-primary/5 transition-colors">
                        View
                    </a>
                </div>
              </div>
            @empty
              <div class="flex flex-col items-center justify-center py-14 px-6 text-center">
                <span class="material-symbols-outlined text-5xl text-outline mb-3">confirmation_number</span>
                <p class="font-headline font-bold text-on-surface mb-1">No bookings yet</p>
                <p class="text-on-surface-variant text-sm mb-5">Your trip history will show up here once you book a seat.</p>
                <a href="{{ route('trips.search') }}" class="bg-primary text-on-primary font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 transition-all">
                    Find a Trip
                </a>
              </div>
            @endforelse
        </div>

        @if($trips->hasPages())
        <div class="mt-4">
            {{ $trips->links() }}
        </div>
        @endif

        <p class="text-xs text-on-surface-variant mt-4">
            Manage or cancel a booking from
            <a href="{{ route('profile') }}#tab-bookings" class="text-primary font-bold hover:underline">My Account → Booking History</a>.
        </p>
    </div>
    @endauth

    {{-- ============================================
         REFERENCE-ID SEARCH — available to everyone.
         Not scoped to the current user: the reference ID itself is
         treated as the credential (like a paper ticket / PNR), which
         is what lets a guest retrieve a booking without an account.
         Phone number is deliberately not offered as a search field —
         it's far easier to guess than a random reference code, so
         allowing it here would make it easy to pull up someone else's
         booking. See BookingController::customerLookup for the full
         reasoning. --}}
    <div>
        <div class="text-center mb-8">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" class="icon" viewBox="0 0 24 24" style="opacity: 1; transform: rotate(0deg); margin: 0 auto;"><path fill="currentColor" d="M12 16.308q.214 0 .357-.144t.143-.357t-.144-.356t-.357-.143t-.356.144t-.143.356q0 .213.144.357t.357.143m0-3.808q.213 0 .356-.144t.143-.357t-.144-.356t-.357-.143t-.356.144t-.143.357t.144.356t.357.143m0-3.808q.213 0 .356-.144t.143-.356t-.144-.357t-.357-.143t-.356.144t-.143.357t.144.356t.357.143M19.385 19H4.615q-.666 0-1.14-.475T3 17.386v-2.577q.883-.327 1.441-1.088Q5 12.96 5 12t-.559-1.72T3 9.192V6.616q0-.667.475-1.141T4.615 5h14.77q.666 0 1.14.475T21 6.615v2.577q-.883.327-1.441 1.088Q19 11.04 19 12t.559 1.72T21 14.808v2.577q0 .666-.475 1.14t-1.14.475m0-1q.269 0 .442-.173t.173-.442V15.45q-.925-.55-1.463-1.462T18 12t.538-1.987T20 8.55V6.616q0-.27-.173-.443T19.385 6H4.615q-.269 0-.442.173T4 6.616V8.55q.925.55 1.463 1.463T6 12t-.537 1.988T4 15.45v1.935q0 .269.173.442t.443.173zM12 12"></path></svg>
            <h2 class="font-headline text-2xl font-extrabold text-on-surface">Look Up a Booking</h2>
            <p class="text-on-surface-variant mt-1 text-sm">
                @auth
                Checking a booking made under someone else's account? Enter its reference ID.
                @else
                Enter the booking reference ID from your ticket.
                @endauth
            </p>
        </div>

        <form method="POST" action="{{ route('booking.lookup.search') }}" class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-8 mb-8 shadow-sm max-w-lg mx-auto">
            @csrf
            @if($error)
            <div class="bg-error-container text-error p-4 rounded-xl flex items-start gap-3 mb-6">
                <span class="material-symbols-outlined">error</span>
                <p class="text-sm font-medium">{{ $error }}</p>
            </div>
            @endif

            <label class="block text-xs font-bold uppercase text-on-surface-variant mb-2">Booking Reference ID</label>
            <input type="text" name="reference_id" value="{{ old('reference_id') }}"
                   placeholder="e.g. BMZ-A3F9K2" required
                   class="w-full bg-surface-container-low border border-outline-variant/30 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-primary transition-all outline-none mb-5">

            <button type="submit"
                    class="w-full py-4 rounded-xl bg-primary text-on-primary font-headline font-bold text-lg shadow-lg hover:brightness-110 transition-all flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="24" height="24" class="icon" viewBox="0 0 24 24" style="opacity: 1; transform: rotate(270deg);"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path stroke-dasharray="40" d="M10.76 13.24c-2.34 -2.34 -2.34 -6.14 0 -8.49c2.34 -2.34 6.14 -2.34 8.49 0c2.34 2.34 2.34 6.14 0 8.49c-2.34 2.34 -6.14 2.34 -8.49 0Z"><animate fill="freeze" attributeName="stroke-dashoffset" dur="0.5s" values="40;0"></animate></path><path stroke-dasharray="14" stroke-dashoffset="14" d="M10.5 13.5l-7.5 7.5"><animate fill="freeze" attributeName="stroke-dashoffset" begin="0.5s" dur="0.2s" to="0"></animate></path></g></svg>
                Find Booking
            </button>
        </form>

        @if($foundBooking)
        @php
            $isGroup = $groupBookings->count() > 1;
            $groupTotal = $groupBookings->sum('amount');
            $route = $foundBooking->route;
        @endphp
        <div class="bg-surface-container-lowest rounded-2xl border-2 border-primary overflow-hidden shadow-lg max-w-lg mx-auto">
            <div class="bg-primary/5 p-6 border-b border-outline-variant/15">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                        {{ $foundBooking->isConfirmed() ? 'bg-primary/10 text-primary' : ($foundBooking->isExpired() ? 'bg-error-container/20 text-error' : 'bg-secondary/10 text-secondary') }}">
                        {{ $foundBooking->isConfirmed() ? 'Confirmed' : ($foundBooking->isExpired() ? 'Expired' : $foundBooking->status) }}
                    </span>
                    <span class="font-mono text-sm font-bold text-on-surface-variant">{{ $foundBooking->reference_id }}</span>
                </div>
                @if($isGroup)
                <p class="text-xs font-bold text-on-surface-variant mt-2">{{ $groupBookings->count() }} seats on this booking</p>
                @endif
            </div>

            <div class="p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase text-on-surface-variant">From</p>
                        <p class="font-headline font-extrabold text-xl">{{ $route->origin ?? 'N/A' }}</p>
                    </div>
                    <span class="material-symbols-outlined text-primary">arrow_forward</span>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-on-surface-variant">To</p>
                        <p class="font-headline font-extrabold text-xl">{{ $route->destination ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-dashed border-outline-variant/20">
                    <div>
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Date</p>
                        <p class="font-bold text-sm">
                            @if($route)
                                {{ \Carbon\Carbon::parse($route->travel_date)->format('d M Y') }} · {{ $route->departure_time }}
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold uppercase text-on-surface-variant mb-1">Total</p>
                        <p class="font-headline font-extrabold text-lg text-primary">ZMW {{ number_format($groupTotal, 2) }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-dashed border-outline-variant/20 space-y-2">
                    @foreach($groupBookings as $seatBooking)
                    @php $qrData = $seatBooking->ticket?->qr_code ?? $seatBooking->reference_id; @endphp
                    <div class="flex items-center justify-between bg-surface-container-low/60 rounded-lg px-4 py-3">
                        <div class="flex items-center gap-3">
                            <img alt="QR" class="w-10 h-10 rounded"
                                 src="https://api.qrserver.com/v1/create-qr-code/?size=64x64&data={{ urlencode($qrData) }}" />
                            <div>
                                <p class="font-bold text-sm">Seat {{ $seatBooking->seat_number }} — {{ $seatBooking->passenger_name ?? 'N/A' }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-on-surface-variant">{{ $seatBooking->status }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="px-6 pb-6 flex justify-center">
                <button onclick="window.print()"
                        class="px-6 py-3 border border-outline-variant/30 rounded-xl text-sm font-bold text-on-surface-variant hover:bg-surface-container-low transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">print</span>
                    Print
                </button>
            </div>
        </div>
        @endif
    </div>

    <p class="text-center text-xs text-on-surface-variant mt-10">
        Having trouble finding your booking? Contact our support team at
        <a href="mailto:support@bookmybus.co.zm" class="text-primary font-bold hover:underline">support@bookmybus.co.zm</a>
    </p>
@endsection