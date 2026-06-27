<!-- TODO: This view should extend layouts.app when a layout is available -->
<!-- TODO: Connect to payment controller for MTN/Airtel Money integration -->

<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Secure Payment | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <script id="tailwind-config">
        tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            "colors": {
                    "on-secondary": "#ffffff",
                    "on-secondary-container": "#632f00",
                    "on-secondary-fixed": "#301400",
                    "inverse-surface": "#2f3123",
                    "on-primary-container": "#b1ffb1",
                    "secondary-container": "#ff8921",
                    "primary-fixed-dim": "#7edb83",
                    "inverse-primary": "#7edb83",
                    "background": "#f9f9fc",
                    "inverse-on-surface": "#f0f0f3",
                    "tertiary-container": "#d1200f",
                    "secondary-fixed": "#ffdcc6",
                    "surface-container-lowest": "#ffffff",
                    "outline-variant": "#bfcaba",
                    "on-tertiary": "#ffffff",
                    "surface": "#f9f9fc",
                    "on-primary": "#ffffff",
                    "primary-fixed": "#99f89d",
                    "surface-variant": "#e2e2e5",
                    "secondary-fixed-dim": "#ffb784",
                    "on-primary-fixed": "#002106",
                    "surface-bright": "#f9f9fc",
                    "on-error-container": "#93000a",
                    "surface-dim": "#dadadc",
                    "outline": "#6f7a6c",
                    "on-secondary-fixed-variant": "#713700",
                    "primary": "#00601f",
                    "on-primary-fixed-variant": "#00531a",
                    "surface-container-low": "#f3f3f6",
                    "on-surface-variant": "#3f493e",
                    "surface-container-high": "#e8e8ea",
                    "on-surface": "#1a1c1e",
                    "primary-container": "#197b30",
                    "error-container": "#ffdad6",
                    "tertiary-fixed": "#ffdad4",
                    "on-tertiary-fixed": "#400100",
                    "tertiary-fixed-dim": "#ffb4a7",
                    "surface-container-highest": "#e2e2e5",
                    "on-tertiary-container": "#ffe7e3",
                    "on-tertiary-fixed-variant": "#920600",
                    "surface-tint": "#006e25",
                    "error": "#ba1a1a",
                    "surface-container": "#eeeef0",
                    "secondary": "#954a00",
                    "tertiary": "#a80800",
                    "on-error": "#ffffff",
                    "on-background": "#1a1c1e"
            },
            "borderRadius": {
                    "DEFAULT": "0.125rem",
                    "lg": "0.25rem",
                    "xl": "0.5rem",
                    "full": "0.75rem"
            },
            "fontFamily": {
                    "headline": ["Manrope"],
                    "body": ["Inter"],
                    "label": ["Inter"]
            }
        },
        },
      }
    </script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

        .bg-glass {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>

<body class="bg-surface font-body text-on-surface selection:bg-primary-container selection:text-on-primary-container">
    <!-- Top Navigation (Shell Implementation) -->
    <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none">
        <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
            <div class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline">
                <a href="/">BookMyBus Zambia</a>
            </div>
            <div class="hidden md:flex items-center space-x-8 font-headline tracking-tight font-bold text-sm">
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="#">Find Trips</a>
                <a class="text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1" href="#">My Bookings</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="#">Operator Portal</a>
                <a class="text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
                    href="#">Support</a>
            </div>
            <div class="flex items-center space-x-4">
                <span
                    class="material-symbols-outlined text-green-800 dark:text-green-400 cursor-pointer">account_circle</span>
            </div>
        </div>
    </nav>
    <main class="pt-24 pb-20 px-6 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Payment Methods -->
            <div class="lg:col-span-7 space-y-8">
                <header>
                    <h1 class="text-4xl font-black font-headline tracking-tight text-on-surface mb-2">Secure Checkout
                    </h1>
                    <p class="text-on-surface-variant font-medium">Finalize your booking to {{ $destination ?? 'Lusaka'
                        }} securely.</p>
                </header>
                <form method="POST" action="{{ route('payment.process', $booking->id) }}">
                    @csrf
                    <section class="space-y-4">
                        <h2 class="text-sm font-bold uppercase tracking-widest text-secondary font-label">Mobile Money
                            Wallets</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Airtel Money Option -->
                            <div
                                class="group relative bg-surface-container-lowest p-6 rounded-xl shadow-sm cursor-pointer hover:shadow-md transition-all border-2 border-transparent hover:border-primary/20">
                                <div class="flex justify-between items-start mb-6">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-red-600 flex items-center justify-center text-white font-bold text-xl">
                                        A</div>
                                    <div
                                        class="w-5 h-5 rounded-full border-2 border-outline flex items-center justify-center">
                                        <div
                                            class="w-2.5 h-2.5 bg-primary rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                                        </div>
                                    </div>
                                </div>
                                <h3 class="font-headline font-bold text-lg">Airtel Money</h3>
                                <p class="text-xs text-on-surface-variant mb-4">Pay instantly using your Airtel number
                                </p>
                                <span class="text-xs font-bold text-primary flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">verified_user</span> Secure Network
                                </span>
                            </div>
                            <!-- MTN Money Option -->
                            <div
                                class="group relative bg-surface-container-lowest p-6 rounded-xl shadow-sm cursor-pointer hover:shadow-md transition-all border-2 border-primary">
                                <div class="flex justify-between items-start mb-6">
                                    <div
                                        class="w-12 h-12 rounded-lg bg-yellow-400 flex items-center justify-center text-zinc-900 font-bold text-xl">
                                        M</div>
                                    <div
                                        class="w-5 h-5 rounded-full border-2 border-primary flex items-center justify-center">
                                        <div class="w-2.5 h-2.5 bg-primary rounded-full"></div>
                                    </div>
                                </div>
                                <h3 class="font-headline font-bold text-lg">MTN MoMo</h3>
                                <p class="text-xs text-on-surface-variant mb-4">Confirm on your phone via USSD prompt
                                </p>
                                <span class="text-xs font-bold text-primary flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">verified_user</span> Recommended
                                </span>
                            </div>
                        </div>
                    </section>
                    <section class="bg-surface-container-low p-8 rounded-xl space-y-6">
                        <div class="flex items-center gap-4">
                            <span class="material-symbols-outlined text-primary"
                                style="font-variation-settings: 'FILL' 1;">phone_iphone</span> {{--phone icon--}}
                            <div class="flex-1">
                                <label
                                    class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">MTN
                                    Phone Number</label>
                                <input
                                    class="w-full bg-surface-container-lowest border-none rounded-lg p-4 font-headline font-bold text-lg focus:ring-2 focus:ring-primary/30 transition-shadow outline-none"
                                    placeholder="096 XXX XXXX" type="text" />
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full py-4 rounded-xl bg-gradient-to-br from-primary to-primary-container text-white font-headline font-bold text-lg shadow-lg hover:shadow-primary/20 transition-all active:scale-[0.98]">
                            Pay with MTN/Airtel Money • ZMW{{ number_format($total_fare, 2) }}
                        </button>
                        <p class="text-center text-xs text-on-surface-variant">By clicking authorize, you will receive a
                            prompt on your phone to enter your PIN.</p>
                    </section>
                </form>
            </div>
            <!-- Right Column: Digital Ticket & Summary -->
            <div class="lg:col-span-5">
                <div class="sticky top-24 space-y-6">
                    <!-- Ticket Design -->
                    <div class="relative">
                        <!-- Top Notch -->
                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-surface rounded-b-full z-10">
                        </div>
                        <div class="bg-surface-container-lowest rounded-2xl shadow-xl overflow-hidden relative">
                            <!-- Visual Header -->
                            <div class="h-32 relative">
                                <img class="w-full h-full object-cover"
                                    data-alt="Modern coach bus driving through a scenic Zambian highway landscape at golden hour with soft sunlight"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBtACWHf96GQ2Q6lm8qydU0xrhEVka89gUOGcMoH974Us9bOlDAAtmr10nk6iIVJ98jsWJWTii8Y8xLjDrFPGlxmNfkI3FGH_6VBr36Xy3f7LlCdbSg2-0_ZP_SiM83Ez88uCg3arvxEVQaOe61WNm9VIt3cvWqw1dkKoQHxHtajf-ws6BRAPpzQED8jlxcNOuEO_ywfSvtmIzSz9cKzbFuJfqHi-ADDDJ6v4amXeggpOH57W9NqViHzH1pa8mfsF4oaXs1MVj_wgc4" />
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-surface-container-lowest to-transparent">
                                </div>
                                <div class="absolute bottom-4 left-6">
                                    <span
                                        class="px-3 py-1 bg-primary text-white text-[10px] font-bold uppercase tracking-widest rounded-full">Pending
                                        Payment</span>
                                </div>
                            </div>
                            <div class="p-8 space-y-8">
                                <div class="flex justify-between items-center">
                                    <div class="space-y-1">
                                        <p
                                            class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">
                                            Passenger</p>
                                        <p class="font-headline font-extrabold text-xl">{{ $passenger_name ?? 'John
                                            Mulenga' }}</p>
                                    </div>
                                    <div class="text-right space-y-1">
                                        <p
                                            class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest">
                                            Seat</p>
                                        <p class="font-headline font-extrabold text-xl text-secondary">{{ $seat_number
                                            }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6 justify-between relative">
                                    <div class="flex-1">
                                        <p class="font-black text-2xl font-headline">{{ $origin_code ?? 'LUN' }}</p>
                                        <p class="text-xs font-medium text-on-surface-variant">{{ $origin }}</p>
                                    </div>
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="material-symbols-outlined text-primary">directions_bus</span>
                                        <div class="h-[2px] w-12 bg-surface-container-highest"></div>
                                    </div>
                                    <div class="flex-1 text-right">
                                        <p class="font-black text-2xl font-headline">{{ $destination_code ?? 'LUS' }}
                                        </p>
                                        <p class="text-xs font-medium text-on-surface-variant">{{ $destination }}</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-6 pt-6 border-t border-dashed border-outline-variant">
                                    <div>
                                        <p
                                            class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">
                                            Departure</p>
                                        <p class="font-bold text-sm">{{ $departure_date }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $departure_time }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p
                                            class="text-[10px] font-bold uppercase text-on-surface-variant tracking-widest mb-1">
                                            Booking ID</p>
                                        <p class="font-bold text-sm">{{ $booking_id }}</p>
                                        <p class="text-xs text-on-surface-variant">{{ $class_type ?? 'Premium Class' }}
                                        </p>
                                    </div>
                                </div>
                                <!-- QR Code Area -->
                                <div class="flex flex-col items-center pt-8">
                                    <div class="p-4 bg-surface-container-low rounded-xl">
                                        <div
                                            class="w-32 h-32 bg-white flex items-center justify-center border-4 border-white">
                                            <img alt="Ticket QR Code" class="w-full h-full"
                                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDYaczauok-Rug7xXFFprXmOzjS1g3lCRtoQCyjLqpmsDz3uLw7lB-lz_6OVUTIjPjX1ND-62V3FiwckGxzEoDsJQ05ZwHoVBtsSmjPx2FUItgmYeY7w6ExZ0iRujMVJnsGp1ahF_Is_WS-SUbuyBSVWB0kz3UE8yL8RcOQdnVoq3xZ6u2B74cb1fEVnl-yfm_0fMWZXZpNrWvKcNuXL6Xe02gnfxkX29S3g1T2Cq9gyjoSHdrleV2CAz2nqlRNICD5mN6phumQ8qZA" />
                                        </div>
                                    </div>
                                    <p
                                        class="mt-4 text-[10px] font-bold text-on-surface-variant uppercase tracking-[0.2em]">
                                        Scan at boarding</p>
                                </div>
                            </div>
                            <!-- Bottom Notch -->
                            <div
                                class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-12 h-6 bg-surface rounded-t-full z-10">
                            </div>
                        </div>
                    </div>
                    <!-- Trust Indicators -->
                    <div class="bg-surface-container-low p-6 rounded-xl flex items-center gap-4">
                        <span class="material-symbols-outlined text-primary text-3xl">shield_lock</span>
                        <div>
                            <p class="font-bold text-sm">Bank-Grade Security</p>
                            <p class="text-xs text-on-surface-variant">Your transaction is encrypted and secured by
                                Zambia's leading payment gateways.</p>
                        </div>
                    </div>
                </div>
            </div>
    </main>
    <!-- Footer (Shell Implementation) -->
    <footer class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="space-y-4">
                <div class="font-manrope font-bold text-zinc-900 dark:text-zinc-100">BookMyBus Zambia</div>
                <div class="font-inter text-xs text-zinc-500 dark:text-zinc-400">© 2024 BookMyBus Zambia. Premium Travel
                    Excellence.</div>
            </div>
            <div class="flex flex-wrap gap-6 md:justify-end">
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="#">Privacy Policy</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="#">Terms of Service</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="#">Carrier Partners</a>
                <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                    href="#">Contact Us</a>
            </div>
        </div>
    </footer>
</body>

</html>