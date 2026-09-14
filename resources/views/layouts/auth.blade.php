<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'BookMyBus Zambia') — BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    <script>
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
        body { font-family: 'Inter', sans-serif; }
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
    @stack('head')
</head>

<body class="@yield('body_class', 'bg-surface font-body text-on-surface min-h-screen flex')">

    <!-- Skip to content link (keyboard / screen-reader users) -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:rounded-lg focus:bg-primary focus:text-white focus:text-sm focus:font-bold">Skip to content</a>

    <div id="main-content" class="flex w-full">
        @if(session('success'))
            <div role="status" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div role="alert" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div role="alert" class="fixed top-4 left-1/2 -translate-x-1/2 z-50 bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @yield('content')
    </div>

    @stack('scripts')
</body>

</html>
