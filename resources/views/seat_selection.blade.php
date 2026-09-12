<!-- TODO: This view should extend layouts.app when a layout is available -->
<!-- TODO: Loop through 'seats' from Buses table to render seat map dynamically -->

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>BookMyBus Zambia | Seat Selection</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
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
                        "inverse-surface": "#2f3123",
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
        h3,
        .font-headline {
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>

<body class="bg-background text-on-background min-h-screen">
    <!-- TopNavBar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none">
        <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
            <span class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter"><a
                    href="/">BookMyBus Zambia</a></span>
            <div class="hidden md:flex gap-8 items-center">
                <a class="font-manrope tracking-tight font-bold text-sm text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1"
                    href="{{ route('home') }}">Find Trips</a>
                <a class="font-manrope tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100"
                    href="{{ route('booking.lookup') }}">My Bookings</a>
                <a class="font-manrope tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100"
                    href="{{ route('operator.login') }}">Operator Portal</a>
                <a class="font-manrope tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100"
                    href="{{ route('support.page') }}">Support</a>
            </div>
            <div class="flex items-center gap-4">
                @auth
                <a href="{{ route('profile') }}" class="material-symbols-outlined text-zinc-600 dark:text-zinc-400"
                    data-icon="account_circle">account_circle</a>
                @else
                <a href="{{ route('login') }}" class="material-symbols-outlined text-zinc-600 dark:text-zinc-400"
                    data-icon="account_circle">account_circle</a>
                @endauth
                @guest
                <a href="{{ route('login') }}"
                    class="bg-primary text-on-primary px-4 py-2 rounded-lg font-bold text-sm transition-transform active:scale-95">Sign
                    In</a>
                @endguest
            </div>
        </div>
    </nav>
    <main class="pt-24 pb-12 px-6 max-w-7xl mx-auto">
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
                                    $isOldSelection = in_array((string) $seatNumber, $oldSeatNumbers, true) || in_array($seatNumber, $oldSeatNumbers, true);
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
                                        <span
                                            class="passenger-seat-badge text-[10px] font-black px-2 py-1 rounded-full bg-surface-container-high text-on-surface-variant/60">
                                            Seat: <span class="seat-value">{{ $oldSeatForPassenger ?: '—' }}</span>
                                        </span>
                                    </div>
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
    </main>
    <!-- Footer -->
    <footer class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <span class="font-manrope font-bold text-zinc-900 dark:text-zinc-100">BookMyBus Zambia</span>
                <p class="font-inter text-xs text-zinc-500 dark:text-zinc-400 mt-2">© 2024 BookMyBus Zambia. Premium
                    Travel Excellence.</p>
            </div>
            <div class="flex flex-wrap gap-6 md:justify-end">
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity"
                    href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity"
                    href="{{ route('terms-of-service') }}">Terms of Service</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity"
                    href="{{ route('carrier-partners') }}">Carrier Partners</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity"
                    href="{{ route('contact-us') }}">Contact Us</a>
            </div>
        </div>
    </footer>
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
                button.setAttribute('aria-pressed', 'true');
            }

            function styleAsAvailable(button) {
                button.classList.remove(...selectedClasses);
                button.classList.add(...availableClasses);
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
</body>

</html>