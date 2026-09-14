<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'BookMyBus Zambia')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script>
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
        .bg-glass {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        /* Visible keyboard focus for interactive controls */
        a:focus-visible, button:focus-visible, [tabindex]:focus-visible {
            outline: 2px solid #00601f;
            outline-offset: 2px;
            border-radius: 0.5rem;
        }
        input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid #00601f;
            outline-offset: 2px;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-surface font-body text-on-surface selection:bg-primary-container selection:text-on-primary-container min-h-screen flex flex-col">

    <!-- Skip to content link (keyboard / screen-reader users) -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[60] focus:px-4 focus:py-2 focus:rounded-lg focus:bg-primary focus:text-white focus:text-sm focus:font-bold">Skip to content</a>

    <!-- Mobile menu backdrop -->
    <div id="nav-backdrop" class="fixed inset-0 bg-black/40 z-40 md:hidden hidden" onclick="document.getElementById('nav-menu').classList.add('hidden'); this.classList.add('hidden');"></div>

    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none" aria-label="Main navigation">
        <div class="flex justify-between items-center px-4 sm:px-6 py-4 max-w-7xl mx-auto w-full">
            <div class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline">
                <a href="/">BookMyBus Zambia</a>
            </div>
            <!-- Desktop nav links -->
            <div class="hidden md:flex items-center space-x-8 font-headline tracking-tight font-bold text-sm">
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('home') }}">Find Trips</a>
                <a class="text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1"
                    href="{{ route('my-bookings') }}">My Bookings</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('operator.login') }}">Operator Portal</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('support.page') }}">Support</a>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                <a href="{{ route('profile') }}"
                    class="material-symbols-outlined text-green-800 dark:text-green-400 cursor-pointer" aria-label="My profile">account_circle</a>
                @else
                <a href="{{ route('login') }}"
                    class="material-symbols-outlined text-green-800 dark:text-green-400 cursor-pointer" aria-label="Sign in">account_circle</a>
                @endauth
                <!-- Mobile hamburger toggle -->
                <button type="button" class="md:hidden flex items-center justify-center text-green-900 dark:text-green-100" aria-label="Open menu" aria-controls="nav-menu" aria-expanded="false" onclick="const m=document.getElementById('nav-menu');const bd=document.getElementById('nav-backdrop');const open=m.classList.contains('hidden');m.classList.toggle('hidden',!open);bd.classList.toggle('hidden',!open);this.setAttribute('aria-expanded',open);">
                    <span class="material-symbols-outlined">menu</span>
                </button>
            </div>
        </div>
        <!-- Mobile nav menu (slide-down) -->
        <div id="nav-menu" class="hidden md:hidden bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 px-4 py-4 space-y-3 font-headline tracking-tight font-bold text-sm">
            <a class="block text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors py-2"
                href="{{ route('home') }}">Find Trips</a>
            <a class="block text-green-900 dark:text-green-100 py-2"
                href="{{ route('my-bookings') }}">My Bookings</a>
            <a class="block text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors py-2"
                href="{{ route('operator.login') }}">Operator Portal</a>
            <a class="block text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors py-2"
                href="{{ route('support.page') }}">Support</a>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="main-content" class="pt-24 pb-20 px-4 sm:px-6 max-w-7xl mx-auto w-full flex-1">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="space-y-4">
                <div class="font-manrope font-bold text-zinc-900 dark:text-zinc-100">BookMyBus Zambia</div>
                <div class="font-inter text-xs text-zinc-500 dark:text-zinc-400">© 2024 BookMyBus Zambia. Premium Travel Excellence.</div>
            </div>
            <div class="flex flex-wrap gap-6 md:justify-end">
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80" href="{{ route('privacy-policy') }}">Privacy Policy</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80" href="{{ route('terms-of-service') }}">Terms of Service</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80" href="{{ route('carrier-partners') }}">Carrier Partners</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80" href="{{ route('contact-us') }}">Contact Us</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>