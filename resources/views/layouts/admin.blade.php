<!DOCTYPE html>
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
