<!-- TODO: This layout should be extended by all views. Created for payment_ticket.blade.php refactor -->

<!DOCTYPE html>
<html lang="en">

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
    </style>
    @stack('styles')
</head>

<body class="bg-surface font-body text-on-surface selection:bg-primary-container selection:text-on-primary-container min-h-screen flex flex-col">
    <!-- Top Navigation -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none">
        <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
            <div class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline">
                <a href="/">BookMyBus Zambia</a>
            </div>
            <div class="hidden md:flex items-center space-x-8 font-headline tracking-tight font-bold text-sm">
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('home') }}">Find Trips</a>
                <a class="text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1"
                    href="{{ route('booking.lookup') }}">My Bookings</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('operator.login') }}">Operator Portal</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="{{ route('support.page') }}">Support</a>
            </div>
            <div class="flex items-center space-x-4">
                @auth
                <a href="{{ route('profile') }}"
                    class="material-symbols-outlined text-green-800 dark:text-green-400 cursor-pointer">account_circle</a>
                @else
                <a href="{{ route('login') }}"
                    class="material-symbols-outlined text-green-800 dark:text-green-400 cursor-pointer">account_circle</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-24 pb-20 px-6 max-w-7xl mx-auto w-full flex-1">
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