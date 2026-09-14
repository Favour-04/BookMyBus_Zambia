@extends('layouts.app')

@section('title', 'Privacy Policy')

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
            <h1 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2">Privacy Policy</h1>
            <p class="text-on-surface-variant mt-3">Last updated: {{ date('F j, Y') }}</p>
        </div>

        <div class="space-y-8">
            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">1. Information We Collect</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    BookMyBus Zambia collects information you provide directly to us, including your name, email address,
                    phone number, ID/NRC number, and booking details. We also automatically collect certain information
                    about your device and how you interact with our platform.
                </p>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">2. How We Use Your Information</h2>
                <ul class="list-disc pl-6 space-y-2 text-on-surface-variant leading-relaxed">
                    <li>To process and manage your bus ticket bookings</li>
                    <li>To process payments via mobile money (MTN, Airtel)</li>
                    <li>To send you booking confirmations and digital tickets</li>
                    <li>To provide customer support and respond to your inquiries</li>
                    <li>To improve our services and personalize your experience</li>
                    <li>To comply with legal obligations and prevent fraud</li>
                </ul>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">3. Data Sharing</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    We share your booking information with the bus operators you travel with so they can verify your
                    ticket and seat assignment. We do not sell your personal data to third parties. Payment information
                    is processed securely through our mobile money partners.
                </p>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">4. Data Security</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    We implement appropriate technical and organizational measures to protect your personal information
                    against unauthorized access, alteration, disclosure, or destruction. All sensitive data is encrypted
                    in transit and at rest.
                </p>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">5. Your Rights</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    You have the right to access, correct, or delete your personal information. You may also object to
                    or restrict certain processing of your data. To exercise these rights, contact our support team at
                    <a href="mailto:support@bookmybus.zm" class="text-primary font-bold hover:underline">support@bookmybus.zm</a>.
                </p>
            </section>

            <section class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
                <h2 class="font-headline text-2xl font-bold mb-4">6. Contact Us</h2>
                <p class="text-on-surface-variant leading-relaxed">
                    If you have any questions about this Privacy Policy, please contact us at
                    <a href="{{ route('contact-us') }}" class="text-primary font-bold hover:underline">our contact page</a>
                    or email <a href="mailto:support@bookmybus.zm" class="text-primary font-bold hover:underline">support@bookmybus.zm</a>.
                </p>
            </section>
        </div>
    

    <!-- Footer -->
@endsection
