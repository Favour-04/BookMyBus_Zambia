<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Privacy Policy — BookMyBus Zambia</title>
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