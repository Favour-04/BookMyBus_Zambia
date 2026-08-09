<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zambia Transit | Fare Rules</title>

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

        /* Tab styling */
        .tab-btn.active {
            background: #004614;
            color: #ffffff;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Drawer */
        #rule-drawer {
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #rule-drawer.open { transform: translateX(0); }
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
                        "surface-variant": "#e2e2e5",
                        "primary-container": "#197b30",
                        "on-primary": "#ffffff",
                        "outline-variant": "#bfcaba",
                        "outline": "#6f7a6c",
                        "surface-dim": "#d9dadd",
                        "on-background": "#1a1c1e",
                        "surface-container-high": "#e8e8eb",
                        "surface": "#f9f9fc",
                        "surface-container": "#edeef1",
                        "surface-container-highest": "#e2e2e5",
                        "error-container": "#ffdad6",
                        "surface-container-lowest": "#ffffff",
                        "background": "#f9f9fc",
                        "error": "#ba1a1a",
                        "on-surface": "#1a1c1e",
                        "tertiary-container": "#a80800",
                        "secondary-container": "#fd9c53",
                        "primary": "#004614",
                        "on-surface-variant": "#40493e",
                        "secondary": "#954a00",
                        "on-error": "#ffffff",
                        "on-primary-container": "#85d988",
                        "on-error-container": "#93000a",
                        "surface-bright": "#f9f9fc",
                        "inverse-surface": "#2f3133"
                    },
                    "fontFamily": {
                        "headline-lg": ["Manrope"],
                        "headline-md": ["Manrope"],
                        "headline-sm": ["Manrope"],
                        "body-md": ["Inter"],
                        "body-sm": ["Inter"],
                        "label-caps": ["Inter"]
                    },
                    "fontSize": {
                        "headline-lg": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "800"}],
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

    <!-- Sidebar Navigation -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline-md text-headline-md font-extrabold text-primary uppercase tracking-tighter">
                {{ $operator->company_name ?? 'Power Tools Bus' }}
            </h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">Operator Portal</p>
        </div>

        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-body-md text-body-md">Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.trips.index') }}">
                <span class="material-symbols-outlined">directions_bus</span>
                <span class="font-body-md text-body-md">Manage Trips</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.trips.calendar') }}">
                <span class="material-symbols-outlined">calendar_month</span>
                <span class="font-body-md text-body-md">Trip Calendar</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.buses.index') }}">
                <span class="material-symbols-outlined">fleet</span>
                <span class="font-body-md text-body-md">Fleet</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.bookings.index') }}">
                <span class="material-symbols-outlined">book_online</span>
                <span class="font-body-md text-body-md">All Bookings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.revenue') }}">
                <span class="material-symbols-outlined">payments</span>
                <span class="font-body-md text-body-md">Revenue</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-high transition-all duration-150"
                href="{{ route('operator.fare-rules.index') }}">
                <span class="material-symbols-outlined">sell</span>
                <span class="font-body-md text-body-md">Fare Rules</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.promo-codes.index') }}">
                <span class="material-symbols-outlined">confirmation_number</span>
                <span class="font-body-md text-body-md">Promo Codes</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.audit-log.index') }}">
                <span class="material-symbols-outlined">history</span>
                <span class="font-body-md text-body-md">Audit Log</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-higher transition-colors duration-200"
                href="{{ route('operator.profile') }}">
                <span class="material-symbols-outlined">account_circle</span>
                <span class="font-body-md text-body-md">Profile</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">

        <!-- Header -->
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary">Fare Rules</h2>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-body-sm text-on-surface-variant">Configure pricing, fees & cancellation policies</span>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-8">

            @if(session('success'))
            <div class="mb-6 p-4 bg-primary/10 border border-primary/20 rounded-xl text-body-sm text-primary font-medium">
                {{ session('success') }}
            </div>
            @endif

            <!-- Tabs -->
            <div class="flex items-center gap-2 mb-6 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1">
                <button onclick="switchTab('cancellation', this)" class="tab-btn active px-4 py-2 rounded-lg text-body-sm font-bold transition-all">Cancellation Rules</button>
                <button onclick="switchTab('fees', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">Service Fees</button>
                <button onclick="switchTab('preview', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">Fare Preview</button>
            </div>

            <!-- Tab: Cancellation Rules -->
            <div id="tab-cancellation" class="tab-content active">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Cancellation & Refund Rules</h3>
                    <button onclick="openDrawer('cancellation')" class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Rule
                    </button>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Rule Name</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Hours Before Departure</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Refund %</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($cancellationRules as $rule)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4 font-bold text-body-sm text-on-surface">{{ $rule->name }}</td>
                                <td class="px-5 py-4 text-body-sm text-on-surface">{{ $rule->hours_before_departure }}h</td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-body-sm {{ $rule->refund_percentage > 0 ? 'text-primary' : 'text-error' }}">
                                        {{ $rule->refund_percentage }}%
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $rule->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <form action="{{ route('operator.fare-rules.cancellation.destroy', $rule->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this rule?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Delete">
                                            <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <span class="material-symbols-outlined text-4xl text-outline-variant block mb-2">policy</span>
                                    <p class="font-body-md text-body-md text-on-surface-variant">No cancellation rules configured.</p>
                                    <button onclick="openDrawer('cancellation')" class="mt-3 text-primary font-bold text-body-sm hover:underline">Add your first rule</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Service Fees -->
            <div id="tab-fees" class="tab-content">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Service & Booking Fees</h3>
                    <button onclick="openDrawer('fee')" class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                        <span class="material-symbols-outlined text-sm">add</span>
                        Add Fee
                    </button>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Fee Name</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Type</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Value</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($serviceFees as $fee)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4 font-bold text-body-sm text-on-surface">{{ $fee->name }}</td>
                                <td class="px-5 py-4">
                                    <span class="text-body-sm px-2.5 py-1 rounded-full bg-surface-container-high text-on-surface-variant">
                                        {{ $fee->fee_type === 'fixed' ? 'Fixed' : 'Percentage' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-bold text-body-sm text-on-surface">
                                    {{ $fee->fee_type === 'fixed' ? 'ZMW ' . number_format($fee->fee_value, 2) : $fee->fee_value . '%' }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $fee->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                        {{ $fee->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <form action="{{ route('operator.fare-rules.service-fees.destroy', $fee->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this fee?')">
                                        @csrf @method('DELETE')
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Delete">
                                            <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <span class="material-symbols-outlined text-4xl text-outline-variant block mb-2">receipt_long</span>
                                    <p class="font-body-md text-body-md text-on-surface-variant">No service fees configured.</p>
                                    <button onclick="openDrawer('fee')" class="mt-3 text-primary font-bold text-body-sm hover:underline">Add your first fee</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Fare Preview -->
            <div id="tab-preview" class="tab-content">
                <div class="mb-4">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Fare Calculator Preview</h3>
                    <p class="text-body-sm text-on-surface-variant mt-1">Test how your configured rules affect the final ticket price.</p>
                </div>

                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <form method="GET" action="{{ route('operator.fare-rules.index') }}" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Base Fare (ZMW)</label>
                                <input type="number" name="preview_fare" value="{{ request('preview_fare', 100) }}" step="0.01" min="0"
                                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                            </div>
                            <div>
                                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Promo Code (optional)</label>
                                <input type="text" name="preview_code" value="{{ request('preview_code') }}" placeholder="e.g. WELCOME10"
                                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                            </div>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                            Calculate Preview
                        </button>
                    </form>

                    @if($previewResult)
                    <div class="mt-6 p-5 bg-surface-container-low rounded-xl border border-outline-variant/15">
                        <h4 class="font-bold text-body-md text-on-surface mb-4">Fare Breakdown</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-body-sm">
                                <span class="text-on-surface-variant">Base Fare</span>
                                <span class="font-bold text-on-surface">ZMW {{ number_format($previewResult['base_fare'], 2) }}</span>
                            </div>

                            @if(count($previewResult['service_fees']) > 0)
                            <div class="border-t border-outline-variant/10 pt-2">
                                <span class="text-body-sm text-on-surface-variant font-medium">Service Fees</span>
                                @foreach($previewResult['service_fees'] as $fee)
                                <div class="flex justify-between text-body-sm ml-3 mt-1">
                                    <span class="text-on-surface-variant">{{ $fee['name'] }} ({{ $fee['type'] === 'fixed' ? 'ZMW' : '%' }}{{ $fee['value'] }})</span>
                                    <span class="text-on-surface">ZMW {{ number_format($fee['amount'], 2) }}</span>
                                </div>
                                @endforeach
                            </div>
                            <div class="flex justify-between text-body-sm">
                                <span class="text-on-surface-variant font-medium">Service Fee Total</span>
                                <span class="font-bold text-on-surface">ZMW {{ number_format($previewResult['service_fee_total'], 2) }}</span>
                            </div>
                            @endif

                            @if($previewResult['discount'] > 0)
                            <div class="flex justify-between text-body-sm text-primary">
                                <span>Discount {{ $previewResult['discount_msg'] ? '(' . $previewResult['discount_msg'] . ')' : '' }}</span>
                                <span class="font-bold">-ZMW {{ number_format($previewResult['discount'], 2) }}</span>
                            </div>
                            @elseif($previewResult['discount_msg'])
                            <div class="flex justify-between text-body-sm text-on-surface-variant">
                                <span>{{ $previewResult['discount_msg'] }}</span>
                                <span>ZMW 0.00</span>
                            </div>
                            @endif

                            <div class="border-t border-outline-variant/20 pt-3 flex justify-between text-body-md">
                                <span class="font-bold text-on-surface">Total</span>
                                <span class="font-bold text-primary text-headline-sm">ZMW {{ number_format($previewResult['total'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </main>

    <!-- Drawer Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-on-surface/30 z-30" onclick="closeDrawer()"></div>

    <!-- Drawer: Add Cancellation Rule -->
    <aside id="drawer-cancellation" class="fixed top-0 right-0 h-full w-[420px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl" style="transform: translateX(100%); transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">Add Cancellation Rule</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Define refund policy based on time before departure</p>
            </div>
            <button onclick="closeDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('operator.fare-rules.cancellation.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5">
            @csrf
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Rule Name</label>
                <input type="text" name="name" placeholder="e.g. Standard Refund" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Hours Before Departure</label>
                <input type="number" name="hours_before_departure" placeholder="e.g. 48" min="0" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                <p class="text-body-sm text-on-surface-variant mt-1">If cancelled at least this many hours before departure</p>
            </div>
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Refund Percentage</label>
                <div class="relative">
                    <input type="number" name="refund_percentage" placeholder="e.g. 100" min="0" max="100" step="0.01" required
                        class="w-full pr-8 rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-body-sm text-on-surface-variant">%</span>
                </div>
            </div>
            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawer()" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all">Create Rule</button>
            </div>
        </form>
    </aside>

    <!-- Drawer: Add Service Fee -->
    <aside id="drawer-fee" class="fixed top-0 right-0 h-full w-[420px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl" style="transform: translateX(100%); transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">Add Service Fee</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Extra charges added to the base fare</p>
            </div>
            <button onclick="closeDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="{{ route('operator.fare-rules.service-fees.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5">
            @csrf
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Fee Name</label>
                <input type="text" name="name" placeholder="e.g. Booking Fee" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Fee Type</label>
                <select name="fee_type" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    <option value="fixed">Fixed Amount (ZMW)</option>
                    <option value="percentage">Percentage (%)</option>
                </select>
            </div>
            <div>
                <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Fee Value</label>
                <input type="number" name="fee_value" placeholder="e.g. 5.00" min="0" step="0.01" required
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
            </div>
            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawer()" class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all">Create Fee</button>
            </div>
        </form>
    </aside>

    <script>
        // Tab switching
        function switchTab(tab, btn) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active', 'bg-primary', 'text-on-primary');
                el.classList.add('text-on-surface-variant');
            });
            document.getElementById('tab-' + tab).classList.add('active');
            btn.classList.add('active', 'bg-primary', 'text-on-primary');
            btn.classList.remove('text-on-surface-variant');
        }

        // Drawer management
        function openDrawer(type) {
            document.getElementById('drawer-' + type).classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }
        function closeDrawer() {
            document.querySelectorAll('[id^="drawer-"]').forEach(el => el.classList.remove('open'));
            document.getElementById('drawer-backdrop').classList.remove('open');
        }

        // Micro-interactions
        document.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('mousedown', () => el.classList.add('scale-[0.98]'));
            el.addEventListener('mouseup',   () => el.classList.remove('scale-[0.98]'));
            el.addEventListener('mouseleave',() => el.classList.remove('scale-[0.98]'));
        });
    </script>

</body>
</html>