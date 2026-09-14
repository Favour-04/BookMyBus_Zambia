@extends('layouts.app')

@section('title', 'Secure Payment | BookMyBus Zambia')

@section('content')
@php
    $partyBookingsForHeader = ($group_bookings ?? collect([$booking]));
    $isGroupBookingHeader = $partyBookingsForHeader->count() > 1;
@endphp
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
    <!-- Left Column: Payment Methods -->
    <div class="lg:col-span-7 space-y-8">
        <header>
            <div class="flex items-center gap-2 mb-4">
                <form id="release-seat-form" method="POST"
                    action="{{ route('bookings.release-hold', $booking->id) }}" class="inline">
                    @csrf
                    <button type="button" id="back-to-seats-btn"
                        class="flex items-center gap-1 text-sm font-bold text-primary hover:underline">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Back to Seat Selection
                    </button>
                </form>
            </div>
            <h1 class="text-4xl font-black font-headline tracking-tight text-on-surface mb-2">Secure Checkout</h1>
            <p class="text-on-surface-variant font-medium">Finalize your booking to {{ $destination ?? 'Lusaka' }} securely.</p>
        </header>

        <!-- Expired Booking Banner -->
        @if ($expired)
            <div class="bg-error-container text-on-error-container p-6 rounded-xl flex items-start gap-4">
                <span class="material-symbols-outlined text-3xl">timer_off</span>
                <div>
                    <h3 class="font-headline font-bold text-lg">Booking Reservation Expired</h3>
                    <p class="text-sm mt-1">Your seat hold expired at {{ $held_until->format('H:i') }}. This seat may no longer be available.</p>
                    <a href="{{ route('trips.search') }}" class="inline-block mt-3 px-6 py-2 bg-white text-error font-bold rounded-lg text-sm hover:bg-error hover:text-white transition-colors">
                        Find New Trip
                    </a>
                </div>
            </div>
        @endif

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="bg-error-container text-on-error-container p-4 mb-6 rounded-xl">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">error</span>
                    <ul class="text-sm font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- Payment Error (shown prominently for payment failures) -->
        @error('payment')
            <div class="bg-error-container text-on-error-container p-6 rounded-xl flex items-start gap-4 mb-6">
                <span class="material-symbols-outlined text-3xl">payment</span>
                <div>
                    <h3 class="font-headline font-bold text-lg">Payment Failed</h3>
                    <p class="text-sm mt-1">{{ $message }}</p>
                    <p class="text-xs mt-2 opacity-80">You can try again with a different phone number or payment method.</p>
                </div>
            </div>
        @enderror

        <!-- Reservation Countdown Timer -->
        @if (!$expired && $held_until)
            <div class="bg-surface-container-low p-4 rounded-xl flex items-center gap-3" id="countdown-container">
                <span class="material-symbols-outlined text-secondary">hourglass_top</span>
                <div class="flex-1">
                    <p class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Seat reserved until</p>
                    <p class="font-headline font-bold text-xl text-secondary" id="countdown-display">
                        <span id="countdown-minutes">--</span>:<span id="countdown-seconds">--</span>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-secondary" id="countdown-icon">schedule</span>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('payment.process', $booking->id) }}" id="payment-form" onsubmit="setLoadingState(this)">
            @csrf
            <input type="hidden" name="payment_provider" id="payment_provider" value="mtn">

            <section class="space-y-4">
                <h2 class="text-sm font-bold uppercase tracking-widest text-secondary font-label">Mobile Money Wallets</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Airtel Money Option -->
                    <div
                        id="airtel-card"
                        onclick="selectProvider('airtel')"
                        role="button"
                        tabindex="0"
                        onkeydown="if(event.key==='Enter'||event.key===' ')selectProvider('airtel')"
                        class="group relative bg-surface-container-lowest p-6 rounded-xl shadow-sm cursor-pointer hover:shadow-md transition-all border-2 border-transparent">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-lg bg-red-600 flex items-center justify-center text-white font-bold text-xl">A</div>
                            <div class="selection-circle w-5 h-5 rounded-full border-2 border-outline flex items-center justify-center">
                                <div class="dot w-2.5 h-2.5 bg-primary rounded-full opacity-0 transition-opacity"></div>
                            </div>
                        </div>
                        <h3 class="font-headline font-bold text-lg">Airtel Money</h3>
                        <p class="text-xs text-on-surface-variant mb-4">Pay instantly using your Airtel number</p>
                        <span class="text-xs font-bold text-primary flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">verified_user</span> Secure Network
                        </span>
                    </div>

                    <!-- MTN Money Option -->
                    <div
                        id="mtn-card"
                        onclick="selectProvider('mtn')"
                        role="button"
                        tabindex="0"
                        onkeydown="if(event.key==='Enter'||event.key===' ')selectProvider('mtn')"
                        class="group relative bg-surface-container-lowest p-6 rounded-xl shadow-sm cursor-pointer hover:shadow-md transition-all border-2 border-primary">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 rounded-lg bg-yellow-400 flex items-center justify-center text-zinc-900 font-bold text-xl">M</div>
                            <div class="selection-circle w-5 h-5 rounded-full border-2 border-primary flex items-center justify-center">
                                <div class="dot w-2.5 h-2.5 bg-primary rounded-full opacity-100 transition-opacity"></div>
                            </div>
                        </div>
                        <h3 class="font-headline font-bold text-lg">MTN MoMo</h3>
                        <p class="text-xs text-on-surface-variant mb-4">Confirm on your phone via USSD prompt</p>
                        <span class="text-xs font-bold text-primary flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">verified_user</span> Recommended
                        </span>
                    </div>
                </div>
            </section>

            <section class="bg-surface-container-low p-8 rounded-xl space-y-6">
                <div class="flex items-center gap-4">
                    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">phone_iphone</span>
                    <div class="flex-1">
                        <label id="phone-label" class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">MTN Phone Number</label>
                        <input
                            id="phone-input"
                            name="phone_number"
                            required
                            pattern="0(96|76)[0-9]{7}"
                            title="Please enter a valid 10-digit Zambian phone number"
                            autocomplete="tel"
                            class="w-full bg-surface-container-lowest border-none rounded-lg p-4 font-headline font-bold text-lg focus:ring-2 focus:ring-primary/30 transition-shadow outline-none @error('phone_number') ring-2 ring-error @enderror"
                            placeholder="096 123 4567"
                            type="tel"
                            value="{{ old('phone_number') }}" />
                        @error('phone_number')
                            <p class="text-error text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" id="pay-button"
                    class="w-full py-4 rounded-xl bg-gradient-to-br from-primary to-primary-container text-on-primary font-headline font-bold text-lg shadow-lg hover:shadow-primary/20 transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed"
                    @if($expired) disabled @endif>
                    @if($expired)
                        Reservation Expired
                    @else
                        Pay with MTN/Airtel Money • ZMW{{ number_format($total_fare, 2) }}
                    @endif
                </button>
                <p class="text-center text-xs text-on-surface-variant">By clicking authorize, you will receive a prompt on your phone to enter your PIN.</p>
            </section>
        </form>
    </div>

    <!-- Right Column: Digital Ticket & Summary -->
    <div class="lg:col-span-5">
        <div class="sticky top-24 space-y-6">
            <!-- Ticket Design -->
            <div class="relative">
                <!-- Top Notch -->
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-surface rounded-b-full z-10"></div>
                <div class="bg-surface-container-lowest rounded-2xl shadow-xl overflow-hidden relative">
                    <!-- Visual Header -->
                    <div class="h-32 relative">
                        <img class="w-full h-full object-cover"
                            data-alt="Modern coach bus driving through a scenic Zambian highway landscape at golden hour with soft sunlight"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtACWHf96GQ2Q6lm8qydU0xrhEVka89gUOGcMoH974Us9bOlDAAtmr10nk6iIVJ98jsWJWTii8Y8xLjDrFPGlxmNfkI3FGH_6VBr36Xy3f7LlCdbSg2-0_ZP_SiM83Ez88uCg3arvxEVQaOe61WNm9VIt3cvWqw1dkKoQHxHtajf-ws6BRAPpzQED8jlxcNOuEO_ywfSvtmIzSz9cKzbFuJfqHi-ADDDJ6v4amXeggpOH57W9NqViHzH1pa8mfsF4oaXs1MVj_wgc4" />
                        <div class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest to-transparent"></div>
                        <div class="absolute bottom-4 left-6">
                            <span class="px-3 py-1 bg-primary text-on-primary text-[10px] font-bold uppercase tracking-widest rounded-full">Pending Payment</span>
                        </div>
                    </div>
                    <div class="p-8 space-y-8">
                        @php
                            $partyBookings = ($group_bookings ?? collect([$booking]));
                            $isGroupBooking = $partyBookings->count() > 1;
                        @endphp
                        <div class="flex justify-between items-center">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">Passenger</p>
                                <p class="font-headline font-extrabold text-xl">
                                    {{ $passenger_name ?? 'John Mulenga' }}
                                    @if($isGroupBooking)
                                        <span class="text-sm font-bold text-on-surface-variant">+{{ $partyBookings->count() - 1 }} more</span>
                                    @endif
                                </p>
                            </div>
                            <div class="text-right space-y-1">
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">
                                    {{ $isGroupBooking ? 'Seats' : 'Seat' }}
                                </p>
                                <p class="font-headline font-extrabold text-xl text-secondary">
                                    {{ isset($seat_numbers) ? implode(', ', $seat_numbers) : $seat_number }}
                                </p>
                            </div>
                        </div>
                        @if($isGroupBooking)
                        <!-- Your Party -->
                        <div class="bg-surface-container-low rounded-xl p-4 space-y-2">
                            <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">
                                Your Party ({{ $partyBookings->count() }} passengers)
                            </p>
                            @foreach($partyBookings as $partyBooking)
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-bold">{{ $partyBooking->passenger_name }}</span>
                                    <span class="text-on-surface-variant">Seat {{ $partyBooking->seat_number }}</span>
                                </div>
                            @endforeach
                        </div>
                        @endif
                        <div class="flex items-center gap-6 justify-between relative">
                            <div class="flex-1">
                                <p class="font-black text-2xl font-headline">{{ $origin_code ?? 'LUN' }}</p>
                                <p class="text-xs font-medium text-on-surface-variant">{{ $origin }}</p>
                            </div>
                            <div class="flex flex-col items-center gap-1">
                                <span class="material-symbols-outlined text-primary">directions_bus</span>
                                <div class="h-[2px] w-12 bg-surface-container-highest"></div>
                            </div>
                            <div class="flex-1 text-right">
                                <p class="font-black text-2xl font-headline">{{ $destination_code ?? 'LUS' }}</p>
                                <p class="text-xs font-medium text-on-surface-variant">{{ $destination }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-6 pt-6 border-t border-dashed border-outline-variant">
                            <div>
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">Departure</p>
                                <p class="font-bold text-sm">{{ $departure_date }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $departure_time }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">Booking ID</p>
                                <p class="font-bold text-sm">{{ $booking_id }}</p>
                                <p class="text-xs text-on-surface-variant">{{ $class_type ?? 'Premium Class' }}</p>
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
                                    <img alt="Ticket QR Code" class="w-full h-full" loading="lazy"
                                        src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($booking_id) }}" />
                                </div>
                            </div>
                            <p class="mt-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em]">Scan at boarding</p>
                        </div>
                    </div>
                    <!-- Bottom Notch -->
                    <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-surface rounded-t-full z-10"></div>
                </div>
            </div>

            <!-- Price Breakdown -->
            <div class="bg-surface-container-low p-6 rounded-xl space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                    Fare Summary
                    @if($isGroupBooking)
                        <span class="normal-case font-medium text-on-surface-variant/70">&middot; {{ $partyBookings->count() }} seats</span>
                    @endif
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Base Fare</span>
                        <span class="font-bold">ZMW {{ number_format($base_fare, 2) }}</span>
                    </div>
                    @if($service_fee_total > 0)
                    <div class="flex justify-between">
                        <span class="text-on-surface-variant">Service Fees</span>
                        <span class="font-bold">ZMW {{ number_format($service_fee_total, 2) }}</span>
                    </div>
                    @endif
                    @if($discount_amount > 0)
                    <div class="flex justify-between text-primary">
                        <span>Discount @if($applied_promo)({{ $applied_promo }})@endif</span>
                        <span class="font-bold">-ZMW {{ number_format($discount_amount, 2) }}</span>
                    </div>
                    @endif
                    <div class="border-t border-dashed border-outline-variant pt-2 mt-2">
                        <div class="flex justify-between font-headline font-extrabold text-lg">
                            <span>Total</span>
                            <span class="text-primary">ZMW {{ number_format($total_fare, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trust Indicators -->
            <div class="bg-surface-container-low p-6 rounded-xl flex items-center gap-4">
                <span class="material-symbols-outlined text-primary text-3xl">shield_lock</span>
                <div>
                    <p class="font-bold text-sm">Bank-Grade Security</p>
                    <p class="text-xs text-on-surface-variant">Your transaction is encrypted and secured by Zambia's leading payment gateways.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        function setLoadingState(form) {
            const btn = document.getElementById('pay-button');
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-wait');
            btn.innerHTML = `<span class="flex items-center justify-center gap-2"><span class="animate-spin material-symbols-outlined">sync</span> Processing Payment...</span>`;
        }

        function selectProvider(provider) {
            document.getElementById('payment_provider').value = provider;

            const airtelCard = document.getElementById('airtel-card');
            const mtnCard = document.getElementById('mtn-card');
            const phoneLabel = document.getElementById('phone-label');
            const phoneInput = document.getElementById('phone-input');

            if (provider === 'airtel') {
                airtelCard.classList.add('border-primary');
                airtelCard.classList.remove('border-transparent');
                airtelCard.querySelector('.dot').classList.replace('opacity-0', 'opacity-100');

                mtnCard.classList.replace('border-primary', 'border-transparent');
                mtnCard.querySelector('.dot').classList.replace('opacity-100', 'opacity-0');

                phoneLabel.textContent = 'Airtel Phone Number';
                phoneInput.placeholder = '097 123 4567';
                phoneInput.pattern = "0(97|77)[0-9]{7}";
                phoneInput.title = "Please enter a 10-digit Airtel number starting with 097 or 077";
            } else {
                mtnCard.classList.add('border-primary');
                mtnCard.classList.remove('border-transparent');
                mtnCard.querySelector('.dot').classList.replace('opacity-0', 'opacity-100');

                airtelCard.classList.replace('border-primary', 'border-transparent');
                airtelCard.querySelector('.dot').classList.replace('opacity-100', 'opacity-0');

                phoneLabel.textContent = 'MTN Phone Number';
                phoneInput.placeholder = '096 123 4567';
                phoneInput.pattern = "0(96|76)[0-9]{7}";
                phoneInput.title = "Please enter a 10-digit MTN number starting with 096 or 076";
            }
        }

        // Countdown Timer
        (function initCountdown() {
            const container = document.getElementById('countdown-container');
            if (!container) return; // no countdown (expired or no held_until)

            const minsEl = document.getElementById('countdown-minutes');
            const secsEl = document.getElementById('countdown-seconds');
            const iconEl = document.getElementById('countdown-icon');
            const payBtn = document.getElementById('pay-button');

            // Parse the held_until timestamp (ISO 8601 format from PHP)
            const heldUntil = new Date('{{ $held_until->toIso8601String() }}').getTime();

            function tick() {
                const now = Date.now();
                const diff = heldUntil - now;

                if (diff <= 0) {
                    // Timer expired
                    minsEl.textContent = '00';
                    secsEl.textContent = '00';
                    iconEl.textContent = 'timer_off';
                    iconEl.classList.add('text-error');

                    // Disable the pay button
                    if (payBtn) {
                        payBtn.disabled = true;
                        payBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        payBtn.textContent = 'Reservation Expired';
                    }

                    // Show an inline expired message if not already shown
                    const existingMsg = document.querySelector('.expired-timer-msg');
                    if (!existingMsg) {
                        const msg = document.createElement('div');
                        msg.className = 'expired-timer-msg bg-error-container text-on-error-container p-4 rounded-xl flex items-center gap-3 mt-4';
                        msg.innerHTML = '<span class="material-symbols-outlined">timer_off</span><p class="text-sm font-medium">Your reservation has expired. Please go back and select your seat again.</p>';
                        container.parentNode.insertBefore(msg, container.nextSibling);
                    }

                    clearInterval(interval);
                    return;
                }

                const totalSeconds = Math.floor(diff / 1000);
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;

                minsEl.textContent = String(minutes).padStart(2, '0');
                secsEl.textContent = String(seconds).padStart(2, '0');

                // Pulse when under 1 minute
                if (totalSeconds <= 60) {
                    container.classList.add('bg-error/10');
                    iconEl.textContent = 'alarm';
                    iconEl.classList.add('animate-pulse', 'text-error');
                    minsEl.classList.add('text-error');
                    secsEl.classList.add('text-error');
                }
            }

            tick();
            var interval = setInterval(tick, 1000);
        })();

        // "Back to Seat Selection" — releasing the hold is a real, POST-backed
        // action (frees the seat immediately rather than waiting on the
        // 10-minute expiry), so confirm before submitting.
        document.getElementById('back-to-seats-btn')?.addEventListener('click', function () {
            const isGroup = @json($isGroupBookingHeader);
            const seatWord = isGroup ? 'seats' : 'seat';
            const pronoun = isGroup ? 'them' : 'it';

            const confirmed = window.confirm(
                'Going back will release your selected ' + seatWord + '. ' +
                'Someone else could book ' + pronoun + ' in the meantime. Continue?'
            );

            if (confirmed) {
                document.getElementById('release-seat-form').submit();
            }
        });
    </script>
@endpush