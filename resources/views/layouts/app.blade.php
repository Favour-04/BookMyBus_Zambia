<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title>@yield('title', 'BookMyBus Zambia — Premium Travel Excellence')</title>

    <!-- Dark mode bootstrap: runs before paint to prevent flash of wrong theme -->
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var useDark = stored === 'dark' || (!stored && prefersDark);
                var root = document.documentElement;
                if (useDark) {
                    root.classList.add('dark');
                    root.classList.remove('light');
                } else {
                    root.classList.add('light');
                    root.classList.remove('dark');
                }
            } catch (e) {
                // localStorage blocked (private mode, etc.) — fall through to default light
            }
        })();
    </script>

    <!-- Tailwind CSS with Forms & Container Queries -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <!-- Google Fonts & Material Symbols -->
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap"
        rel="stylesheet" />

    <!-- Tailwind Configuration -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        // All semantic tokens point at CSS variables so they can
                        // swap between light/dark at runtime without regenerating CSS.
                        "primary": "var(--color-primary)",
                        "on-primary": "var(--color-on-primary)",
                        "primary-container": "var(--color-primary-container)",
                        "on-primary-container": "var(--color-on-primary-container)",
                        "primary-fixed": "var(--color-primary-fixed)",
                        "primary-fixed-dim": "var(--color-primary-fixed-dim)",
                        "on-primary-fixed": "var(--color-on-primary-fixed)",
                        "on-primary-fixed-variant": "var(--color-on-primary-fixed-variant)",
                        "secondary": "var(--color-secondary)",
                        "on-secondary": "var(--color-on-secondary)",
                        "secondary-container": "var(--color-secondary-container)",
                        "on-secondary-container": "var(--color-on-secondary-container)",
                        "tertiary": "var(--color-tertiary)",
                        "on-tertiary": "var(--color-on-tertiary)",
                        "tertiary-container": "var(--color-tertiary-container)",
                        "surface": "var(--color-surface)",
                        "on-surface": "var(--color-on-surface)",
                        "surface-variant": "var(--color-surface-variant)",
                        "on-surface-variant": "var(--color-on-surface-variant)",
                        "surface-container-lowest": "var(--color-surface-container-lowest)",
                        "surface-container-low": "var(--color-surface-container-low)",
                        "surface-container": "var(--color-surface-container)",
                        "surface-container-high": "var(--color-surface-container-high)",
                        "surface-container-highest": "var(--color-surface-container-highest)",
                        "outline": "var(--color-outline)",
                        "outline-variant": "var(--color-outline-variant)",
                        "error": "var(--color-error)",
                        "on-error": "var(--color-on-error)",
                        "error-container": "var(--color-error-container)",
                        "on-error-container": "var(--color-on-error-container)",
                        "background": "var(--color-background)",
                        "on-background": "var(--color-on-background)",
                    },
                    fontFamily: {
                        headline: ["Manrope", "sans-serif"],
                        body: ["Inter", "sans-serif"],
                        label: ["Inter", "sans-serif"]
                    }
                }
            }
        }
    </script>

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }

        .card-hover {
            transition: box-shadow 0.2s ease;
        }

        .card-hover:hover {
            box-shadow: 0 10px 15px -3px var(--color-shadow), 0 4px 6px -4px var(--color-shadow);
        }
        .editorial-shadow {
            box-shadow: 0 24px 40px var(--color-shadow);
        }
        /* Print support — hide nav/footer and force white background when printing. */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
        }

        /* ============================================================
           Semantic color tokens — LIGHT (default)
           ============================================================ */
        :root {
            color-scheme: light;

            --color-primary: #00601f;
            --color-on-primary: #ffffff;
            --color-primary-container: #197b30;
            --color-on-primary-container: #b1ffb1;
            --color-primary-fixed: #99f89d;
            --color-primary-fixed-dim: #7edb83;
            --color-on-primary-fixed: #002106;
            --color-on-primary-fixed-variant: #00531a;

            --color-secondary: #954a00;
            --color-on-secondary: #ffffff;
            --color-secondary-container: #ff8921;
            --color-on-secondary-container: #632f00;

            --color-tertiary: #a80800;
            --color-on-tertiary: #ffffff;
            --color-tertiary-container: #d1200f;

            --color-surface: #f9f9fc;
            --color-on-surface: #1a1c1e;
            --color-surface-variant: #e2e2e5;
            --color-on-surface-variant: #3f493e;
            --color-surface-container-lowest: #ffffff;
            --color-surface-container-low: #f3f3f6;
            --color-surface-container: #eeeef0;
            --color-surface-container-high: #e8e8ea;
            --color-surface-container-highest: #e2e2e5;

            --color-outline: #6f7a6c;
            --color-outline-variant: #bfcaba;

            --color-error: #ba1a1a;
            --color-on-error: #ffffff;
            --color-error-container: #ffdad6;
            --color-on-error-container: #93000a;

            --color-background: #f9f9fc;
            --color-on-background: #1a1c1e;
            --color-shadow: rgba(26, 28, 30, 0.05);
        }

        /* ============================================================
           Semantic color tokens — DARK
           Follows Material 3 dark guidance: surfaces invert,
           brand accents are lifted for contrast on dark backgrounds.
           ============================================================ */
        .dark {
            color-scheme: dark;

            --color-primary: #7edb83;
            --color-on-primary: #003910;
            --color-primary-container: #00531a;
            --color-on-primary-container: #99f89d;
            --color-primary-fixed: #99f89d;
            --color-primary-fixed-dim: #7edb83;
            --color-on-primary-fixed: #002106;
            --color-on-primary-fixed-variant: #00531a;

            --color-secondary: #ffb784;
            --color-on-secondary: #4d2600;
            --color-secondary-container: #713700;
            --color-on-secondary-container: #ffdcc6;

            --color-tertiary: #ffb4a7;
            --color-on-tertiary: #690100;
            --color-tertiary-container: #920600;

            --color-surface: #121316;
            --color-on-surface: #e3e2e6;
            --color-surface-variant: #3f493e;
            --color-on-surface-variant: #bfcaba;
            --color-surface-container-lowest: #0d0e11;
            --color-surface-container-low: #1a1c1e;
            --color-surface-container: #1e2022;
            --color-surface-container-high: #282a2d;
            --color-surface-container-highest: #333538;

            --color-outline: #899383;
            --color-outline-variant: #3f493e;

            --color-error: #ffb4ab;
            --color-on-error: #690005;
            --color-error-container: #93000a;
            --color-on-error-container: #ffdad6;

            --color-background: #121316;
            --color-on-background: #e3e2e6;
            --color-shadow: rgba(0, 0, 0, 0.45);
        }
    </style>
    @stack('styles')
