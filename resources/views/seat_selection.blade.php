@extends('layouts.app')

@section('title', 'BookMyBus Zambia | Seat Selection')
@section('main-class', 'pb-12 px-6 max-w-7xl mx-auto')

@push('styles')
<style>
    /* Decorative interior glow — theme-aware brand green */
    .seat-interior-glow {
        background: radial-gradient(circle at 50% 0%, var(--color-primary), transparent);
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
@if ($errors->any())
    <div class="mb-6 rounded-xl border border-error/30 bg-error-container px-4 py-3 text-sm text-on-error-container">
        <ul class="list-disc pl-5 space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@php
    $availableRemaining = $route->bus->seat_capacity - count($bookedSeats);
    $passengers = max(1, min((int) ($passengers ?? request('passengers', 1)), max($availableRemaining, 1)));
    $oldSeatNumbers = array_filter((array) old('seat_numbers', []));
@endphp

<form method="POST" action="{{ route('bookings.store') }}" id="booking-form" data-passengers="{{ $passengers }}"
    data-fare="{{ $route->fare }}">
    @csrf
    <input type="hidden" name="route_id" value="{{ $route->id }}">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Left Column: Bus Layout -->
        <section class="lg:col-span-7 space-y-6">
            <a href="{{ $searchBackUrl }}"
                class="inline-flex items-center gap-2 text-sm font-bold text-primary hover:text-primary-container transition-colors">
                {{-- <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="40" height="40" class="icon" viewBox="0 0 14 14" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M13.5 7H.5M4 3.5L.5 7L4 10.5"></path></svg> --}}
                &larr; Back to search results
            </a>
            <div class="flex items-baseline justify-between">
                <h1 class="text-3xl font-black tracking-tight text-primary">{{ $route->origin }} → {{
                    $route->destination }}</h1>
                <div class="flex gap-4">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-surface-container-highest rounded-md"></div>
                        <span class="text-xs font-medium text-on-surface-variant">Available</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-secondary-container rounded-md"></div>
                        <span class="text-xs font-medium text-on-surface-variant">Selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-surface-dim opacity-40 rounded-md"></div>
                        <span class="text-xs font-medium text-on-surface-variant">Occupied</span>
                    </div>
                </div>
            </div>

            <!-- Seat count progress -->
            <div class="flex items-center justify-between bg-primary/5 border border-primary/10 rounded-xl px-5 py-3">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" viewBox="0 0 24 24" style="color: var(--color-primary); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M12 12c1.873 0 3.57.62 4.814 1.487c1.184.826 2.186 2.051 2.186 3.37c0 .725-.31 1.324-.796 1.77c-.458.421-1.056.693-1.672.88C15.301 19.879 13.68 20 12 20s-3.3-.12-4.532-.493c-.616-.187-1.214-.459-1.672-.88c-.487-.446-.796-1.045-.796-1.77c0-1.319 1.002-2.544 2.186-3.37C8.429 12.62 10.127 12 12 12m-7 1q.198 0 .39.016c-.968.909-1.819 2.159-1.886 3.65l-.004.191l.006.221q.026.45.145.857a6 6 0 0 1-1.062-.203c-.345-.104-.723-.268-1.03-.55A1.68 1.68 0 0 1 1 15.93c0-.905.666-1.65 1.307-2.096A4.76 4.76 0 0 1 5 13m14 0c1.044 0 1.992.344 2.693.833c.64.447 1.307 1.19 1.307 2.096c0 .517-.225.946-.56 1.254c-.306.28-.684.445-1.029.55a6 6 0 0 1-1.062.2c.097-.336.151-.695.151-1.076c0-1.575-.881-2.894-1.892-3.841Q18.803 13 19 13M5.5 7a2.5 2.5 0 1 1 0 5a2.5 2.5 0 0 1 0-5m13 0a2.5 2.5 0 1 1 0 5a2.5 2.5 0 0 1 0-5M12 3a4 4 0 1 1 0 8a4 4 0 0 1 0-8"></path></svg>
                    <p class="text-sm font-bold text-on-surface">
                        Select <span id="seats-required-label">{{ $passengers }}</span>
                        seat{{ $passengers > 1 ? 's' : '' }} for your group
                    </p>
                </div>
                <p class="text-sm font-bold text-primary" id="seats-progress-label">0 of {{ $passengers }} selected</p>
            </div>
            <!-- Bus Interior Visualization -->
            <div class="bg-surface-container-low rounded-[2rem] p-8 relative overflow-hidden">
                <!-- Subtle Interior Texture/Glass Effect -->
                <div class="absolute inset-0 opacity-10 pointer-events-none seat-interior-glow"></div>
                <div
                    class="relative bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-outline-variant/15">
                    <!-- Driver's Cabin Area -->
                    <div
                        class="flex justify-between items-center mb-12 pb-8 border-b border-outline-variant/10">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-surface-container-highest rounded-full flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="64" height="64" class="icon" viewBox="0 0 512 512" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M256 25C128.3 25 25 128.3 25 256s103.3 231 231 231s231-103.3 231-231S383.7 25 256 25m0 30c110.9 0 201 90.1 201 201s-90.1 201-201 201S55 366.9 55 256S145.1 55 256 55M80.52 203.9c-4.71 19.2-7.52 37-7.52 54c144.7 30.3 121.5 62.4 148 177.8c11.4 2.1 23 3.3 35 3.3s23.6-1.2 35-3.3c26.5-115.4 3.3-147.5 148-177.8c-.6-18.9-3-38.4-7.5-54C346.7 182.7 301.1 172 256 172s-90.7 10.7-175.48 31.9M256 183c40.2 0 73 32.8 73 73s-32.8 73-73 73s-73-32.8-73-73s32.8-73 73-73m0 18c-30.5 0-55 24.5-55 55s24.5 55 55 55s55-24.5 55-55s-24.5-55-55-55"></path></svg>
                            </div>
                            <span
                                class="text-sm font-bold uppercase tracking-widest text-on-surface-variant/60">Cockpit</span>
                        </div>
                        <div class="h-12 w-1.5 bg-surface-container-highest rounded-full"></div>
                    </div>
                    <!-- Seat Grid: 2 seats | aisle | 2 seats per row, like a real bus -->
                    <div class="grid grid-cols-5 gap-y-4 gap-x-2" id="seat-grid">
                        @php $seatsPerRow = 4; @endphp
                        @for($seatNumber = 1; $seatNumber <= $route->bus->seat_capacity; $seatNumber++)
                            @php
                            $positionInRow = ($seatNumber - 1) % $seatsPerRow;
                            $isBooked = in_array($seatNumber, $bookedSeats);
                            $isOldSelection = in_array((string) $seatNumber, $oldSeatNumbers, true) || in_array($seatNumber, $oldSeatNumbers, true);
                            $seatClasses = 'seat-button h-14 rounded-lg flex items-center justify-center text-xs font-bold';
                            if ($isBooked) {
                                $seatClasses .= ' bg-surface-dim opacity-40 cursor-not-allowed';
                            } elseif ($isOldSelection) {
                                $seatClasses .= ' bg-secondary-container ring-2 ring-primary cursor-pointer';
                            } else {
                                $seatClasses .= ' bg-surface-container-highest hover:bg-secondary-container transition-colors cursor-pointer';
                            }
                            // Seat-number span color: matches the seat state so text stays legible
                            // on the orange (light) / brown (dark) selected background.
                            $seatNumberClass = $isOldSelection ? 'text-on-secondary-container' : 'text-on-surface-variant';
                            @endphp
                            @if ($positionInRow === 2)
                                <div class="h-14" aria-hidden="true"></div>
                            @endif
                            <button type="button"
                                class="{{ $seatClasses }}"
                                data-seat="{{ $seatNumber }}"
                                data-booked="{{ $isBooked ? 'true' : 'false' }}"
                                aria-pressed="{{ $isOldSelection ? 'true' : 'false' }}"
                                @if($isBooked) disabled aria-disabled="true" @endif>
                                <span class="{{ $seatNumberClass }}">{{ $seatNumber }}</span>
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
        <!-- Right Column: Details & Checkout -->
        <aside class="lg:col-span-5 flex flex-col gap-6">
            <!-- Trip Summary Card -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/10">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <p
                            class="text-label uppercase text-[10px] tracking-[0.2em] font-bold text-secondary mb-1">
                            Trip Summary</p>
                        <h2 class="text-2xl font-black text-on-surface">{{ $route->origin }} → {{
                            $route->destination }}</h2>
                    </div>
                    <div class="bg-primary-container px-3 py-1 rounded-full">
                        <span class="text-xs font-bold text-on-primary-container">{{ $route->bus->bus_class ??
                            'Executive Class' }}</span>
                    </div>
                </div>
                <div class="flex gap-12 mb-6">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-on-surface-variant/60 mb-1">Departure</p>
                        <p class="text-lg font-bold">{{ $route->departure_time }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $route->travel_date->format('d M, Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-on-surface-variant/60 mb-1">Arrival</p>
                        <p class="text-lg font-bold">{{ $route->arrival_time }}</p>
                        <p class="text-xs text-on-surface-variant">{{ $route->destination }} Station</p>
                    </div>
                </div>
                <div class="p-4 bg-surface-container-low rounded-xl">
                    <div class="flex justify-between items-center mb-3">
                        <div>
                            <p class="text-xs font-bold">Selected Seats</p>
                            <p class="text-[10px] text-on-surface-variant">{{ $passengers }} passenger{{ $passengers > 1 ? 's' : '' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-on-surface-variant align-top mr-1">ZMW</span>
                            <span class="text-2xl font-black text-secondary" id="total-price-display">{{ number_format($route->fare * $passengers, 0) }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2" id="selected-seat-chips">
                        <span class="text-[11px] text-on-surface-variant/60" id="no-seats-placeholder">No seats selected yet</span>
                    </div>
                </div>
            </div>
            <!-- Booking Form -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/10">
                <h3 class="text-lg font-bold mb-2 text-on-surface">Passenger Details</h3>
                <p class="text-xs text-on-surface-variant/70 mb-6">Fill in one passenger per selected seat. Seats
                    are assigned to passengers in the order you tap them.</p>
                <div class="space-y-5" id="passenger-forms">
                    @for ($i = 0; $i < $passengers; $i++)
                        @php
                            $oldSeatForPassenger = $oldSeatNumbers[$i] ?? '';
                        @endphp
                        <div class="passenger-block space-y-4 {{ $i > 0 ? 'pt-5 border-t border-outline-variant/10' : '' }}"
                            data-index="{{ $i }}">
                            <div class="flex items-center justify-between">
                                <p class="text-xs font-bold uppercase tracking-wide text-on-surface-variant/70">
                                    Passenger {{ $i + 1 }}
                                </p>
                                <div class="flex items-center gap-2">
                                    @if ($i === 0 && $passengers > 1)
                                        <span
                                            class="text-[9px] font-black px-2 py-1 rounded-full bg-primary/10 text-primary uppercase tracking-wide">
                                            Pays for the group
                                        </span>
                                    @endif
                                    <span
                                        class="passenger-seat-badge text-[10px] font-black px-2 py-1 rounded-full bg-surface-container-high text-on-surface-variant/60">
                                        Seat: <span class="seat-value">{{ $oldSeatForPassenger ?: '—' }}</span>
                                    </span>
                                </div>
                            </div>
                            @if ($i === 0 && $passengers > 1)
                                <p class="text-[11px] text-on-surface-variant/60 -mt-3">
                                    This passenger's details are used for the payment and booking reference.
                                </p>
                            @endif
                            <input type="hidden" class="passenger-seat-input" name="passengers[{{ $i }}][seat_number]"
                                value="{{ $oldSeatForPassenger }}">
                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">Full
                                    Name</label>
                                <input
                                    class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40 @error("passengers.$i.passenger_name") ring-2 ring-error @enderror"
                                    placeholder="John Mulenga" type="text" name="passengers[{{ $i }}][passenger_name]"
                                    pattern="[A-Za-z\s'\-]{2,255}" title="Please enter a valid full name (letters only)"
                                    value="{{ old("passengers.$i.passenger_name") }}" required />
                                @error("passengers.$i.passenger_name")
                                    <p class="text-error text-xs mt-1 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">NRC /
                                        ID Number</label>
                                    <input
                                        class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40 @error("passengers.$i.id_number") ring-2 ring-error @enderror"
                                        placeholder="123456/78/1" type="text" name="passengers[{{ $i }}][id_number]"
                                        pattern="[0-9]{6}/[0-9]{2}/[0-9]{1}" title="Please enter a valid NRC number in the format 123456/78/1"
                                        value="{{ old("passengers.$i.id_number") }}" required />
                                    @error("passengers.$i.id_number")
                                        <p class="text-error text-xs mt-1 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">Phone
                                        Number</label>
                                    <input
                                        class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40 @error("passengers.$i.phone") ring-2 ring-error @enderror"
                                        placeholder="+260 9xx xxxxxx" type="tel" name="passengers[{{ $i }}][phone]"
                                        pattern="(\+260|0)[0-9]{9}" title="Please enter a valid Zambian phone number, e.g. 0961234567 or +260961234567"
                                        value="{{ old("passengers.$i.phone") }}" required />
                                    @error("passengers.$i.phone")
                                        <p class="text-error text-xs mt-1 font-bold">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endfor
                    @error('seat_numbers')
                        <p class="text-xs text-error font-medium">{{ $message }}</p>
                    @enderror
                    <div class="pt-4 flex flex-col sm:flex-row gap-3">
                        <a href="{{ $searchBackUrl }}"
                            class="w-full py-4 bg-surface-container-high text-on-surface rounded-xl font-bold text-sm hover:bg-surface-container-highest active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                            {{-- <span class="material-symbols-outlined text-base">arrow_back</span> --}}
                            <span>&larr; Cancel</span>
                        </a>
                        <button
                            class="w-full py-4 bg-gradient-to-br from-primary to-primary-container text-on-primary rounded-xl font-bold text-sm shadow-xl shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            type="submit" id="confirm-btn" @disabled(count($oldSeatNumbers) < $passengers)>
                            <span>Confirm Booking &rarr;</span>
                            {{-- <span class="material-symbols-outlined text-base">arrow_forward</span> --}}
                        </button>
                    </div>
                </div>
                <!-- Security Assurance -->
                <div class="flex items-center gap-3 px-4 mt-6 text-on-surface-variant/60">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" role="img" width="50" height="50" class="icon" viewBox="0 0 24 24" style="color: rgb(74, 85, 101); opacity: 1; transform: rotate(0deg);"><path fill="currentColor" d="M6 22q-.825 0-1.412-.587T4 20V10q0-.825.588-1.412T6 8h1V6q0-2.075 1.463-3.537T12 1t3.538 1.463T17 6v2h1q.825 0 1.413.588T20 10v10q0 .825-.587 1.413T18 22zm7.413-5.587Q14 15.825 14 15t-.587-1.412T12 13t-1.412.588T10 15t.588 1.413T12 17t1.413-.587M9 8h6V6q0-1.25-.875-2.125T12 3t-2.125.875T9 6z"></path></svg>
                    <p class="text-[10px] font-medium leading-relaxed">Your data is encrypted and secured by
                        Zambia Digital Trust protocol. Guaranteed safe transaction.</p>
                </div>
            </div>
        </aside>
    </div>
</form>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const bookingForm = document.getElementById('booking-form');
        const seatButtons = document.querySelectorAll('#seat-grid button[data-seat]');
        const passengerBlocks = Array.from(document.querySelectorAll('.passenger-block'));
        const passengerSeatInputs = passengerBlocks.map((block) => block.querySelector('.passenger-seat-input'));
        const passengerSeatBadges = passengerBlocks.map((block) => block.querySelector('.seat-value'));
        const seatChipsContainer = document.getElementById('selected-seat-chips');
        const noSeatsPlaceholder = document.getElementById('no-seats-placeholder');
        const progressLabel = document.getElementById('seats-progress-label');
        const totalPriceDisplay = document.getElementById('total-price-display');
        const confirmBtn = document.getElementById('confirm-btn');

        const requiredSeats = parseInt(bookingForm.dataset.passengers, 10) || 1;
        const farePerSeat = parseFloat(bookingForm.dataset.fare) || 0;

        const availableClasses = ['bg-surface-container-highest', 'hover:bg-secondary-container'];
        const selectedClasses = ['bg-secondary-container', 'ring-2', 'ring-primary'];
        const availableSpanClasses = ['text-on-surface-variant'];
        const selectedSpanClasses = ['text-on-secondary-container'];

        // Ordered list of currently-selected seat numbers (order = order tapped)
        let selectedSeats = [];

        function seatButtonFor(seatNumber) {
            return document.querySelector(`#seat-grid button[data-seat="${seatNumber}"]`);
        }

        function render() {
            // Sync each passenger block to the seat at the same index
            passengerSeatInputs.forEach((input, index) => {
                const seat = selectedSeats[index] || '';
                input.value = seat;
                passengerSeatBadges[index].textContent = seat || '—';
            });

            // Selected-seat chips in the trip summary card
            seatChipsContainer.innerHTML = '';
            if (selectedSeats.length === 0) {
                seatChipsContainer.appendChild(noSeatsPlaceholder);
            } else {
                selectedSeats.forEach((seat) => {
                    const chip = document.createElement('span');
                    chip.className = 'text-[11px] font-bold px-2 py-1 rounded-full bg-secondary-container text-on-secondary-container';
                    chip.textContent = 'Seat ' + seat;
                    seatChipsContainer.appendChild(chip);
                });
            }

            progressLabel.textContent = selectedSeats.length + ' of ' + requiredSeats + ' selected';
            totalPriceDisplay.textContent = (farePerSeat * requiredSeats).toLocaleString();

            confirmBtn.disabled = selectedSeats.length !== requiredSeats;
        }

        function styleAsSelected(button) {
            button.classList.remove(...availableClasses);
            button.classList.add(...selectedClasses);
            const span = button.querySelector('span');
            if (span) {
                span.classList.remove(...availableSpanClasses);
                span.classList.add(...selectedSpanClasses);
            }
            button.setAttribute('aria-pressed', 'true');
        }

        function styleAsAvailable(button) {
            button.classList.remove(...selectedClasses);
            button.classList.add(...availableClasses);
            const span = button.querySelector('span');
            if (span) {
                span.classList.remove(...selectedSpanClasses);
                span.classList.add(...availableSpanClasses);
            }
            button.setAttribute('aria-pressed', 'false');
        }

        function toggleSeat(button) {
            if (button.dataset.booked === 'true') {
                return;
            }

            const seatNumber = button.dataset.seat;
            const existingIndex = selectedSeats.indexOf(seatNumber);

            if (existingIndex !== -1) {
                // Deselect: remove and let later seats shift up to fill the gap
                selectedSeats.splice(existingIndex, 1);
                styleAsAvailable(button);
            } else {
                if (selectedSeats.length >= requiredSeats) {
                    alert('You can only select ' + requiredSeats + ' seat' + (requiredSeats > 1 ? 's' : '') +
                        ' for this booking. Deselect a seat first if you want to change one.');
                    return;
                }
                selectedSeats.push(seatNumber);
                styleAsSelected(button);
            }

            render();
        }

        seatButtons.forEach((button) => {
            button.addEventListener('click', function () {
                toggleSeat(this);
            });
        });

        // Restore selection after a failed validation round-trip
        passengerSeatInputs.forEach((input) => {
            if (input.value) {
                const preselected = seatButtonFor(input.value);
                if (preselected && preselected.dataset.booked !== 'true' && !selectedSeats.includes(input.value)) {
                    selectedSeats.push(input.value);
                    styleAsSelected(preselected);
                }
            }
        });

        render();

        bookingForm.addEventListener('submit', function (event) {
            if (selectedSeats.length !== requiredSeats) {
                event.preventDefault();
                alert('Please select exactly ' + requiredSeats + ' seat' + (requiredSeats > 1 ? 's' : '') +
                    ' before confirming your booking.');
            }
        });
    });
</script>
@endpush
