<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BookMyBus Zambia')</title>

    <!-- Vite Assets -->
    @vite('resources/css/app.css')

    <!-- Material Symbols Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Custom Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Admin Navigation -->
    <nav class="bg-white border-b border-zinc-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="material-symbols-outlined text-primary text-3xl mr-2">directions_bus</span>
                    <span class="font-headline font-bold text-xl text-on-surface">BookMyBus Zambia</span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-zinc-600">Admin Portal</span>
                    <a href="/audit-logs" class="text-sm text-primary hover:text-primary/80 font-medium">Audit Logs</a>
                    <a href="/system-reports" class="text-sm text-primary hover:text-primary/80 font-medium">System Reports</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    @yield('content')

    @stack('scripts')
</body>
</html>
