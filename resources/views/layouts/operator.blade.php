<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Operator Portal') | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
        .chart-bar { transition: height 0.4s ease; }
        /* Visible keyboard focus for interactive controls */
        a:focus-visible, button:focus-visible, [tabindex]:focus-visible {
            outline: 2px solid #004614;
            outline-offset: 2px;
            border-radius: 0.5rem;
        }
        input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid #004614;
            outline-offset: 2px;
        }
        /* Mobile sidebar slide transition */
        #operator-sidebar { transition: transform 0.25s ease-in-out; }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#004614", "on-primary": "#ffffff",
                        "surface-container-low": "#f3f3f6", "surface-container": "#edeef1",
                        "surface-container-highest": "#e2e2e5", "surface-container-lowest": "#ffffff",
                        "surface": "#f9f9fc", "on-surface": "#1a1c1e",
                        "on-surface-variant": "#40493e", "outline-variant": "#bfcaba",
                        "outline": "#6f7a6c", "tertiary": "#7c0400",
                        "error-container": "#ffdad6", "error": "#ba1a1a",
                    },
                    fontFamily: { 'headline': ['Manrope', 'sans-serif'], 'body': ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    @stack('head')
</head>
<body class="bg-surface text-on-surface">

    <!-- Skip to content link (keyboard / screen-reader users) -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:rounded-lg focus:bg-primary focus:text-white focus:text-sm focus:font-bold">Skip to content</a>

    <!-- Mobile sidebar backdrop -->
    <div id="operator-sidebar-backdrop" class="fixed inset-0 bg-black/40 z-30 lg:hidden hidden" onclick="document.getElementById('operator-sidebar').classList.add('-translate-x-full'); this.classList.add('hidden');"></div>

    <!-- Sidebar -->
    <aside id="operator-sidebar" class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-40 -translate-x-full lg:translate-x-0">
        <div class="mb-10 px-2 flex items-center justify-between">
            <div>
                <h1 class="font-headline text-xl font-extrabold text-primary uppercase tracking-tighter">{{ $operator->company_name ?? 'Operator' }}</h1>
                <p class="text-xs text-on-surface-variant opacity-70">Operator Portal</p>
            </div>
            <!-- Close button (mobile only) -->
            <button type="button" class="lg:hidden text-on-surface-variant hover:text-primary" aria-label="Close menu" onclick="document.getElementById('operator-sidebar').classList.add('-translate-x-full'); document.getElementById('operator-sidebar-backdrop').classList.add('hidden');">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <nav class="flex-grow space-y-1 custom-scrollbar overflow-y-auto" aria-label="Operator navigation">
            @include('layouts.partials.operator-nav')
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20 space-y-1">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg {{ (request()->routeIs('operator.profile*')) ? 'text-primary font-bold' : 'text-on-surface-variant' }} hover:text-primary transition-colors" href="{{ route('operator.profile') }}">
                <span class="material-symbols-outlined">settings</span><span>Settings</span>
            </a>
            <form action="{{ route('operator.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-error transition-colors w-full">
                    <span class="material-symbols-outlined">logout</span><span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 min-h-screen">
        <header class="h-16 px-4 md:px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-20">
            <div class="flex items-center gap-3">
                <!-- Hamburger toggle (mobile only) -->
                <button type="button" class="lg:hidden flex items-center justify-center text-on-surface-variant hover:text-primary" aria-label="Open menu" aria-controls="operator-sidebar" aria-expanded="false" onclick="const sb=document.getElementById('operator-sidebar');const bd=document.getElementById('operator-sidebar-backdrop');const open=sb.classList.contains('-translate-x-full');sb.classList.toggle('-translate-x-full',!open);bd.classList.toggle('hidden',!open);this.setAttribute('aria-expanded',open);">
                    <span class="material-symbols-outlined">menu</span>
                </button>
                <h2 class="font-headline text-base font-bold text-primary">@yield('page_title', 'Operator Portal')</h2>
            </div>
            <div class="flex items-center gap-4">
                @yield('header_actions')
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">person</span>
                </div>
            </div>
        </header>

        <div id="main-content" class="p-4 md:p-8">
            @if(session('success'))
                <div role="status" class="bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary mb-6">{{ session('success') }}</div>
            @endif
            @if(session('status'))
                <div role="status" class="bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary mb-6">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div role="alert" class="bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error mb-6">{{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div role="alert" class="bg-tertiary/10 border border-tertiary/20 rounded-xl p-4 text-sm text-tertiary mb-6">{{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div role="alert" class="bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error mb-6">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>