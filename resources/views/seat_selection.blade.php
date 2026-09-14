@extends('layouts.app')

@section('title', 'Seat Selection')

@push('head')
<style>
h1,
        h2,
        h3,
        .font-headline {
            font-family: 'Manrope', sans-serif;
        }
</style>
@endpush

@section('content')
<!-- TopNavBar -->
    
    
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-error/30 bg-error-container px-4 py-3 text-sm text-on-error-container">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bookings.store') }}" id="booking-form">
            @csrf
            <input type="hidden" name="route_id" value="{{ $route->id }}">
            <input type="hidden" name="seat_number" id="selected-seat" value="{{ old('seat_number') }}">
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
                    <!-- Bus Interior Visualization -->
                    <div class="bg-surface-container-low rounded-[2rem] p-8 relative overflow-hidden">
                        <!-- Subtle Interior Texture/Glass Effect -->
                        <div
                            class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(circle_at_50%_0%,#00601f,transparent)]">
                        </div>
                        <div
                            class="relative bg-surface-container-lowest rounded-xl p-8 shadow-sm border border-outline-variant/15">
                            <!-- Driver's Cabin Area -->
                            <div
                                class="flex justify-between items-center mb-12 pb-8 border-b border-outline-variant/10">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-surface-container-highest rounded-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-on-surface-variant"
                                            data-icon="steering_wheel">steering_wheel_heat</span>
                                    </div>
                                    <span
                                        class="text-sm font-bold uppercase tracking-widest text-on-surface-variant/60">Cockpit</span>
                                </div>
                                <div class="h-12 w-1.5 bg-surface-container-highest rounded-full"></div>
                            </div>
                            <!-- Seat Grid -->
                            <div class="grid grid-cols-5 gap-y-4 gap-x-2" id="seat-grid">
                                @for($seatNumber = 1; $seatNumber <= $route->bus->seat_capacity; $seatNumber++)
                                    @php
                                    $isBooked = in_array($seatNumber, $bookedSeats);
                                    $isOldSelection = (int) old('seat_number') === $seatNumber;
                                    $seatClasses = 'seat-button h-14 rounded-lg flex items-center justify-center text-xs font-bold';
                                    if ($isBooked) {
                                        $seatClasses .= ' bg-surface-dim opacity-40 cursor-not-allowed';
                                    } elseif ($isOldSelection) {
                                        $seatClasses .= ' bg-secondary-container ring-2 ring-primary cursor-pointer';
                                    } else {
                                        $seatClasses .= ' bg-surface-container-highest hover:bg-secondary-container transition-colors cursor-pointer';
                                    }
                                    @endphp
                                    <button type="button"
                                        class="{{ $seatClasses }}"
                                        data-seat="{{ $seatNumber }}"
                                        data-booked="{{ $isBooked ? 'true' : 'false' }}"
                                        aria-pressed="{{ $isOldSelection ? 'true' : 'false' }}"
                                        @if($isBooked) disabled aria-disabled="true" @endif>
                                        <span class="text-on-surface-variant">{{ $seatNumber }}</span>
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
                        <div class="p-4 bg-surface-container-low rounded-xl flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-secondary-container rounded flex items-center justify-center text-on-secondary-container font-black text-xs"
                                    id="selected-seat-display">{{ old('seat_number') ?: '—' }}</div>
                                <div>
                                    <p class="text-xs font-bold">Selected Seat</p>
                                    <p class="text-[10px] text-on-surface-variant">Window View</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-on-surface-variant align-top mr-1">ZMW</span>
                                <span class="text-2xl font-black text-secondary">{{ $route->fare }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Booking Form -->
                    <div class="bg-surface-container-lowest rounded-2xl p-6 shadow-sm border border-outline-variant/10">
                        <h3 class="text-lg font-bold mb-6 text-on-surface">Passenger Details</h3>
                        <div class="space-y-4">
                            <div class="space-y-1">
                                <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">Full
                                    Name</label>
                                <input
                                    class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40 @error('passenger_name') ring-2 ring-error @enderror"
                                    placeholder="John Mulenga" type="text" name="passenger_name"
                                    value="{{ old('passenger_name') }}" required />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">NRC /
                                        ID Number</label>
                                    <input
                                        class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40 @error('id_number') ring-2 ring-error @enderror"
                                        placeholder="123456/78/1" type="text" name="id_number"
                                        value="{{ old('id_number') }}" required />
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] uppercase font-bold text-on-surface-variant/70 ml-1">Phone
                                        Number</label>
                                    <input
                                        class="w-full bg-surface-container-low border-none rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/30 transition-all placeholder:text-on-surface-variant/40 @error('phone') ring-2 ring-error @enderror"
                                        placeholder="+260 9xx xxxxxx" type="tel" name="phone"
                                        value="{{ old('phone') }}" required />
                                </div>
                            </div>
                            <div class="pt-4 flex flex-col sm:flex-row gap-3">
                                <a href="{{ $searchBackUrl }}"
                                    class="w-full py-4 bg-surface-container-high text-on-surface rounded-xl font-bold text-sm hover:bg-surface-container-highest active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-base">arrow_back</span>
                                    <span>Cancel</span>
                                </a>
                                <button
                                    class="w-full py-4 bg-gradient-to-br from-primary to-primary-container text-on-primary rounded-xl font-bold text-sm shadow-xl shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    type="submit" id="confirm-btn" @disabled(! old('seat_number'))>
                                    <span>Confirm Booking</span>
                                    <span class="material-symbols-outlined text-base"
                                        data-icon="arrow_forward">arrow_forward</span>
                                </button>
                            </div>
                        </div>
                        <!-- Security Assurance -->
                        <div class="flex items-center gap-3 px-4 mt-6 text-on-surface-variant/60">
                            <span class="material-symbols-outlined text-sm" data-icon="lock">lock</span>
                            <p class="text-[10px] font-medium leading-relaxed">Your data is encrypted and secured by
                                Zambia Digital Trust protocol. Guaranteed safe transaction.</p>
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    
    <!-- Footer -->
@endsection

@push('scripts')
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const seatButtons = document.querySelectorAll('#seat-grid button[data-seat]');
            const selectedSeatInput = document.getElementById('selected-seat');
            const selectedSeatDisplay = document.getElementById('selected-seat-display');
            const confirmBtn = document.getElementById('confirm-btn');
            const bookingForm = document.getElementById('booking-form');

            const availableClasses = ['bg-surface-container-highest', 'hover:bg-secondary-container'];
            const selectedClasses = ['bg-secondary-container', 'ring-2', 'ring-primary'];

            let activeSeatButton = null;

            function clearSeatSelection() {
                if (activeSeatButton) {
                    activeSeatButton.classList.remove(...selectedClasses);
                    activeSeatButton.classList.add(...availableClasses);
                    activeSeatButton.setAttribute('aria-pressed', 'false');
                    activeSeatButton = null;
                }

                selectedSeatInput.value = '';
                selectedSeatDisplay.textContent = '—';
                confirmBtn.disabled = true;
            }

            function selectSeat(button) {
                if (button.dataset.booked === 'true') {
                    return;
                }

                if (activeSeatButton === button) {
                    clearSeatSelection();
                    return;
                }

                if (activeSeatButton) {
                    activeSeatButton.classList.remove(...selectedClasses);
                    activeSeatButton.classList.add(...availableClasses);
                    activeSeatButton.setAttribute('aria-pressed', 'false');
                }

                button.classList.remove(...availableClasses);
                button.classList.add(...selectedClasses);
                button.setAttribute('aria-pressed', 'true');

                activeSeatButton = button;
                const seatNumber = button.dataset.seat;
                selectedSeatInput.value = seatNumber;
                selectedSeatDisplay.textContent = seatNumber;
                confirmBtn.disabled = false;
            }

            seatButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    selectSeat(this);
                });
            });

            if (selectedSeatInput.value) {
                const preselected = document.querySelector(`#seat-grid button[data-seat="${selectedSeatInput.value}"]`);
                if (preselected && preselected.dataset.booked !== 'true') {
                    selectSeat(preselected);
                }
            }

            bookingForm.addEventListener('submit', function (event) {
                if (!selectedSeatInput.value) {
                    event.preventDefault();
                    alert('Please select a seat before confirming your booking.');
                }
            });
        });
    </script>
@endpush
