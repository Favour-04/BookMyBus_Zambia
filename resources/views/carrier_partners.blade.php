<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Carrier Partners — BookMyBus Zambia</title>
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
    <main class="pt-28 pb-20 max-w-5xl mx-auto px-6 flex-1">
        <div class="mb-10 text-center">
            <span class="text-secondary font-bold tracking-widest text-xs uppercase">Our Network</span>
            <h1 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2">Carrier Partners</h1>
            <p class="text-on-surface-variant mt-3 max-w-2xl mx-auto">
                We partner with Zambia's most trusted and verified bus operators to bring you safe, comfortable, and reliable travel across the nation.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Partner 1 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">directions_bus</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">Euro-Trans</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Premium inter-city coach services connecting Lusaka, Kitwe, Ndola, and Livingstone with modern fleet and professional drivers.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.8</span> · 2,300+ trips completed
                </div>
            </div>

            <!-- Partner 2 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">commute</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">Power Tools Bus</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Reliable daily departures on the Copperbelt corridor with comfortable seating and on-time performance.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.6</span> · 1,800+ trips completed
                </div>
            </div>

            <!-- Partner 3 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">airport_shuttle</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">Mazhandu Family Bus</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Zambia's largest bus operator serving all major routes nationwide with a focus on safety and comfort.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.7</span> · 5,100+ trips completed
                </div>
            </div>

            <!-- Partner 4 -->
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-primary-container text-2xl">electric_bolt</span>
                    </div>
                    <div>
                        <h3 class="font-headline text-xl font-bold">FM Traveller</h3>
                        <span class="text-xs font-bold text-primary bg-primary/10 px-2 py-1 rounded-full">Verified Partner</span>
                    </div>
                </div>
                <p class="text-on-surface-variant text-sm leading-relaxed">
                    Express services between Lusaka and the Copperbelt with modern amenities and competitive fares.
                </p>
                <div class="mt-4 flex items-center gap-2 text-xs text-on-surface-variant">
                    <span class="material-symbols-outlined text-sm">star</span>
                    <span class="font-bold">4.5</span> · 1,200+ trips completed
                </div>
            </div>
        </div>

        <!-- Become a Partner CTA -->
        <div class="mt-12 bg-primary rounded-2xl p-10 text-center text-white">
            <h2 class="font-headline text-3xl font-extrabold mb-3">Are you a bus operator?</h2>
            <p class="text-primary-fixed max-w-xl mx-auto mb-6">
                Join our platform and reach thousands of passengers across Zambia. Manage your fleet, trips, and bookings from one dashboard.
            </p>
            <a href="{{ route('operator.login') }}"
                class="inline-block bg-white text-primary font-bold px-8 py-3 rounded-xl hover:bg-primary-fixed transition-colors">
                Get Started
            </a>
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