<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zambia Transit | Operator Profile</title>

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

        .tab-panel { display: none; }
        .tab-panel.active { display: block; }
    </style>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "seat-available": "#00601f",
                        "on-tertiary-fixed-variant": "#920600",
                        "on-primary-fixed": "#002106",
                        "inverse-primary": "#86d989",
                        "surface-container-low": "#f3f3f6",
                        "primary-fixed": "#a1f6a3",
                        "surface-variant": "#e2e2e5",
                        "primary-container": "#197b30",
                        "on-primary": "#ffffff",
                        "outline-variant": "#bfcaba",
                        "outline": "#6f7a6c",
                        "tertiary-fixed-dim": "#ffb4a7",
                        "on-secondary-fixed": "#301400",
                        "surface-dim": "#d9dadd",
                        "seat-booked": "#a80800",
                        "on-background": "#1a1c1e",
                        "surface-container-high": "#e8e8eb",
                        "surface": "#f9f9fc",
                        "on-primary-fixed-variant": "#00531a",
                        "surface-container": "#edeef1",
                        "primary-fixed-dim": "#86d989",
                        "on-secondary-container": "#6f3600",
                        "surface-container-highest": "#e2e2e5",
                        "inverse-on-surface": "#f0f0f3",
                        "error-container": "#ffdad6",
                        "secondary-fixed-dim": "#ffb785",
                        "on-tertiary-container": "#ffb3a7",
                        "seat-selected": "#0077b6",
                        "surface-container-lowest": "#ffffff",
                        "background": "#f9f9fc",
                        "error": "#ba1a1a",
                        "tertiary-fixed": "#ffdad4",
                        "on-surface": "#1a1c1e",
                        "on-tertiary": "#ffffff",
                        "tertiary-container": "#a80800",
                        "secondary-container": "#fd9c53",
                        "primary": "#004614",
                        "on-secondary-fixed-variant": "#713700",
                        "surface-tint": "#176d2a",
                        "secondary-fixed": "#ffdcc6",
                        "tertiary": "#7c0400",
                        "on-tertiary-fixed": "#400100",
                        "on-surface-variant": "#40493e",
                        "secondary": "#954a00",
                        "on-error": "#ffffff",
                        "on-primary-container": "#85d988",
                        "on-secondary": "#ffffff",
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
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low dark:bg-surface-dim flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline-md text-headline-md font-extrabold text-primary dark:text-primary-fixed uppercase tracking-tighter">
                {{ $operator->company_name ?? 'N/A' }}
            </h1>
            <p class="font-body-sm text-body-sm text-on-surface-variant opacity-70">Operator Portal</p>
        </div>

        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="{{ route('operator.dashboard') }}">
                <span class="material-symbols-outlined" data-icon="dashboard">dashboard</span>
                <span class="font-body-md text-body-md">Dashboard</span>
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="{{ route('operator.trips.index') }}">
                <span class="material-symbols-outlined" data-icon="directions_bus">directions_bus</span>
                <span class="font-body-md text-body-md">Manage Trips</span>
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="#">
                <span class="material-symbols-outlined" data-icon="event_seat">event_seat</span>
                <span class="font-body-md text-body-md">Seat Maps</span>
            </a>

            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant dark:text-outline hover:text-primary dark:hover:text-primary-fixed hover:bg-surface-container-highest dark:hover:bg-surface-container transition-colors duration-200"
                href="#">
                <span class="material-symbols-outlined" data-icon="payments">payments</span>
                <span class="font-body-md text-body-md">Revenue</span>
            </a>

            <!-- Active: Profile -->
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary dark:text-primary-fixed font-bold border-r-4 border-primary dark:border-primary-fixed bg-surface-container-high dark:bg-surface-container transition-all duration-150"
                href="{{ route('operator.profile') }}">
                <span class="material-symbols-outlined" data-icon="account_circle">account_circle</span>
                <span class="font-body-md text-body-md">Profile</span>
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-outline-variant/20 space-y-1">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="#">
                <span class="material-symbols-outlined" data-icon="settings">settings</span>
                <span class="font-body-md text-body-md">Settings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="#">
                <span class="material-symbols-outlined" data-icon="help">help</span>
                <span class="font-body-md text-body-md">Support</span>
            </a>
            <form action="{{ route('operator.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-error transition-colors">
                    <span class="material-symbols-outlined" data-icon="logout">logout</span>
                    <span class="font-body-md text-body-md">Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">

        <!-- Header -->
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low dark:bg-surface-container border-b border-outline-variant/15 z-10">
            <div>
                <h2 class="font-headline-sm text-headline-sm text-primary">Profile &amp; Settings</h2>
            </div>
            <div class="flex items-center gap-4">
                <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors relative" data-icon="notifications">
                    notifications
                    <span class="absolute top-0 right-0 w-2 h-2 bg-tertiary rounded-full"></span>
                </button>
                <div class="h-8 w-8 rounded-full bg-primary-container flex items-center justify-center overflow-hidden border border-primary/20">
                    <span class="material-symbols-outlined text-on-primary-fixed text-lg">business</span>
                </div>
            </div>
        </header>

        <!-- Page Canvas -->
        <div class="p-8 max-w-5xl">

            <!-- Status messages -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-primary-fixed/30 border border-primary-container/30 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
                    <p class="text-on-surface text-body-sm font-body-sm">{{ session('status') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-error-container border border-error/20 flex items-start gap-3">
                    <span class="material-symbols-outlined text-error text-sm mt-0.5">error</span>
                    <div>
                        @foreach($errors->all() as $error)
                            <p class="text-error text-body-sm font-body-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Profile Header Card -->
            <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm mb-6 flex items-center gap-5">
                <div class="h-20 w-20 rounded-2xl bg-primary-container flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-on-primary-fixed text-4xl">business</span>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center gap-2">
                        <h3 class="font-headline-md text-headline-md text-on-surface">{{ $operator->company_name }}</h3>
                        @if($operator->is_verified)
                            <span class="bg-primary-fixed/40 text-on-primary-fixed-variant text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">verified</span> Verified
                            </span>
                        @else
                            <span class="bg-secondary-container/30 text-on-secondary-container text-[10px] font-bold uppercase tracking-wider px-2 py-1 rounded-full">
                                Pending Verification
                            </span>
                        @endif
                    </div>
                    <p class="text-on-surface-variant text-body-sm font-body-sm mt-1">{{ $operator->email }}</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1 mb-6 w-fit">
                <button onclick="setTab('details', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-bold bg-primary text-on-primary transition-all">
                    Account Details
                </button>
                <button onclick="setTab('password', this)" class="tab-btn px-4 py-2 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                    Change Password
                </button>
            </div>

            <!-- Account Details Tab -->
            <div id="tab-details" class="tab-panel active">
                <form action="{{ route('operator.profile.update') }}" method="POST" class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm">
                    @csrf
                    @method('PUT')

                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Business Information</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Company Name</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $operator->company_name) }}" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $operator->email) }}" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $operator->phone_number) }}" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">TPIN</label>
                            <input type="text" name="tpin" value="{{ old('tpin', $operator->tpin) }}"
                                placeholder="Optional"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Business Address</label>
                            <input type="text" name="address" value="{{ old('address', $operator->address) }}"
                                placeholder="Optional"
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-primary text-on-primary font-bold text-body-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">save</span>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Change Password Tab -->
            <div id="tab-password" class="tab-panel">
                <form action="{{ route('operator.profile.password') }}" method="POST" class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 shadow-sm max-w-lg">
                    @csrf
                    @method('PUT')

                    <h4 class="font-headline-sm text-headline-sm text-on-surface mb-5">Change Password</h4>

                    <div class="flex flex-col gap-5">
                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">New Password</label>
                            <input type="password" name="password" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>

                        <div>
                            <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Confirm New Password</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-body-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
                        </div>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="bg-primary text-on-primary font-bold text-body-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">lock_reset</span>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </main>

    <script>
        function setTab(tab, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');

            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary', 'font-bold');
                b.classList.add('text-on-surface-variant', 'font-medium');
            });
            btn.classList.add('bg-primary', 'text-on-primary', 'font-bold');
            btn.classList.remove('text-on-surface-variant', 'font-medium');
        }

        // If validation errors exist on password fields, auto-switch to that tab
        @if($errors->has('current_password') || $errors->has('password'))
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.tab-btn')[1].click();
            });
        @endif
    </script>
</body>
</html>
