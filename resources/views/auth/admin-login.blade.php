<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Sign In — BookMyBus Zambia</title>

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
        }

        /* Brand gradient — always dark, intentional */
        .auth-gradient { background: linear-gradient(135deg, #001a0a 0%, #004614 50%, #00601f 100%); }

        /* Card — theme-aware */
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
        <div class="w-full max-w-[420px] relative z-10">
            <div class="text-center mb-8">
                <div class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-sm px-6 py-3 rounded-2xl border border-white/20 mb-6">
                    <span class="material-symbols-outlined text-white text-3xl">admin_panel_settings</span>
                    <span class="text-white font-headline font-extrabold text-xl tracking-tight">Admin Portal</span>
                </div>
                <h1 class="text-white text-2xl font-headline font-extrabold tracking-tight">Welcome Back</h1>
                <p class="text-white/70 text-sm mt-1">Sign in to manage the platform</p>
            </div>
            <div class="auth-card rounded-3xl shadow-2xl p-6 md:p-8">
                <form action="{{ route('admin.login') }}" method="POST" class="space-y-5">
                    @csrf
                    @if($errors->any())
                        <div class="bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error">
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                    @if(session('status'))
                        <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="email">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="input-field w-full rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none"
                            placeholder="admin@bookmybus.co.zm" required autofocus />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant mb-1.5 uppercase tracking-wider" for="password">Password</label>
                        <input type="password" name="password" id="password"
                            class="input-field w-full rounded-xl px-4 py-3 text-sm text-on-surface focus:outline-none"
                            placeholder="••••••••" required />
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 text-sm text-on-surface-variant cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary focus:ring-primary" />
                            Remember me
                        </label>
                    </div>
                    <button type="submit" class="btn-primary w-full py-3 rounded-xl text-on-primary font-bold text-sm flex items-center justify-center gap-2">
                        <span>Sign In</span>
                        <span class="material-symbols-outlined text-lg">login</span>
                    </button>
                </form>
                <div class="mt-6 text-center">
                    <a href="{{ route('home') }}" class="text-sm text-on-surface-variant hover:text-primary transition-colors">
                        <span class="material-symbols-outlined text-sm">arrow_back</span>
                        Back to BookMyBus Zambia
                    </a>
                </div>
            </div>
            <div class="text-center mt-6">
                <p class="text-white/50 text-xs">© {{ date('Y') }} BookMyBus Zambia. Admin Panel.</p>
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