<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password — BookMyBus Zambia</title>

    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var useDark = stored === 'dark' || (!stored && prefersDark);
                var root = document.documentElement;
                if (useDark) { root.classList.add('dark'); root.classList.remove('light'); }
                else { root.classList.add('light'); root.classList.remove('dark'); }
            } catch (e) {}
        })();
    </script>

    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "var(--color-primary)",
                        "primary-container": "var(--color-primary-container)",
                        "on-primary": "var(--color-on-primary)",
                        surface: "var(--color-surface)",
                        "surface-container-low": "var(--color-surface-container-low)",
                        "surface-container-lowest": "var(--color-surface-container-lowest)",
                        "on-surface": "var(--color-on-surface)",
                        "on-surface-variant": "var(--color-on-surface-variant)",
                        outline: "var(--color-outline)",
                        "outline-variant": "var(--color-outline-variant)",
                        error: "var(--color-error)",
                        "error-container": "var(--color-error-container)",
                        "primary-fixed": "var(--color-primary-fixed)",
                        "secondary-container": "var(--color-secondary-container)",
                    },
                    fontFamily: { headline: ["Manrope"], body: ["Inter"] },
                }
            }
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background: var(--color-surface); }

        :root {
            color-scheme: light;
            --color-primary: #00601f;
            --color-primary-container: #197b30;
            --color-on-primary: #ffffff;
            --color-surface: #f9f9fc;
            --color-surface-container-low: #f3f3f6;
            --color-surface-container-lowest: #ffffff;
            --color-on-surface: #1a1c1e;
            --color-on-surface-variant: #3f493e;
            --color-outline: #6f7a6c;
            --color-outline-variant: #bfcaba;
            --color-error: #ba1a1a;
            --color-error-container: #ffdad6;
            --color-primary-fixed: #99f89d;
            --color-secondary-container: #ff8921;
            --color-secondary: #954a00;
        }

        .dark {
            color-scheme: dark;
            --color-primary: #7edb83;
            --color-primary-container: #00531a;
            --color-on-primary: #003910;
            --color-surface: #121316;
            --color-surface-container-low: #1a1c1e;
            --color-surface-container-lowest: #0d0e11;
            --color-on-surface: #e3e2e6;
            --color-on-surface-variant: #bfcaba;
            --color-outline: #899383;
            --color-outline-variant: #3f493e;
            --color-error: #ffb4ab;
            --color-error-container: #93000a;
            --color-primary-fixed: #99f89d;
            --color-secondary-container: #713700;
            --color-secondary: #ffb784;
        }

        .hero-gradient { background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-container) 100%); }
        .panel-gradient { background: linear-gradient(160deg, #00601f 0%, #197b30 60%, #1a3d22 100%); }

        .theme-toggle {
            position: fixed; top: 1rem; right: 1rem; z-index: 100;
            width: 2.5rem; height: 2.5rem;
            display: flex; align-items: center; justify-content: center;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            cursor: pointer;
            transition: background 0.2s ease;
        }
        .theme-toggle:hover { background: rgba(255, 255, 255, 0.25); }
        .dark .theme-toggle { background: rgba(0, 0, 0, 0.4); border-color: rgba(255, 255, 255, 0.1); }

        /* SVG icon color — theme-aware. SVGs using stroke="currentColor" or
           fill="currentColor" inherit this automatically. */
        .icon {
            color: var(--color-on-surface);
            transition: color 0.2s ease;
        }
        .icon-primary { color: var(--color-primary); }
        .icon-secondary { color: var(--color-secondary); }
        .icon-muted { color: var(--color-on-surface-variant); }
        .icon-error { color: var(--color-error); }
        .icon-on-primary { color: var(--color-on-primary); }
    </style>
</head>

<body class="bg-surface font-body text-on-surface min-h-screen flex">

    <button type="button" id="themeToggle" class="theme-toggle" aria-label="Toggle dark mode">
        <span class="material-symbols-outlined" id="themeToggleIcon">dark_mode</span>
    </button>

    <div class="hidden lg:flex lg:w-1/2 panel-gradient flex-col justify-between p-12 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>
        <div class="absolute -bottom-16 -right-16 w-72 h-72 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-8 -right-8 w-48 h-48 rounded-full bg-white/5"></div>

        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="material-symbols-outlined text-white text-3xl">directions_bus</span>
                <span class="font-headline font-extrabold text-white text-xl tracking-tighter">BookMyBus Zambia</span>
            </a>
        </div>

        <div class="relative z-10 flex-1 flex flex-col justify-center py-12">
            <span class="text-secondary-container font-bold tracking-widest text-xs uppercase mb-4">Account Recovery</span>
            <h1 class="font-headline text-5xl font-extrabold text-white tracking-tighter leading-tight mb-6">
                Don't worry,<br/>we've got you.
            </h1>
            <p class="text-white/70 font-body text-base max-w-sm leading-relaxed">
                Enter your email and we'll send you a secure link to reset your password and get you back on the road.
            </p>

            <div class="flex flex-col gap-3 mt-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">lock_reset</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">Secure password reset link</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">schedule</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">Link expires in 60 minutes</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">support_agent</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">24/7 support if you need help</span>
                </div>
            </div>
        </div>

        <div class="relative z-10">
            <p class="text-white/40 text-xs font-body">
                Remembered your password?
                <a href="{{ route('login') }}" class="text-secondary-container font-semibold hover:underline ml-1">Back to Sign In →</a>
            </p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-20">

        <div class="lg:hidden mb-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">directions_bus</span>
                <span class="font-headline font-extrabold text-primary text-lg tracking-tighter">BookMyBus Zambia</span>
            </a>
        </div>

        <div class="max-w-sm w-full mx-auto lg:mx-0">

            <div class="mb-8">
                <div class="w-12 h-12 rounded-xl bg-primary-fixed/40 flex items-center justify-center mb-4">
                    <span class="material-symbols-outlined text-primary text-2xl">lock_reset</span>
                </div>
                <h2 class="font-headline text-3xl font-extrabold text-on-surface tracking-tight">Reset Password</h2>
                <p class="text-on-surface-variant font-body mt-2 text-sm">Enter the email address associated with your account.</p>
            </div>

            @if(session('status'))
                <div class="mb-6 p-4 rounded-lg bg-primary-fixed/30 border border-primary-container/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
                    <p class="text-on-surface text-sm font-body">{{ session('status') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-lg bg-error-container border border-error/20 flex items-start gap-3">
                    <span class="material-symbols-outlined text-error text-sm mt-0.5">error</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-error text-sm font-body">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="flex flex-col gap-5">
                @csrf

                <div>
                    <label for="email" class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email Address</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">mail</span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="email"
                            placeholder="you@example.com"
                            class="w-full pl-10 pr-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-outline @error('email') border-error focus:border-error focus:ring-error @enderror"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full hero-gradient text-on-primary font-headline font-extrabold py-3.5 rounded-lg hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 mt-1"
                >
                    <span>Send Reset Link</span>
                    <span class="material-symbols-outlined text-lg">send</span>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-outline-variant lg:hidden">
                <p class="text-center text-xs text-outline font-body">
                    Remembered your password?
                    <a href="{{ route('login') }}" class="text-primary font-semibold hover:underline ml-1">Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var root = document.documentElement;
            var toggle = document.getElementById('themeToggle');
            var icon = document.getElementById('themeToggleIcon');
            function syncIcon() {
                icon.textContent = root.classList.contains('dark') ? 'light_mode' : 'dark_mode';
            }
            toggle.addEventListener('click', function () {
                var goingDark = !root.classList.contains('dark');
                root.classList.toggle('dark', goingDark);
                root.classList.toggle('light', !goingDark);
                try { localStorage.setItem('theme', goingDark ? 'dark' : 'light'); } catch (e) {}
                syncIcon();
            });
            syncIcon();
        })();
    </script>
</body>
</html>