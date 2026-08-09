<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zambia Transit | Promo Codes</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Manrope:wght@700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap"
        rel="stylesheet" />

    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }

        #promo-drawer {
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #promo-drawer.open { transform: translateX(0); }
        #drawer-backdrop {
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #drawer-backdrop.open { opacity: 1; pointer-events: auto; }
    </style>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-container-low": "#f3f3f6",
                        "primary": "#004614",
                        "on-primary": "#ffffff",
                        "outline-variant": "#bfcaba",
                        "outline": "#6f7a6c",
                        "surface": "#f9f9fc",
                        "surface-container-high": "#e8e8eb",
                        "surface-container": "#edeef1",
                        "surface-container-highest": "#e2e2e5",
                        "error-container": "#ffdad6",
                        "surface-container-lowest": "#ffffff",
                        "background": "#f9f9fc",
                        "error": "#ba1a1a",
                        "on-surface": "#1a1c1e",
                        "on-surface-variant": "#40493e",
                    },
                    "fontFamily": {
                        "headline-md": ["Manrope"],
                        "headline-sm": ["Manrope"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-caps": ["Inter"]
                    },
                    "fontSize": {
                        "headline-md": ["20px", {"lineHeight": "28px", "fontWeight": "700"}],
                        "headline-sm": ["14px", {"lineHeight": "20px", "fontWeight": "700"}],
                        "body-md": ["14px", {"lineHeight": "20px", "fontWeight": "500"}],
                        "body-sm": ["12px", {"lineHeight": "16px", "fontWeight": "400"}],
                        "label-caps": ["10px", {"lineHeight": "12px", "letterSpacing": "0.1em", "fontWeight": "700"}]
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-background text-on-surface">

    <!-- Sidebar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline-md text-headline-md font-extrabold text-primary uppercase tracking-tighter">
                {{ $operator->company_name ?? 'Power Tools Bus' }}
            </h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">Operator Portal</p>
        </div>

        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-body-md text-body-md">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.trips.index') }}">
                <span class="material-symbols-outlined">directions_bus</span>
                <span class="font-body-md text-body-md">Manage Trips</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.trips.calendar') }}">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="font-body-md text-body-md">Trip Calendar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.buses.index') }}">
                <span class="material-symbols-outlined">fleet</span>
                <span class="font-body-md text-body-md">Fleet</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.bookings.index') }}">
                <span class="material-symbols-outlined">book_online</span>
                <span class="font-body-md text-body-md">All Bookings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.revenue') }}">
                <span class="material-symbols-outlined">payments</span>
                <span class="font-body-md text-body-md">Revenue</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.fare-rules.index') }}">
                <span class="material-symbols-outlined">sell</span>
                <span class="font-body-md text-body-md">Fare Rules</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-high transition-all duration-150" href="{{ route('operator.promo-codes.index') }}">
                <span class="material-symbols-outlined">confirmation_number</span>
                <span class="font-body-md text-body-md">Promo Codes</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.audit-log.index') }}">
                <span class="material-symbols-outlined">history</span>
                <span class="font-body-md text-body-md">Audit Log</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200" href="{{ route('operator.profile') }}">
                <span class="material-symbols-outlined">account_circle</span>
                <span class="font-body-md text-body-md">Profile</span>
            </a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary">Promo Codes</h2>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-body-sm text-on-surface-variant">Create and manage discount codes</span>
            </div>
        </header>

        <div class="p-8">

            @if(session('success'))
            <div class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-xl text-body-sm text-primary font-medium">
                {{ session('success') }}
            </div>
            @endif

            <!-- Header + Add Button -->
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-body-sm text-on-surface-variant">{{ count($promoCodes) }} promo code(s) configured</p>
                </div>
                <button onclick="openDrawer()" class="flex items-center gap-2 px-5 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                    <span class="material-symbols-outlined text-sm">add</span>
                    New Promo Code
                </button>
            </div>

            <!-- Promo Codes Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-surface-container-low border-b border-outline-variant/15">
                        <tr>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Code</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Discount</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Usage</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Valid Period</th>
                            <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                            <th class="px-5 py-4"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline-variant/10">
                        @forelse($promoCodes as $promo)
                        @php
                            $now = now();
                            $isExpired = $now->gt($promo->valid_until);
                            $isValid = $promo->is_active && !$isExpired && (!$promo->usage_limit || $promo->used_count < $promo->usage_limit);
                        @endphp
                        <tr class="hover:bg-surface-container-low/60 transition-colors">
                            <td class="px-5 py-4">
                                <span class="font-bold text-body-sm text-on-surface tracking-wide uppercase">{{ $promo->code }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-body-sm text-primary">
                                    @if($promo->discount_type === 'fixed')
                                        ZMW {{ number_format($promo->discount_value, 2) }}
                                    @else
                                        {{ $promo->discount_value }}%
                                        @if($promo->max_discount_amount)
                                            <span class="text-body-sm text-on-surface-variant">(max ZMW {{ number_format($promo->max_discount_amount, 2) }})</span>
                                        @endif
                                    @endif
                                </span>
                                @if($promo->min_booking_amount > 0)
                                <span class="block text-body-sm text-on-surface-variant">Min: ZMW {{ number_format($promo->min_booking_amount, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-body-sm text-on-surface">
                                {{ $promo->used_count }} / {{ $promo->usage_limit ?? '∞' }}
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-body-sm text-on-surface">{{ $promo->valid_from->format('d M Y') }}</span>
                                <span class="text-body-sm text-on-surface-variant"> → </span>
                                <span class="text-body-sm text-on-surface">{{ $promo->valid_until->format('d M Y') }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $isValid ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                    {{ $isValid ? 'Active' : ($isExpired ? 'Expired' : 'Inactive') }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <form action="{{ route('operator.promo-codes.destroy', $promo->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete promo code {{ $promo->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Delete">
                                        <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <span class="material-symbols-outlined text-4xl text-outline-variant block mb-2">confirmation_number</span>
                                <p class="font-body-md text-body-md text-on-surface-variant">No promo codes yet.</p>
                                <button onclick="openDrawer()" class="mt-3 text-primary font-bold text-body-sm hover:underline">Create your first promo code</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-on-surface/30 z-30" onclick="closeDrawer()"></div>

    <!-- Drawer: New Promo Code -->
    <aside id="promo-drawer" class="fixed top-0 right-0 h-full w-[440px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">New Promo Code</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Create a discount code for your passengers</p>
            </div>
            <button onclick="closeDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('operator.promo-codes.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5">
            @csrf

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Promo Code</label>
                <input type="text" name="code" placeholder="e.g. WELCOME10" maxlength="20" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3 uppercase">
                <p class="text-body-sm text-on-surface-variant mt-1">Will be auto-converted to uppercase</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Discount Type</label>
                    <select name="discount_type" required class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed (ZMW)</option>
                    </select>
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Discount Value</label>
                    <input type="number" name="discount_value" placeholder="e.g. 10" min="0" step="0.01" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Min. Booking Amount (ZMW)</label>
                    <input type="number" name="min_booking_amount" placeholder="0" min="0" step="0.01"
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Max Discount (ZMW) <span class="text-on-surface-variant/50">(optional)</span></label>
                    <input type="number" name="max_discount_amount" placeholder="For % discounts" min="0" step="0.01"
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>

            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Usage Limit <span class="text-on-surface-variant/50">(optional)</span></label>
                <input type="number" name="usage_limit" placeholder="Leave empty for unlimited" min="1"
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Valid From</label>
                    <input type="datetime-local" name="valid_from" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
                <div>
                    <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Valid Until</label>
                    <input type="datetime-local" name="valid_until" required
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawer()" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all">Create Promo Code</button>
            </div>
        </form>
    </aside>

    <script>
        function openDrawer() {
            document.getElementById('promo-drawer').classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }
        function closeDrawer() {
            document.getElementById('promo-drawer').classList.remove('open');
            document.getElementById('drawer-backdrop').classList.remove('open');
        }
        document.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('mousedown', () => el.classList.add('scale-[0.98]'));
            el.addEventListener('mouseup',   () => el.classList.remove('scale-[0.98]'));
            el.addEventListener('mouseleave',() => el.classList.remove('scale-[0.98]'));
        });
    </script>

</body>
</html>