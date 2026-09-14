@extends('layouts.app')

@section('title', 'BookMyBus Zambia | Seat Selection')
@section('main-class', 'pb-12 px-6 max-w-7xl mx-auto')

@push('styles')
<style>
    /* Decorative interior glow — theme-aware brand green */
    .seat-interior-glow {
        background: radial-gradient(circle at 50% 0%, var(--color-primary), transparent);
    }
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
                <span class="material-symbols-outlined text-base">arrow_back</span>
                Back to search results
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
                    <span class="material-symbols-outlined text-primary text-lg">group</span>
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
                                <span class="material-symbols-outlined text-on-surface-variant">steering_wheel_heat</span>
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
                                    class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40"
                                    placeholder="John Mulenga" type="text" name="passengers[{{ $i }}][passenger_name]"
                                    value="{{ old("passengers.$i.passenger_name") }}" required />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">NRC /
                                        ID Number</label>
                                    <input
                                        class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40"
                                        placeholder="123456/78/1" type="text" name="passengers[{{ $i }}][id_number]"
                                        value="{{ old("passengers.$i.id_number") }}" required />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">Phone
                                        Number</label>
                                    <input
                                        class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40"
                                        placeholder="+260 9xx xxxxxx" type="tel" name="passengers[{{ $i }}][phone]"
                                        value="{{ old("passengers.$i.phone") }}" required />
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
                            <span class="material-symbols-outlined text-base">arrow_back</span>
                            <span>Cancel</span>
                        </a>
                        <button
                            class="w-full py-4 bg-gradient-to-br from-primary to-primary-container text-on-primary rounded-xl font-bold text-sm shadow-xl shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                            type="submit" id="confirm-btn" @disabled(count($oldSeatNumbers) < $passengers)>
                            <span>Confirm Booking</span>
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </button>
                    </div>
                </div>
                <!-- Security Assurance -->
                <div class="flex items-center gap-3 px-4 mt-6 text-on-surface-variant/60">
                    <span class="material-symbols-outlined text-sm">lock</span>
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