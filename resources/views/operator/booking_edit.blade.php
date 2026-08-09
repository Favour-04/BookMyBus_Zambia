<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale() ?? 'en') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Booking #{{ $booking->reference_id }} | BookMyBus Zambia</title>
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

    <!-- Sidebar -->
    <aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low flex flex-col py-6 px-4 z-20">
        <div class="mb-10 px-2">
            <h1 class="font-headline text-xl font-extrabold text-primary uppercase tracking-tighter">{{ $operator->company_name ?? 'Operator' }}</h1>
            <p class="text-xs text-on-surface-variant opacity-70">Operator Portal</p>
        </div>
        <nav class="flex-grow space-y-1">
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.trips.index') }}">
                <span class="material-symbols-outlined">directions_bus</span><span>Manage Trips</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.bookings.index') }}">
                <span class="material-symbols-outlined">book_online</span><span>All Bookings</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.revenue') }}">
                <span class="material-symbols-outlined">payments</span><span>Revenue</span>
            </a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.audit-log.index') }}">
                <span class="material-symbols-outlined">history</span><span>Audit Log</span>
            </a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="{{ route('operator.profile') }}">
                <span class="material-symbols-outlined">settings</span><span>Settings</span>
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Edit Booking</h2>
            <div class="flex items-center gap-4">
                <div class="h-8 w-8 rounded-full bg-primary/20 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary text-sm">person</span>
                </div>
            </div>
        </header>

        <div class="p-8">

            <!-- Session Messages -->
            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-primary/5 border border-primary/10 rounded-xl text-primary font-bold text-sm">
                <span class="material-symbols-outlined">check_circle</span>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-error-container/20 border border-error-container/30 rounded-xl text-error font-bold text-sm">
                <span class="material-symbols-outlined">error</span>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Back link -->
            <a href="{{ route('operator.bookings.show', $booking->id) }}" class="inline-flex items-center gap-1 text-sm font-bold text-primary hover:underline mb-6">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Back to Booking Details
            </a>

            <div class="max-w-2xl">

                <!-- Edit Form Card -->
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-6">
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-dashed border-outline-variant/20">
                        <div>
                            <h3 class="font-headline font-bold text-lg">Booking Information</h3>
                            <p class="text-sm text-on-surface-variant mt-1">Reference: <span class="font-mono font-bold">{{ $booking->reference_id }}</span></p>
                        </div>
                        <span class="text-[10px] font-bold uppercase px-3 py-1.5 rounded-full
                            {{ $booking->status === 'confirmed' ? 'bg-primary/10 text-primary' : ($booking->status === 'pending' ? 'bg-tertiary/10 text-tertiary' : 'bg-error-container/20 text-error') }}">
                            {{ $booking->status }}
                        </span>
                    </div>

                    <form method="POST" action="{{ route('operator.bookings.update', $booking->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Trip Summary (Read-only) -->
                        <div class="bg-surface-container-low rounded-xl p-4 mb-6">
                            <div class="flex items-center gap-2 text-sm font-medium">
                                <span class="material-symbols-outlined text-primary text-sm">directions_bus</span>
                                {{ $booking->route->origin }} → {{ $booking->route->destination }}
                                <span class="text-on-surface-variant mx-2">|</span>
                                <span class="text-on-surface-variant">{{ $booking->route->travel_date instanceof \Carbon\Carbon ? $booking->route->travel_date->format('d M Y') : \Carbon\Carbon::parse($booking->route->travel_date)->format('d M Y') }}</span>
                                <span class="text-on-surface-variant mx-2">|</span>
                                <span class="text-on-surface-variant">{{ $booking->route->departure_time }}</span>
                            </div>
                        </div>

                        <!-- Passenger Name -->
                        <div class="mb-5">
                            <label for="passenger_name" class="block text-xs font-bold uppercase text-on-surface-variant tracking-wider mb-2">Passenger Name</label>
                            <input type="text" id="passenger_name" name="passenger_name"
                                   value="{{ old('passenger_name', $booking->passenger_name) }}"
                                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('passenger_name') border-error @enderror"
                                   required>
                            @error('passenger_name')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-5">
                            <label for="phone_number" class="block text-xs font-bold uppercase text-on-surface-variant tracking-wider mb-2">Phone Number</label>
                            <input type="text" id="phone_number" name="phone_number"
                                   value="{{ old('phone_number', $booking->phone_number) }}"
                                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('phone_number') border-error @enderror"
                                   required>
                            @error('phone_number')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ID Number -->
                        <div class="mb-5">
                            <label for="id_number" class="block text-xs font-bold uppercase text-on-surface-variant tracking-wider mb-2">ID Number <span class="text-on-surface-variant/50">(optional)</span></label>
                            <input type="text" id="id_number" name="id_number"
                                   value="{{ old('id_number', $booking->id_number) }}"
                                   class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('id_number') border-error @enderror">
                            @error('id_number')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Seat Number -->
                        <div class="mb-5">
                            <label for="seat_number" class="block text-xs font-bold uppercase text-on-surface-variant tracking-wider mb-2">Seat Number</label>
                            <select id="seat_number" name="seat_number"
                                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('seat_number') border-error @enderror"
                                    required>
                                @foreach($availableSeats as $seat)
                                    <option value="{{ $seat }}" {{ old('seat_number', (int) $booking->seat_number) === $seat ? 'selected' : '' }}>
                                        Seat #{{ $seat }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-on-surface-variant mt-1.5">
                                <span class="material-symbols-outlined text-xs align-text-bottom">info</span>
                                Showing available seats for this trip. The current seat is included in the list.
                            </p>
                            @error('seat_number')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-6">
                            <label for="amount" class="block text-xs font-bold uppercase text-on-surface-variant tracking-wider mb-2">Amount (ZMW)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm font-bold">ZMW</span>
                                <input type="number" step="0.01" min="0" id="amount" name="amount"
                                       value="{{ old('amount', $booking->amount) }}"
                                       class="w-full pl-16 pr-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all @error('amount') border-error @enderror"
                                       required>
                            </div>
                            @error('amount')
                                <p class="text-xs text-error mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant/20">
                            <a href="{{ route('operator.bookings.show', $booking->id) }}"
                               class="px-5 py-2.5 rounded-xl text-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 rounded-xl bg-primary text-on-primary text-sm font-bold hover:bg-primary/90 transition-colors flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">save</span>
                                Update Booking
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change History Note -->
                <div class="mt-4 bg-surface-container-low rounded-xl p-4 flex items-start gap-3">
                    <span class="material-symbols-outlined text-on-surface-variant text-sm mt-0.5">history</span>
                    <p class="text-xs text-on-surface-variant">
                        <strong>Note:</strong> Changes to the booking will be recorded. The booking's reference ID and
                        creation timestamp will remain unchanged. If you change the seat number, ensure the passenger is informed.
                    </p>
                </div>

            </div>
        </div>
    </main>

</body>
</html>