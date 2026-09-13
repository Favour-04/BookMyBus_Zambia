<!DOCTYPE html>
<<<<<<< HEAD
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal') | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#00601f", "on-primary": "#ffffff",
                        "primary-container": "#197b30",
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

    <!-- Sidebar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline text-xl font-extrabold text-primary uppercase tracking-tighter">BookMyBus</h1>
            <p class="text-xs text-on-surface-variant opacity-70">Admin Portal</p>
        </div>
        <nav class="flex-grow space-y-1 custom-scrollbar overflow-y-auto">
            @include('layouts.partials.admin-nav')
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-error transition-colors w-full">
                    <span class="material-symbols-outlined">logout</span><span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">@yield('page_title', 'Admin Portal')</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">admin_panel_settings</span>
                </div>
            </div>
        </header>

        <div class="p-8">
            @if(session('success'))
                <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary mb-6">{{ session('success') }}</div>
            @endif
            @if(session('status'))
                <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 text-sm text-primary mb-6">{{ session('status') }}</div>
            @endif
            @if(session('error'))
                <div class="bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error mb-6">{{ session('error') }}</div>
            @endif
            @if(session('warning'))
                <div class="bg-tertiary/10 border border-tertiary/20 rounded-xl p-4 text-sm text-tertiary mb-6">{{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-error-container/20 border border-error/20 rounded-xl p-4 text-sm text-error mb-6">
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
=======
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · {{ config('app.name', 'BookMyBus Zambia') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans text-slate-800 antialiased">
    <div class="min-h-screen flex flex-col">

        {{-- Top navigation --}}
        <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ Auth::check() ? route('admin.dashboard') : url('/') }}" class="flex items-center gap-2 font-semibold text-lg text-slate-900">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-700 text-white text-sm font-bold">BB</span>
                        BookMyBus <span class="text-emerald-700">Zambia</span>
                    </a>

                    @auth
                        <nav class="hidden md:flex items-center gap-1">
                            <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                            <a href="{{ route('admin.search.results') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.search.results') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Search Results</a>
                            <a href="{{ route('admin.profile') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.profile') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Profile</a>
                            <a href="{{ route('admin.support') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.support') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Support</a>
                        </nav>

                        <div class="flex items-center gap-4">
                            <span class="hidden sm:block text-sm text-slate-500">{{ Auth::user()->full_name }}</span>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-slate-600 hover:text-red-600">Log out</button>
                            </form>
                            <button type="button" id="mobile-menu-btn" class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-slate-500 hover:bg-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                        </div>
                    @else
                        <div class="flex items-center gap-2">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-emerald-700">Log in</a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-emerald-700 rounded-md hover:bg-emerald-800">Register</a>
                        </div>
                    @endauth
                </div>

                @auth
                    <nav id="mobile-menu" class="hidden md:hidden pb-4 flex flex-col gap-1">
                        <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
                        <a href="{{ route('admin.search.results') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.search.results') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Search Results</a>
                        <a href="{{ route('admin.profile') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.profile') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Profile</a>
                        <a href="{{ route('admin.support') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.support') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-100' }}">Support</a>
                    </nav>
                @endauth
            </div>
        </header>

        {{-- Flash messages / validation errors --}}
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-4 space-y-3">
            @if (session('success'))
                <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-800 px-4 py-3 text-sm">
                    <p class="font-medium mb-1">Please fix the following:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Page content --}}
        <main class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

        <footer class="border-t border-slate-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} BookMyBus Zambia. All rights reserved.</p>
                <p>Powered by MTN Money &middot; Airtel Money &middot; Zamtel Kwacha</p>
            </div>
        </footer>
    </div>

    <script>
        const mobileBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));
        }
    </script>

    @stack('scripts')
</body>
</html>
>>>>>>> 4401be0635ec745e70f8772aaef936d9d5c549d3
