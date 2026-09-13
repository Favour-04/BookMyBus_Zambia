@extends('layouts.admin')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-900">Complete your payment</h1>
            <p class="mt-1 text-sm text-slate-500">Booking reference <span class="font-mono font-medium text-slate-700">{{ $booking->reference_id }}</span></p>
        </div>

        {{-- Booking summary --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 mb-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="font-semibold text-slate-900">{{ $booking->route->origin }} &rarr; {{ $booking->route->destination }}</p>
                    <p class="text-sm text-slate-500 mt-1">
                        {{ \Illuminate\Support\Carbon::parse($booking->route->travel_date)->format('l, d M Y') }} &middot;
                        {{ \Illuminate\Support\Carbon::parse($booking->route->departure_time)->format('H:i') }} &middot;
                        Seat {{ $booking->seat_number }}
                    </p>
                    <p class="text-sm text-slate-500">{{ $booking->route->operator->company_name ?? '' }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400">Amount due</p>
                    <p class="text-2xl font-bold text-slate-900">K{{ number_format($booking->route->fare, 2) }}</p>
                </div>
            </div>

            @if ($booking->status === 'pending' && $booking->held_until)
                <p class="mt-4 text-xs font-medium text-amber-600" id="hold-timer" data-held-until="{{ $booking->held_until->toIso8601String() }}">
                    Seat held &mdash; complete payment before the hold expires.
                </p>
            @endif
        </div>

        {{-- Payment method form --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="font-semibold text-slate-900 mb-4">Choose a payment method</h2>

            <form method="POST" action="{{ route('admin.payment.process', $booking) }}" id="payment-form">
                @csrf

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                    <label class="payment-option cursor-pointer">
                        <input type="radio" name="payment_method" value="mtn_money" class="sr-only peer" {{ old('payment_method') === 'mtn_money' ? 'checked' : '' }} required>
                        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-yellow-400 peer-checked:bg-yellow-50 p-4 text-center transition">
                            <div class="h-8 w-8 mx-auto rounded-full bg-yellow-400 mb-2"></div>
                            <p class="text-xs font-semibold text-slate-700">MTN Money</p>
                        </div>
                    </label>

                    <label class="payment-option cursor-pointer">
                        <input type="radio" name="payment_method" value="airtel_money" class="sr-only peer" {{ old('payment_method') === 'airtel_money' ? 'checked' : '' }}>
                        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-red-500 peer-checked:bg-red-50 p-4 text-center transition">
                            <div class="h-8 w-8 mx-auto rounded-full bg-red-600 mb-2"></div>
                            <p class="text-xs font-semibold text-slate-700">Airtel Money</p>
                        </div>
                    </label>

                    <label class="payment-option cursor-pointer">
                        <input type="radio" name="payment_method" value="zanaco" class="sr-only peer" {{ old('payment_method') === 'zanaco' ? 'checked' : '' }}>
                        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-sky-500 peer-checked:bg-sky-50 p-4 text-center transition">
                            <div class="h-8 w-8 mx-auto rounded-full bg-sky-600 mb-2"></div>
                            <p class="text-xs font-semibold text-slate-700">Zamtel Kwacha</p>
                        </div>
                    </label>

                    <label class="payment-option cursor-pointer">
                        <input type="radio" name="payment_method" value="card" class="sr-only peer" {{ old('payment_method') === 'card' ? 'checked' : '' }}>
                        <div class="rounded-xl border-2 border-slate-200 peer-checked:border-slate-500 peer-checked:bg-slate-50 p-4 text-center transition">
                            <div class="h-8 w-8 mx-auto rounded-full bg-slate-600 mb-2"></div>
                            <p class="text-xs font-semibold text-slate-700">Debit / Credit card</p>
                        </div>
                    </label>
                </div>
                @error('payment_method')
                    <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Mobile money phone number (shown for MTN / Airtel) --}}
                <div id="mobile-money-fields" class="mb-5 hidden">
                    <label for="phone_number" class="block text-sm font-medium text-slate-700 mb-1">Mobile money phone number</label>
                    <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                           placeholder="e.g. 096XXXXXXX"
                           class="w-full rounded-lg border-slate-300 focus:border-emerald-600 focus:ring-emerald-600 text-sm @error('phone_number') border-red-400 @enderror">
                    <p class="mt-1 text-xs text-slate-400">You will receive a prompt on your phone to approve the transaction.</p>
                    @error('phone_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full inline-flex justify-center px-4 py-3 rounded-lg bg-emerald-700 text-white text-sm font-semibold hover:bg-emerald-800">
                    Pay K{{ number_format($booking->route->fare, 2) }}
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const methodInputs = document.querySelectorAll('input[name="payment_method"]');
        const mobileFields = document.getElementById('mobile-money-fields');
        const phoneInput = document.getElementById('phone_number');

        function toggleMobileFields() {
            const selected = document.querySelector('input[name="payment_method"]:checked');
            const needsPhone = selected && (selected.value === 'mtn_money' || selected.value === 'airtel_money');
            mobileFields.classList.toggle('hidden', !needsPhone);
            if (phoneInput) {
                phoneInput.required = !!needsPhone;
            }
        }

        methodInputs.forEach((input) => input.addEventListener('change', toggleMobileFields));
        toggleMobileFields();

        // Seat-hold countdown
        const timerEl = document.getElementById('hold-timer');
        if (timerEl) {
            const heldUntil = new Date(timerEl.dataset.heldUntil).getTime();
            const baseText = 'Seat held — complete payment before the hold expires: ';

            const tick = () => {
                const diff = heldUntil - Date.now();
                if (diff <= 0) {
                    timerEl.textContent = 'Your seat hold has expired. Please search again.';
                    return;
                }
                const minutes = Math.floor(diff / 60000);
                const seconds = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
                timerEl.textContent = baseText + minutes + ':' + seconds;
                setTimeout(tick, 1000);
            };
            tick();
        }
    </script>
@endpush
