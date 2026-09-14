<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BookMyBus Zambia - Sign Up</title>

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

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Manrope:wght@700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />

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
                    },
                    fontFamily: { headline: ["Manrope"], body: ["Inter"] },
                }
            }
        }
    </script>

    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
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
        }

        .auth-gradient {
            background: linear-gradient(135deg, #00601f 0%, #197b30 50%, #2d8f47 100%);
        }

        .auth-card {
            backdrop-filter: blur(20px);
            background: color-mix(in srgb, var(--color-surface-container-lowest) 95%, transparent);
        }

        .input-field {
            transition: all 0.2s ease;
            background: var(--color-surface-container-low);
            border: 2px solid transparent;
            color: var(--color-on-surface);
        }
        .input-field:focus {
            background: var(--color-surface-container-lowest);
            border-color: var(--color-primary);
            box-shadow: 0 0 0 4px color-mix(in srgb, var(--color-primary) 10%, transparent);
        }
        .input-field.error {
            border-color: var(--color-error);
            background: color-mix(in srgb, var(--color-error-container) 40%, transparent);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-container) 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px color-mix(in srgb, var(--color-primary) 30%, transparent);
        }
        .btn-primary:active { transform: scale(0.98); }

        .floating-label { position: relative; }
        .floating-label input { padding-top: 20px; padding-bottom: 6px; }
        .floating-label label {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.2s ease;
            color: var(--color-outline);
            font-size: 14px;
            pointer-events: none;
        }
        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            top: 8px;
            transform: translateY(0);
            font-size: 10px;
            color: var(--color-primary);
            font-weight: 600;
        }

        .password-toggle { cursor: pointer; user-select: none; }
        .password-toggle:hover { color: var(--color-primary); }

        .strength-bar { height: 4px; border-radius: 4px; transition: all 0.3s ease; }

        .slide-enter { animation: slideIn 0.3s ease-out; }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

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
    </style>
</head>

