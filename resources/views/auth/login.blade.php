<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign In — BookMyBus Zambia</title>
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
        .panel-gradient {
            background: linear-gradient(160deg, #00601f 0%, #197b30 60%, #1a3d22 100%);
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface min-h-screen flex">

    <!-- Left decorative panel -->
    <div class="hidden lg:flex lg:w-1/2 panel-gradient flex-col justify-between p-12 relative overflow-hidden">
        <!-- Background pattern -->
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
        <!-- Decorative bus icon blob -->
        <div class="absolute -bottom-16 -right-16 w-72 h-72 rounded-full bg-white/5"></div>
        <div class="absolute -bottom-8 -right-8 w-48 h-48 rounded-full bg-white/5"></div>

        <!-- Logo -->
        <div class="relative z-10">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="material-symbols-outlined text-white text-3xl">directions_bus</span>
                <span class="font-headline font-extrabold text-white text-xl tracking-tighter">BookMyBus Zambia</span>
            </a>
        </div>

        <!-- Center content -->
        <div class="relative z-10 flex-1 flex flex-col justify-center py-12">
            <span class="text-secondary-container font-bold tracking-widest text-xs uppercase mb-4">Traveler Portal</span>
            <h1 class="font-headline text-5xl font-extrabold text-white tracking-tighter leading-tight mb-6">
                Your journey<br/>starts here.
            </h1>
            <p class="text-white/70 font-body text-base max-w-sm leading-relaxed">
                Search routes, compare fares, and book your seat — all from one place. Pay with Airtel Money or MTN.
            </p>

            <!-- Feature pills -->
            <div class="flex flex-col gap-3 mt-10">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">confirmation_number</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">Digital tickets delivered to your email</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">event_seat</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">Choose your preferred seat</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-white/15 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-white text-sm">smartphone</span>
                    </div>
                    <span class="text-white/80 text-sm font-body">Pay via mobile money — no card needed</span>
                </div>
            </div>
        </div>

        <!-- Bottom links -->
        <div class="relative z-10">
            <p class="text-white/40 text-xs font-body">
                Are you a bus operator?
                <a href="{{ route('operator.login') }}" class="text-secondary-container font-semibold hover:underline ml-1">Operator Portal →</a>
            </p>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 py-12 sm:px-12 lg:px-20">

        <!-- Mobile logo -->
        <div class="lg:hidden mb-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-2xl">directions_bus</span>
                <span class="font-headline font-extrabold text-primary text-lg tracking-tighter">BookMyBus Zambia</span>
            </a>
        </div>

        <div class="max-w-sm w-full mx-auto lg:mx-0">

            <div class="mb-8">
                <h2 class="font-headline text-3xl font-extrabold text-on-surface tracking-tight">Welcome back</h2>
                <p class="text-on-surface-variant font-body mt-2 text-sm">Sign in to your traveler account to continue.</p>
            </div>

            <!-- Session / Validation errors -->
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

            <form action="{{ route('login') }}" method="POST" class="flex flex-col gap-5">
                @csrf

                <!-- Email -->
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

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="password" class="block text-[10px] uppercase font-bold text-outline tracking-widest">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs text-primary font-semibold hover:underline">Forgot password?</a>
                    </div>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-lg">lock</span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface font-body text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors placeholder:text-outline @error('password') border-error focus:border-error focus:ring-error @enderror"
                        />
                        <button type="button" onclick="togglePassword()" class="absolute right-3 top-1/2 -translate-y-1/2 text-outline hover:text-on-surface transition-colors">
                            <span class="material-symbols-outlined text-lg" id="eye-icon">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Remember me -->
                <div class="flex items-center gap-2">
                    <input id="remember" name="remember" type="checkbox" class="rounded border-outline-variant text-primary focus:ring-primary" />
                    <label for="remember" class="text-sm text-on-surface-variant font-body">Keep me signed in</label>
                </div>

                <!-- Submit -->
                <button
                    type="submit"
                    class="w-full hero-gradient text-white font-headline font-extrabold py-3.5 rounded-lg hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 mt-1"
                >
                    <span>Sign In</span>
                    <span class="material-symbols-outlined text-lg">arrow_forward</span>
                </button>
            </form>

            <!-- Register link -->
            <p class="mt-6 text-center text-sm text-on-surface-variant font-body">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-primary font-semibold hover:underline">Create one</a>
            </p>

            <!-- Divider -->
            <div class="mt-8 pt-6 border-t border-outline-variant lg:hidden">
                <p class="text-center text-xs text-outline font-body">
                    Bus operator?
                    <a href="/operator/login" class="text-secondary font-semibold hover:underline ml-1">Go to Operator Portal</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }
    </script>
</body>
</html>
