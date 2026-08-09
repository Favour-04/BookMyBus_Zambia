<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us — BookMyBus Zambia</title>
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
            <span class="text-secondary font-bold tracking-widest text-xs uppercase">Get in Touch</span>
            <h1 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2">Contact Us</h1>
            <p class="text-on-surface-variant mt-3 max-w-2xl mx-auto">
                We're here to help. Reach out to our team for any questions, feedback, or support.
            </p>
        </div>

        <!-- Contact Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 text-center hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-on-primary-container">call</span>
                </div>
                <h3 class="font-headline text-lg font-bold mb-2">Call Us</h3>
                <p class="text-on-surface-variant text-sm mb-3">Available 24/7 for emergencies</p>
                <p class="font-bold">+260 97 1234567</p>
                <p class="font-bold">+260 96 7654321</p>
            </div>

            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 text-center hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-on-primary-container">mail</span>
                </div>
                <h3 class="font-headline text-lg font-bold mb-2">Email Us</h3>
                <p class="text-on-surface-variant text-sm mb-3">Response within 24 hours</p>
                <p class="font-bold">support@bookmybus.zm</p>
                <p class="font-bold">info@bookmybus.zm</p>
            </div>

            <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15 text-center hover:shadow-lg transition-shadow">
                <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-on-primary-container">location_on</span>
                </div>
                <h3 class="font-headline text-lg font-bold mb-2">Visit Us</h3>
                <p class="text-on-surface-variant text-sm mb-3">Head Office</p>
                <p class="font-bold">Plot 123, Cairo Road</p>
                <p class="font-bold">Lusaka, Zambia</p>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="bg-surface-container-lowest rounded-2xl p-8 border border-outline-variant/15">
            <h2 class="font-headline text-2xl font-bold mb-6">Send us a message</h2>
            <form class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Your Name</label>
                        <input type="text" placeholder="Enter your full name"
                            class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email Address</label>
                        <input type="email" placeholder="Enter your email"
                            class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Subject</label>
                    <select class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
                        <option>Booking Issue</option>
                        <option>Payment Problem</option>
                        <option>Cancellation Request</option>
                        <option>General Inquiry</option>
                        <option>Feedback</option>
                        <option>Partnership</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Message</label>
                    <textarea rows="5" placeholder="Describe your issue or question..."
                        class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors"></textarea>
                </div>
                <button type="submit"
                    class="hero-gradient text-white font-headline font-bold text-sm px-8 py-3.5 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 w-full md:w-auto">
                    <span class="material-symbols-outlined text-lg">send</span>
                    Send Message
                </button>
            </form>
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