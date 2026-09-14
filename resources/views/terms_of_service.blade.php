@extends('layouts.app')

@section('title', 'Terms of Service')

@push('head')
<style>
.hero-gradient {
            background: linear-gradient(135deg, #00601f 0%, #197b30 100%);
        }
</style>
@endpush

@section('content')


    <!-- Top Navigation -->
    

    <!-- Main Content -->
    
        <div class="mb-10">
            <span class="text-secondary font-bold tracking-widest text-xs uppercase">Legal</span>
            <h1 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2">Terms of Service</h1>
            <p class="text-on-surface-variant mt-3">Last updated: {{ date('F j, Y') }}</p>
        </div>

        <div class="space-y-8">
            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">1. Acceptance of Terms</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    By accessing or using BookMyBus Zambia, you agree to be bound by these Terms of Service. If you do
                    not agree to these terms, please do not use our platform.
                </p>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">2. Booking and Payment</h2>
                <ul class="list-disc pl-6 space-y-2 text-on-surface-variant leading-relaxed">
                    <li>All bookings are subject to seat availability and operator confirmation.</li>
                    <li>Fares are quoted in Zambian Kwacha (ZMW) and include applicable service fees.</li>
                    <li>Payment must be completed via mobile money (MTN or Airtel) to confirm your booking.</li>
                    <li>Unpaid bookings are held for a limited time and may be released if payment is not received.</li>
                    <li>Promo codes are subject to their specific terms and conditions.</li>
                </ul>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">3. Cancellations and Refunds</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    Cancellation and refund policies vary by operator and are governed by the cancellation rules in
                    effect at the time of booking. Please review the cancellation policy before confirming your booking.
                    Refunds are processed back to the original payment method.
                </p>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">4. Passenger Responsibilities</h2>
                <ul class="list-disc pl-6 space-y-2 text-on-surface-variant leading-relaxed">
                    <li>Arrive at the departure point at least 30 minutes before departure.</li>
                    <li>Carry a valid ID/NRC matching the name on your ticket.</li>
                    <li>Present your digital ticket (QR code) at boarding.</li>
                    <li>Follow all safety instructions from the bus operator and driver.</li>
                </ul>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">5. Limitation of Liability</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    BookMyBus Zambia acts as a booking platform and is not the carrier. We are not liable for delays,
                    cancellations, or incidents caused by bus operators, weather conditions, or other factors beyond
                    our reasonable control.
                </p>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">6. Contact Us</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    For questions about these Terms of Service, please contact us at
                    <a href="{{ route('contact-us') }}" class="text-primary font-bold hover:underline">our contact page</a>
                    or email <a href="mailto:support@bookmybus.zm" class="text-primary font-bold hover:underline">support@bookmybus.zm</a>.
                </p>
            </section>
        </div>
    

    <!-- Footer -->
@endsection
