<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log Entry | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .json-view { background: #f4f5f7; border-radius: 8px; padding: 16px; font-family: 'Cascadia Code', 'Fira Code', monospace; font-size: 13px; line-height: 1.6; overflow-x: auto; white-space: pre-wrap; word-break: break-word; }
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
</head>
<body class="bg-surface text-on-surface">
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline text-xl font-extrabold text-primary uppercase tracking-tighter">{{ $operator->company_name ?? 'Operator' }}</h1>
            <p class="text-xs text-on-surface-variant opacity-70">Operator Portal</p>
        </div>
        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.dashboard') }}"><span class="material-symbols-outlined">dashboard</span><span>Dashboard</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.trips.index') }}"><span class="material-symbols-outlined">directions_bus</span><span>Manage Trips</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.bookings.index') }}"><span class="material-symbols-outlined">book_online</span><span>All Bookings</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.customers.index') }}"><span class="material-symbols-outlined">people</span><span>Customers</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.revenue') }}"><span class="material-symbols-outlined">payments</span><span>Revenue</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.audit-log.index') }}"><span class="material-symbols-outlined">history</span><span>Audit Log</span></a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="{{ route('operator.profile') }}"><span class="material-symbols-outlined">settings</span><span>Settings</span></a>
        </div>
    </aside>
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Audit Log Entry</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center"><span class="material-symbols-outlined text-primary text-sm">history</span></div>
            </div>
        </header>
        <div class="p-8">
            <a href="{{ route('operator.audit-log.index') }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>Back to Audit Log
            </a>

            <!-- Entry Summary -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold
                            @php
                                $eventColor = match(true) {
                                    str_contains($log->event, 'login') || str_contains($log->event, 'logout') => 'bg-primary/10 text-primary',
                                    str_contains($log->event, 'cancelled') || str_contains($log->event, 'deleted') => 'bg-error-container/20 text-error',
                                    str_contains($log->event, 'created') => 'bg-tertiary/10 text-tertiary',
                                    str_contains($log->event, 'updated') || str_contains($log->event, 'changed') => 'bg-amber-100 text-amber-800',
                                    str_contains($log->event, 'viewed') || str_contains($log->event, 'exported') => 'bg-sky-100 text-sky-800',
                                    default => 'bg-surface-container text-on-surface-variant',
                                };
                            @endphp
                        ">{{ $log->eventLabel() }}</span>
                        <h3 class="font-headline text-xl font-extrabold mt-2">{{ $log->description }}</h3>
                    </div>
                    <div class="text-right text-sm text-on-surface-variant">
                        <p>{{ $log->created_at->format('l, d F Y') }}</p>
                        <p class="font-bold">{{ $log->created_at->format('H:i:s') }}</p>
                    </div>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-4">Event Information</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Event Key</dt>
                            <dd class="text-sm font-mono font-bold">{{ $log->event }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Operator</dt>
                            <dd class="text-sm font-bold">{{ $log->operator->company_name ?? 'Unknown' }} (#{{ $log->operator_id }})</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Record ID</dt>
                            <dd class="text-sm font-mono">#{{ $log->id }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Target Type</dt>
                            <dd class="text-sm font-mono">{{ $log->auditable_type ? class_basename($log->auditable_type) : '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Target ID</dt>
                            <dd class="text-sm font-mono">{{ $log->auditable_id ? '#' . $log->auditable_id : '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-4">Request Information</h4>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">IP Address</dt>
                            <dd class="text-sm font-mono font-bold">{{ $log->ip_address ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">User Agent</dt>
                            <dd class="text-sm text-right max-w-[250px] truncate" title="{{ $log->user_agent ?? '' }}">{{ $log->user_agent ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Created At</dt>
                            <dd class="text-sm">{{ $log->created_at->format('d M Y H:i:s') }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-on-surface-variant">Updated At</dt>
                            <dd class="text-sm">{{ $log->updated_at->format('d M Y H:i:s') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Old Values -->
            @if($log->old_values)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6 mb-6">
                <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-3">Previous Values</h4>
                <div class="json-view">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
            </div>
            @endif

            <!-- New Values -->
            @if($log->new_values)
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                <h4 class="font-headline font-bold text-sm text-on-surface-variant uppercase mb-3">New Values</h4>
                <div class="json-view">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</div>
            </div>
            @endif

            @if(!$log->old_values && !$log->new_values)
            <div class="bg-surface-container-low rounded-2xl border border-outline-variant/15 p-8 text-center">
                <span class="material-symbols-outlined text-4xl text-outline-variant block mb-2">info</span>
                <p class="text-on-surface-variant">No data payload recorded for this entry.</p>
            </div>
            @endif
        </div>
    </main>
</body>
</html>