</head>

<body class="bg-surface font-body text-on-surface min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none no-print">
        <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
            <a href="{{ route('home') }}"
                class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline">
                BookMyBus<span class="text-on-surface dark:text-zinc-300"> Zambia</span>
            </a>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center gap-8 font-headline tracking-tight font-bold text-sm">
                <a href="{{ route('home') }}"
                class="{{ request()->routeIs('home') || request()->routeIs('trips.search') || request()->routeIs('booking.seats') ? 'text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1' : 'text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors' }}">
                    Find Trips
                </a>
                <a href="{{ route('booking.lookup') }}"
                    class="{{ request()->routeIs('booking.lookup*') ? 'text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1' : 'text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors' }}">
                    My Bookings
                </a>
                <a href="{{ route('operator.login') }}"
                    class="{{ request()->routeIs('operator.*') ? 'text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1' : 'text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors' }}">
                    Operator Portal
                </a>
                <a href="{{ route('support.page') }}"
                    class="{{ request()->routeIs('support.*') || request()->routeIs('contact-us') ? 'text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1' : 'text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors' }}">
                    Support
                </a>
            </div>

            <!-- Auth Actions + Theme Toggle -->
            <div class="flex items-center gap-4">
                <!-- Theme Toggle -->
                <button type="button" id="themeToggle" aria-label="Toggle dark mode" title="Toggle dark mode"
                    class="w-9 h-9 flex items-center justify-center rounded-lg text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:text-green-900 dark:hover:text-green-100 transition-colors">
                    <span class="material-symbols-outlined text-xl" id="themeToggleIcon">dark_mode</span>
                </button>

                @auth
                <a href="{{ route('profile') }}"
                    class="text-green-800 dark:text-green-400 font-headline font-bold text-sm px-3 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors rounded-lg flex items-center gap-1">
                    <span class="material-symbols-outlined">account_circle</span>
                    <span class="hidden sm:inline">My Account</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="text-xs font-bold text-outline dark:text-zinc-500 hover:text-error transition-colors px-2 py-1">
                        Sign Out
                    </button>
                </form>
                @else
                <a href="{{ route('login') }}"
                    class="text-green-800 dark:text-green-400 font-headline font-bold text-sm px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors rounded-lg flex items-center gap-2">
                    <span>Sign In</span>
                    <span class="material-symbols-outlined text-sm">account_circle</span>
                </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Dynamic Content -->
    <main class="flex-1 pt-24 @yield('main-class', 'pb-16 max-w-7xl mx-auto px-6 w-full')">
        @yield('content')
    </main>

    <!-- Global Footer -->
    <footer class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 no-print">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="flex flex-col gap-2">
                <span class="font-headline font-bold text-zinc-900 dark:text-zinc-100 text-lg">BookMyBus Zambia</span>
                <p class="font-body text-xs text-zinc-500 dark:text-zinc-400 max-w-xs">
                    © {{ date('Y') }} BookMyBus Zambia. Premium Travel Excellence. Your reliable partner for
                    trans-Zambian journeys.
                </p>
            </div>
            <div class="flex flex-wrap md:justify-end gap-6 md:gap-8">
                <a href="{{ route('privacy-policy') }}"
                    class="font-body text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80">Privacy
                    Policy</a>
                <a href="{{ route('terms-of-service') }}"
                    class="font-body text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80">Terms
                    of Service</a>
                <a href="{{ route('carrier-partners') }}"
                    class="font-body text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80">Carrier
                    Partners</a>
                <a href="{{ route('contact-us') }}"
                    class="font-body text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80">Contact
                    Us</a>
            </div>
        </div>
    </footer>

    <!-- Theme Toggle Script -->
    <script>
        (function () {
            var root = document.documentElement;
            var toggle = document.getElementById('themeToggle');
            var icon = document.getElementById('themeToggleIcon');

            function syncIcon() {
                // Show the icon for the mode you'd switch TO
                icon.textContent = root.classList.contains('dark') ? 'light_mode' : 'dark_mode';
            }

            toggle.addEventListener('click', function () {
                var goingDark = !root.classList.contains('dark');
                root.classList.toggle('dark', goingDark);
                root.classList.toggle('light', !goingDark);
                try {
                    localStorage.setItem('theme', goingDark ? 'dark' : 'light');
                } catch (e) { /* localStorage unavailable — ignore */ }
                syncIcon();
            });

            syncIcon();
        })();
    </script>

    @stack('scripts')
</body>

</html>