<body>

    <button type="button" id="themeToggle" class="theme-toggle" aria-label="Toggle dark mode">
        <span class="material-symbols-outlined" id="themeToggleIcon">dark_mode</span>
    </button>

    <div class="min-h-screen flex items-center justify-center p-4 auth-gradient relative overflow-hidden">

        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        </div>

        <div class="w-full max-w-[440px] relative z-10">

            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-sm px-6 py-3 rounded-2xl border border-white/20 mb-6">
                    <span class="material-symbols-outlined text-white text-3xl">directions_bus</span>
                    <span class="text-white font-headline font-extrabold text-xl tracking-tight">BookMyBus Zambia</span>
                </div>
                <h1 class="text-white text-2xl font-headline font-extrabold tracking-tight">
                    Create Account
                </h1>
                <p class="text-white/70 text-sm mt-1">Start your journey with us</p>
            </div>

            <div class="auth-card rounded-3xl shadow-2xl p-6 md:p-8">

                <form id="register-form" action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="floating-label">
                        <input type="text" name="full_name" id="full_name"
                            class="input-field w-full rounded-xl px-4 text-on-surface text-sm placeholder-transparent focus:outline-none"
                            placeholder=" " value="{{ old('full_name') }}" required>
                        <label for="full_name">Full Name</label>
                        @error('full_name')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="floating-label">
                        <input type="email" name="email" id="email"
                            class="input-field w-full rounded-xl px-4 text-on-surface text-sm placeholder-transparent focus:outline-none"
                            placeholder=" " value="{{ old('email') }}" required>
                        <label for="email">Email Address</label>
                        @error('email')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="floating-label">
                        <input type="tel" name="phone_number" id="phone_number"
                            class="input-field w-full rounded-xl px-4 text-on-surface text-sm placeholder-transparent focus:outline-none"
                            placeholder=" " value="{{ old('phone_number') }}" required>
                        <label for="phone_number">Phone Number</label>
                        @error('phone_number')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="floating-label">
                        <input type="password" name="password" id="password"
                            class="input-field w-full rounded-xl px-4 text-on-surface text-sm placeholder-transparent focus:outline-none pr-12"
                            placeholder=" " required>
                        <label for="password">Password</label>
                        <button type="button" onclick="togglePassword()"
                            class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-primary transition-colors">
                            <span class="material-symbols-outlined text-lg" id="password-icon">visibility</span>
                        </button>
                        @error('password')
                            <p class="text-error text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1">
                        <div class="flex gap-1">
                            <div class="strength-bar flex-1" id="strength-1"></div>
                            <div class="strength-bar flex-1" id="strength-2"></div>
                            <div class="strength-bar flex-1" id="strength-3"></div>
                            <div class="strength-bar flex-1" id="strength-4"></div>
                        </div>
                        <p class="text-xs text-on-surface-variant" id="strength-text">Password strength</p>
                    </div>

                    <div class="floating-label">
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="input-field w-full rounded-xl px-4 text-on-surface text-sm placeholder-transparent focus:outline-none"
                            placeholder=" " required>
                        <label for="password_confirmation">Confirm Password</label>
                    </div>

                    <div class="flex items-start gap-2">
                        <input type="checkbox" name="terms" id="terms"
                            class="mt-0.5 rounded border-outline-variant text-primary focus:ring-primary"
                            required>
                        <label for="terms" class="text-xs text-on-surface-variant">
                            I agree to the
                            <a href="{{ route('terms-of-service') }}" class="text-primary font-medium hover:underline">Terms of Service</a>
                            and
                            <a href="{{ route('privacy-policy') }}" class="text-primary font-medium hover:underline">Privacy Policy</a>
                        </label>
                    </div>
                    @error('terms')
                        <p class="text-error text-xs">{{ $message }}</p>
                    @enderror

                    <button type="submit"
                        class="btn-primary w-full py-3 rounded-xl text-on-primary font-bold text-sm flex items-center justify-center gap-2">
                        <span>Create Account</span>
                        <span class="material-symbols-outlined text-lg">person_add</span>
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-on-surface-variant">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-bold text-primary hover:underline">
                        Sign in
                    </a>
                </div>

                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-outline-variant/30"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-surface-container-lowest text-on-surface-variant">or</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="bg-surface-container-low rounded-lg p-3 text-center">
                        <span class="material-symbols-outlined text-primary text-xl block mx-auto">lock</span>
                        <p class="text-on-surface-variant mt-1">Secure Booking</p>
                    </div>
                    <div class="bg-surface-container-low rounded-lg p-3 text-center">
                        <span class="material-symbols-outlined text-primary text-xl block mx-auto">smartphone</span>
                        <p class="text-on-surface-variant mt-1">Mobile Money</p>
                    </div>
                    <div class="bg-surface-container-low rounded-lg p-3 text-center">
                        <span class="material-symbols-outlined text-primary text-xl block mx-auto">support_agent</span>
                        <p class="text-on-surface-variant mt-1">24/7 Support</p>
                    </div>
                    <div class="bg-surface-container-low rounded-lg p-3 text-center">
                        <span class="material-symbols-outlined text-primary text-xl block mx-auto">qr_code</span>
                        <p class="text-on-surface-variant mt-1">Digital Tickets</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-6">
                <p class="text-white/50 text-xs">
                    © {{ date('Y') }} BookMyBus Zambia. All rights reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('password-icon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                passwordInput.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = calculateStrength(password);
            const bars = document.querySelectorAll('.strength-bar');
            const text = document.getElementById('strength-text');

            bars.forEach((bar, index) => {
                bar.className = 'strength-bar flex-1 rounded';
                if (index < strength.level) {
                    bar.classList.add(strength.color);
                } else {
                    bar.classList.add('bg-outline-variant/30');
                }
            });

            text.textContent = strength.label;
            text.style.color = strength.textColor;
        });

        function calculateStrength(password) {
            let score = 0;
            if (password.length >= 8) score++;
            if (password.match(/[a-z]/)) score++;
            if (password.match(/[A-Z]/)) score++;
            if (password.match(/[0-9]/)) score++;
            if (password.match(/[^a-zA-Z0-9]/)) score++;

            const levels = {
                0: { level: 0, label: 'Too weak', color: 'bg-error', textColor: 'var(--color-error)' },
                1: { level: 1, label: 'Weak', color: 'bg-error', textColor: 'var(--color-error)' },
                2: { level: 2, label: 'Fair', color: 'bg-[#ff8921]', textColor: 'var(--color-secondary, #954a00)' },
                3: { level: 3, label: 'Good', color: 'bg-[#197b30]', textColor: 'var(--color-primary-container, #00601f)' },
                4: { level: 4, label: 'Strong', color: 'bg-primary', textColor: 'var(--color-primary)' },
                5: { level: 5, label: 'Very Strong', color: 'bg-primary', textColor: 'var(--color-primary)' },
            };

            return levels[Math.min(score, 5)] || levels[0];
        }

        document.querySelectorAll('button, a').forEach(el => {
            el.addEventListener('mousedown', () => el.classList.add('scale-[0.98]'));
            el.addEventListener('mouseup', () => el.classList.remove('scale-[0.98]'));
            el.addEventListener('mouseleave', () => el.classList.remove('scale-[0.98]'));
        });

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