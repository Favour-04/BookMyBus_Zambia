<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Terms of Service — BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "on-secondary": "#ffffff",
                        "on-secondary-container": "#632f00",
                        "secondary-container": "#ff8921",
                        "primary-fixed-dim": "#7edb83",
                        "inverse-primary": "#7edb83",
                        background: "#f9f9fc",
                        "inverse-on-surface": "#f0f0f3",
                        "tertiary-container": "#d1200f",
                        "surface-container-lowest": "#ffffff",
                        "outline-variant": "#bfcaba",
                        "on-tertiary": "#ffffff",
                        surface: "#f9f9fc",
                        "on-primary": "#ffffff",
                        "primary-fixed": "#99f89d",
                        "surface-variant": "#e2e2e5",
                        "on-primary-fixed": "#002106",
                        "surface-bright": "#f9f9fc",
                        "surface-dim": "#dadadc",
                        outline: "#6f7a6c",
                        primary: "#00601f",
                        "on-primary-fixed-variant": "#00531a",
                        "surface-container-low": "#f3f3f6",
                        "on-surface-variant": "#3f493e",
                        "surface-container-high": "#e8e8ea",
                        "on-surface": "#1a1c1e",
                        "primary-container": "#197b30",
                        "error-container": "#ffdad6",
                        "surface-container-highest": "#e2e2e5",
                        "surface-tint": "#006e25",
                        error: "#ba1a1a",
                        "surface-container": "#eeeef0",
                        secondary: "#954a00",
                        tertiary: "#a80800",
                        "on-error": "#ffffff",
                        "on-background": "#1a1c1e",
                    },
                    fontFamily: {
                        headline: ["Manrope"],
                        body: ["Inter"],
                    },
                },
            },
        };
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
            vertical-align: middle;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #00601f 0%, #197b30 100%);
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface min-h-screen flex flex-col">

    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none">
        <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
            <div class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline">
                <a href="{{ route('home') }}">BookMyBus Zambia</a>
            </div>
            <div class="hidden md:flex items-center gap-8 font-headline tracking-tight font-bold text-sm">
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('home') }}">Find Trips</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('booking.lookup') }}">My Bookings</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('operator.login') }}">Operator Portal</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('support.page') }}">Support</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-28 pb-20 max-w-4xl mx-auto px-6 flex-1">
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
    </main>

    <!-- Footer -->
    <footer class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                <span class="font-manrope font-bold text-zinc-900 dark:text-zinc-100 block mb-2">BookMyBus Zambia</span>
                <p class="font-inter text-xs text-zinc-500 dark:text-zinc-400">© {{ date('Y') }} BookMyBus Zambia. Premium Travel Excellence.</p>
            </div>
            <div class="flex flex-wrap gap-6 md:justify-end">
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="{{ route('terms-of-service') }}">Terms of Service</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="{{ route('carrier-partners') }}">Carrier Partners</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="{{ route('contact-us') }}">Contact Us</a>
            </div>
        </div>
    </footer>
</body>
</html>