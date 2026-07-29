@extends('layouts.landing')

@section('title', 'Search Results - BookMyBus Zambia')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
<style>
    /* Only minimal custom styles that can't be done with Tailwind */
    .filter-time-btn.active {
        @apply bg-primary text-white border-primary;
    }

    .filter-time-btn.active .material-symbols-outlined {
        @apply text-white;
    }

    .checkbox.checked {
        @apply bg-primary border-primary;
    }

    .trip-card {
        transition: all 0.3s ease;
    }

    .trip-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }

    .skeleton-card {
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .loading-spinner {
        animation: spin 0.8s linear infinite;
    }
</style>
@endpush

@section('content')
<!-- Toast Notification -->
<div id="toastMsg" class="fixed bottom-8 left-1/2 -translate-x-1/2 translate-y-5 bg-gray-800 text-white px-5 py-2.5 rounded-full text-sm font-semibold z-[1000] opacity-0 transition-opacity duration-200 pointer-events-none whitespace-nowrap shadow-lg">
</div>

<main class="pt-24 pb-20 px-4 md:px-6">
    <div class="max-w-7xl mx-auto">
        <!-- Search Header -->
        <header class="mb-10">
            <div class="flex flex-wrap items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 text-xs uppercase font-bold text-primary tracking-wider mb-1">
                        <span>One Way</span>
                        <span class="material-symbols-outlined text-[0.25rem]">circle</span>
                        <span id="passengerCount">
                            {{ request('passengers', 1) }} Passenger{{ request('passengers', 1) > 1 ? 's' : '' }}
                        </span>
                    </div>
                    <h1 class="text-3xl md:text-5xl font-headline font-extrabold tracking-tight">
                        <span id="originCity">{{ request('origin', 'Lusaka') }}</span>
                        <span class="text-zinc-300">→</span>
                        <span id="destinationCity">{{ request('destination', 'Kitwe') }}</span>
                    </h1>
                    <p class="text-zinc-500 font-medium mt-1" id="searchDate">
                        {{ request('travel_date') ? \Carbon\Carbon::parse(request('travel_date'))->format('l, d F Y') : now()->format('l, d F Y') }}
                        • <span id="tripCount">{{ $trips->count() }}</span> departures available
                    </p>
                </div>
                <button onclick="goBack()" class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-100 hover:bg-zinc-200 rounded-lg font-bold text-sm transition-all">
                    <span class="material-symbols-outlined text-sm">edit</span>
                    <span>Modify Search</span>
                </button>
            </div>
        </header>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="lg:w-72 flex-shrink-0">
                <div class="bg-zinc-50 rounded-2xl p-6 sticky top-24 border border-zinc-100">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-headline font-extrabold text-lg">Filters</h3>
                        <button id="clearFiltersBtn" class="text-xs font-bold text-primary hover:underline">Clear All</button>
                    </div>

                    <!-- Time Filters -->
                    <div class="mb-6">
                        <label class="text-xs uppercase font-bold text-zinc-400 tracking-wider block mb-3">Time of Day</label>
                        <div class="grid grid-cols-2 gap-2">
                            <button data-time="dawn" class="filter-time-btn flex flex-col items-center justify-center p-3 rounded-lg bg-white border border-zinc-200 hover:border-primary transition-all">
                                <span class="material-symbols-outlined text-sm">wb_twilight</span>
                                <span class="text-xs font-bold">Dawn</span>
                            </button>
                            <button data-time="morning" class="filter-time-btn flex flex-col items-center justify-center p-3 rounded-lg bg-white border border-zinc-200 hover:border-primary transition-all active">
                                <span class="material-symbols-outlined text-sm">light_mode</span>
                                <span class="text-xs font-bold">Morning</span>
                            </button>
                            <button data-time="afternoon" class="filter-time-btn flex flex-col items-center justify-center p-3 rounded-lg bg-white border border-zinc-200 hover:border-primary transition-all">
                                <span class="material-symbols-outlined text-sm">wb_sunny</span>
                                <span class="text-xs font-bold">Afternoon</span>
                            </button>
                            <button data-time="night" class="filter-time-btn flex flex-col items-center justify-center p-3 rounded-lg bg-white border border-zinc-200 hover:border-primary transition-all">
                                <span class="material-symbols-outlined text-sm">bedtime</span>
                                <span class="text-xs font-bold">Night</span>
                            </button>
                        </div>
                    </div>

                    <!-- Price Filter -->
                    <div class="mb-6">
                        <label class="text-xs uppercase font-bold text-zinc-400 tracking-wider block mb-3">Price Range (ZMW)</label>
                        <input type="range" class="w-full accent-primary" min="0" max="{{ $maxPrice }}" value="{{ $maxPrice }}" id="priceSlider">
                        <div class="flex justify-between text-sm text-zinc-500 mt-2">
                            <span>K0</span>
                            <span id="priceValue">K{{ $maxPrice }}</span>
                            <span>K{{ $maxPrice }}</span>
                        </div>
                    </div>

                    <!-- Operators Filter -->
                    @if($operators->count() > 0)
                    <div class="mb-6">
                        <label class="text-xs uppercase font-bold text-zinc-400 tracking-wider block mb-3">Preferred Operator</label>
                        <div class="space-y-3">
                            @foreach($operators as $operator)
                            <label class="operator-item flex items-center gap-3 cursor-pointer" data-op="{{ $operator->id }}">
                                <div class="checkbox w-5 h-5 rounded border-2 border-primary flex items-center justify-center checked">
                                    <span class="material-symbols-outlined text-white text-xs">check</span>
                                </div>
                                <span class="text-sm font-medium">{{ $operator->name }}</span>
                                <span class="text-xs text-zinc-400 ml-auto">({{ $operator->buses_count ?? 0 }} buses)</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </aside>

            <!-- Results Section -->
            <div class="flex-1">
                <!-- Sort and Results Info -->
                <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                    <p class="text-sm text-zinc-500" id="tripCountDisplay">
                        {{ $trips->count() }} trips found
                    </p>
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-zinc-500 font-medium">Sort by:</label>
                        <select id="sortSelect" class="px-3 py-1.5 rounded-lg border border-zinc-200 text-sm font-medium text-gray-800 focus:border-primary focus:ring-1 focus:ring-primary outline-none transition-all cursor-pointer">
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="departure" selected>Departure Time</option>
                            <option value="duration">Duration</option>
                            <option value="rating">Rating</option>
                        </select>
                    </div>
                </div>

                <!-- Results Container -->
                <div id="resultsContainer">
                    @if($trips->count() > 0)
                        @foreach($trips as $trip)
                        <div class="trip-card bg-white rounded-2xl shadow-sm border border-zinc-100 overflow-hidden hover:shadow-md transition-all relative mb-6"
                             data-price="{{ $trip->fare }}"
                             data-time="{{ $trip->time_category }}"
                             data-operator="{{ $trip->bus->operator_id ?? '' }}"
                             data-departure="{{ $trip->departure_time }}"
                             data-duration="{{ $trip->duration_minutes ?? 270 }}"
                             data-rating="{{ $trip->bus->rating ?? 4.0 }}">

                            @if(isset($trip->bus) && ($trip->bus->class_type ?? 'standard') === 'luxury')
                                <div class="absolute top-0 right-0 bg-primary px-4 py-1 rounded-bl-xl text-xs font-bold text-white z-10">
                                    {{ $trip->bus->class_type }}
                                </div>
                            @endif

                            @if($trip->available_seats == 0)
                                <div class="absolute inset-0 bg-black/5 flex items-center justify-center rounded-2xl z-5">
                                    <div class="bg-red-500 text-white px-6 py-2 rounded-full font-extrabold text-sm transform -rotate-12 uppercase tracking-wider">
                                        Sold Out
                                    </div>
                                </div>
                            @endif

                            <div class="p-6">
                                <div class="flex flex-col md:flex-row md:items-center gap-6">
                                    <!-- Operator Info -->
                                    <div class="flex flex-row md:flex-col items-center md:items-start gap-3 md:gap-1 min-w-[80px]">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold text-xs">
                                            {{ isset($trip->bus->operator) ? substr($trip->bus->operator->name, 0, 3) : 'BUS' }}
                                        </div>
                                        <div>
                                            <div class="font-extrabold text-sm text-center md:text-left">
                                                {{ $trip->bus->operator->name ?? 'Unknown' }}
                                            </div>
                                            <div class="flex items-center justify-center md:justify-start gap-1">
                                                <span class="material-symbols-outlined text-xs text-primary">star</span>
                                                <span class="text-xs">{{ number_format($trip->bus->rating ?? 4.0, 1) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Trip Details -->
                                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="text-center md:text-left">
                                            <div class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Departure</div>
                                            <div class="text-2xl md:text-3xl font-headline font-extrabold">
                                                {{ \Carbon\Carbon::parse($trip->departure_time)->format('H:i') }}
                                            </div>
                                            <div class="text-xs text-zinc-500">{{ $trip->departure_terminal ?? 'Main Terminal' }}</div>
                                        </div>

                                        <div class="flex flex-col items-center justify-center">
                                            <div class="text-xs font-bold text-primary">{{ $trip->duration }}</div>
                                            <div class="flex items-center gap-2 w-full max-w-[120px]">
                                                <div class="w-2 h-2 rounded-full border-2 border-primary"></div>
                                                <div class="flex-1 h-[2px] border-t-2 border-dashed border-zinc-300 relative">
                                                    <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white text-xs">directions_bus</span>
                                                </div>
                                                <div class="w-2 h-2 rounded-full bg-primary"></div>
                                            </div>
                                            <div class="text-xs text-zinc-400">Direct Trip</div>
                                        </div>

                                        <div class="text-center md:text-right">
                                            <div class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Arrival</div>
                                            <div class="text-2xl md:text-3xl font-headline font-extrabold">
                                                {{ \Carbon\Carbon::parse($trip->departure_time)->addMinutes($trip->duration_minutes ?? 270)->format('H:i') }}
                                            </div>
                                            <div class="text-xs text-zinc-500">{{ $trip->arrival_terminal ?? 'Destination Terminal' }}</div>
                                        </div>
                                    </div>

                                    <!-- Price & Action -->
                                    <div class="flex flex-row md:flex-col items-center md:items-end gap-4 md:gap-1 min-w-[120px]">
                                        <div>
                                            <span class="text-xs text-zinc-500">ZMW</span>
                                            <span class="text-2xl md:text-3xl font-headline font-extrabold">{{ number_format($trip->fare, 0) }}</span>
                                        </div>
                                        <div class="text-xs font-bold {{ $trip->available_seats <= 5 && $trip->available_seats > 0 ? 'text-red-500' : 'text-primary' }}">
                                            {{ $trip->available_seats == 0 ? 'Sold Out' : $trip->available_seats . ' seats left' }}
                                        </div>
                                        @if($trip->available_seats > 0)
                                            <a href="{{ route('booking.seats', $trip) }}" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-primary-dark transition-all inline-flex items-center gap-2">
                                                <span>Select Seats</span>
                                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                            </a>
                                        @else
                                            <button class="bg-zinc-100 hover:bg-zinc-200 text-gray-800 px-6 py-2.5 rounded-xl font-bold text-sm transition-all inline-flex items-center gap-2" onclick="notifyMe({{ $trip->id }})">
                                                <span class="material-symbols-outlined text-sm">notifications</span>
                                                Notify Me
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <!-- Amenities -->
                                @if(isset($trip->bus->amenities) && $trip->bus->amenities->count() > 0)
                                <div class="flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-zinc-100">
                                    @foreach($trip->bus->amenities->take(3) as $amenity)
                                    <span class="flex items-center gap-1 text-xs text-zinc-600">
                                        <span class="material-symbols-outlined text-sm text-primary">{{ $amenity->icon ?? 'check_circle' }}</span>
                                        {{ $amenity->name }}
                                    </span>
                                    @endforeach
                                    @if($trip->bus->amenities->count() > 3)
                                    <span class="text-xs text-zinc-400">+{{ $trip->bus->amenities->count() - 3 }} more</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="bg-white rounded-2xl shadow-sm border border-zinc-100 p-12 text-center">
                            <span class="material-symbols-outlined text-6xl text-zinc-300 block mb-4">search_off</span>
                            <h3 class="font-headline text-2xl font-bold text-gray-800 mb-2">No trips found</h3>
                            <p class="text-zinc-500 mb-6">Try adjusting your search criteria or travel dates</p>
                            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                                <span class="material-symbols-outlined text-sm">arrow_back</span>
                                Back to search
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-10">
                    <div class="bg-primary/5 border border-primary/10 rounded-xl p-5 cursor-pointer hover:bg-primary/10 transition-all">
                        <span class="material-symbols-outlined text-primary block mb-2">security</span>
                        <h4 class="font-headline font-bold text-sm mb-1">Verified Operators</h4>
                        <p class="text-xs text-zinc-500">All operators pass safety inspection.</p>
                    </div>
                    <div class="bg-primary/5 border border-primary/10 rounded-xl p-5 cursor-pointer hover:bg-primary/10 transition-all">
                        <span class="material-symbols-outlined text-primary block mb-2">confirmation_number</span>
                        <h4 class="font-headline font-bold text-sm mb-1">Instant Ticket</h4>
                        <p class="text-xs text-zinc-500">QR via SMS/WhatsApp after payment.</p>
                    </div>
                <a href="/support" class="bg-zinc-50 rounded-xl p-5 cursor-pointer hover:bg-zinc-100 transition-all block">
                    <span class="material-symbols-outlined text-zinc-500 block mb-2">support_agent</span>
                    <h4 class="font-headline font-bold text-sm mb-1">24/7 Support</h4>
                    <p class="text-xs text-zinc-500">Zambian support team available.</p>
                </a>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Mobile Bottom Navigation -->
<div class="lg:hidden fixed bottom-0 left-0 w-full bg-white border-t border-zinc-100 px-6 py-3 flex justify-between z-40">
    <div class="mobile-nav-item flex flex-col items-center gap-0.5 cursor-pointer text-zinc-500 hover:text-primary transition-colors" onclick="window.location.href='{{ route('home') }}'">
        <span class="material-symbols-outlined text-xl">search</span>
        <span class="text-[0.625rem] font-bold">Find</span>
    </div>
    <div class="mobile-nav-item flex flex-col items-center gap-0.5 cursor-pointer text-primary transition-colors" onclick="window.location.href='{{ route('trips.search') }}'">
        <span class="material-symbols-outlined text-xl">confirmation_number</span>
        <span class="text-[0.625rem] font-bold">Trips</span>
    </div>
    <div class="mobile-nav-item flex flex-col items-center gap-0.5 cursor-pointer text-zinc-500 hover:text-primary transition-colors" onclick="window.location.href='{{ route('home') }}'">
        <span class="material-symbols-outlined text-xl">account_circle</span>
        <span class="text-[0.625rem] font-bold">Profile</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // State
    let activeFilters = {
        time: 'morning',
        maxPrice: {{ $maxPrice }},
        operators: []
    };

    let currentTrips = @json($trips);
    let filteredTrips = [...currentTrips];

    // DOM Elements
    const resultsContainer = document.getElementById('resultsContainer');
    const priceSlider = document.getElementById('priceSlider');
    const priceValue = document.getElementById('priceValue');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const sortSelect = document.getElementById('sortSelect');
    const tripCountDisplay = document.getElementById('tripCountDisplay');

    // Initialize operator filters from checked checkboxes
    document.querySelectorAll('.operator-item .checkbox.checked').forEach(el => {
        const opId = el.closest('.operator-item').dataset.op;
        if (opId) activeFilters.operators.push(opId);
    });

    // Show Toast
    function showToast(msg) {
        const toast = document.getElementById('toastMsg');
        toast.innerText = msg;
        toast.classList.add('opacity-100', 'translate-y-0');
        toast.classList.remove('opacity-0', 'translate-y-5');
        setTimeout(() => {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'translate-y-5');
        }, 2000);
    }

    // Render Trips
    function renderTrips(loading = false) {
        if (loading) {
            resultsContainer.innerHTML = `
                <div class="skeleton-card rounded-2xl h-[180px] mb-6"></div>
                <div class="skeleton-card rounded-2xl h-[180px] mb-6"></div>
                <div class="skeleton-card rounded-2xl h-[180px] mb-6"></div>
            `;
            return;
        }

        // Filter trips
        filteredTrips = currentTrips.filter(trip => {
            if (trip.fare > activeFilters.maxPrice) return false;
            if (activeFilters.time !== 'all' && (trip.time_category || 'morning') !== activeFilters.time) return false;
            if (activeFilters.operators.length > 0 && !activeFilters.operators.includes(String(trip.bus?.operator_id || ''))) return false;
            return true;
        });

        // Sort trips
        const sortBy = sortSelect?.value || 'departure';
        filteredTrips.sort((a, b) => {
            switch(sortBy) {
                case 'price-asc': return a.fare - b.fare;
                case 'price-desc': return b.fare - a.fare;
                case 'departure': return a.departure_time.localeCompare(b.departure_time);
                case 'duration': return (a.duration_minutes || 270) - (b.duration_minutes || 270);
                case 'rating': return (b.bus?.rating || 4.0) - (a.bus?.rating || 4.0);
                default: return 0;
            }
        });

        // Update count
        if (tripCountDisplay) {
            tripCountDisplay.textContent = filteredTrips.length + ' trips found';
        }
        document.getElementById('tripCount').textContent = filteredTrips.length;

        // Render
        if (filteredTrips.length === 0) {
            resultsContainer.innerHTML = `
                <div class="bg-white rounded-2xl shadow-sm border border-zinc-100 p-12 text-center">
                    <span class="material-symbols-outlined text-6xl text-zinc-300 block mb-4">search_off</span>
                    <h3 class="font-headline text-2xl font-bold text-gray-800 mb-2">No trips match your filters</h3>
                    <p class="text-zinc-500 mb-6">Try adjusting your search criteria</p>
                    <button onclick="clearAllFilters()" class="text-primary font-bold hover:underline">Clear all filters</button>
                </div>
            `;
            return;
        }

        // Build HTML for each trip
        let html = '';
        filteredTrips.forEach(trip => {
            const isSoldOut = trip.available_seats === 0;
            const luxuryBadge = (trip.bus?.class_type ?? 'standard') === 'luxury' ?
                `<div class="absolute top-0 right-0 bg-primary px-4 py-1 rounded-bl-xl text-xs font-bold text-white z-10">${trip.bus.class_type}</div>` : '';
            const departureTime = trip.departure_time || '06:00';
            const duration = trip.duration || '~4h 30m';
            const durationMinutes = trip.duration_minutes || 270;

            // Calculate arrival time
            const depParts = departureTime.split(':');
            const depMinutes = parseInt(depParts[0]) * 60 + parseInt(depParts[1]);
            const arrMinutes = depMinutes + durationMinutes;
            const arrHours = Math.floor(arrMinutes / 60) % 24;
            const arrMins = arrMinutes % 60;
            const arrivalTime = `${String(arrHours).padStart(2, '0')}:${String(arrMins).padStart(2, '0')}`;

            const operatorName = trip.bus?.operator?.name || 'Unknown';
            const operatorInitials = operatorName.substring(0, 3).toUpperCase();
            const rating = trip.bus?.rating || 4.0;

            html += `
                <div class="trip-card bg-white rounded-2xl shadow-sm border border-zinc-100 overflow-hidden hover:shadow-md transition-all relative mb-6">
                    ${luxuryBadge}

                    ${isSoldOut ? `
                        <div class="absolute inset-0 bg-black/5 flex items-center justify-center rounded-2xl z-5">
                            <div class="bg-red-500 text-white px-6 py-2 rounded-full font-extrabold text-sm transform -rotate-12 uppercase tracking-wider">
                                Sold Out
                            </div>
                        </div>
                    ` : ''}

                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center gap-6">
                            <!-- Operator Info -->
                            <div class="flex flex-row md:flex-col items-center md:items-start gap-3 md:gap-1 min-w-[80px]">
                                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-primary to-primary-dark flex items-center justify-center text-white font-bold text-xs">
                                    ${operatorInitials}
                                </div>
                                <div>
                                    <div class="font-extrabold text-sm text-center md:text-left">${operatorName}</div>
                                    <div class="flex items-center justify-center md:justify-start gap-1">
                                        <span class="material-symbols-outlined text-xs text-primary">star</span>
                                        <span class="text-xs">${rating.toFixed(1)}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Trip Details -->
                            <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center md:text-left">
                                    <div class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Departure</div>
                                    <div class="text-2xl md:text-3xl font-headline font-extrabold">${departureTime}</div>
                                    <div class="text-xs text-zinc-500">${trip.departure_terminal || 'Main Terminal'}</div>
                                </div>

                                <div class="flex flex-col items-center justify-center">
                                    <div class="text-xs font-bold text-primary">${duration}</div>
                                    <div class="flex items-center gap-2 w-full max-w-[120px]">
                                        <div class="w-2 h-2 rounded-full border-2 border-primary"></div>
                                        <div class="flex-1 h-[2px] border-t-2 border-dashed border-zinc-300 relative">
                                            <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white text-xs">directions_bus</span>
                                        </div>
                                        <div class="w-2 h-2 rounded-full bg-primary"></div>
                                    </div>
                                    <div class="text-xs text-zinc-400">Direct Trip</div>
                                </div>

                                <div class="text-center md:text-right">
                                    <div class="text-xs uppercase font-bold text-zinc-400 tracking-wider">Arrival</div>
                                    <div class="text-2xl md:text-3xl font-headline font-extrabold">${arrivalTime}</div>
                                    <div class="text-xs text-zinc-500">${trip.arrival_terminal || 'Destination Terminal'}</div>
                                </div>
                            </div>

                            <!-- Price & Action -->
                            <div class="flex flex-row md:flex-col items-center md:items-end gap-4 md:gap-1 min-w-[120px]">
                                <div>
                                    <span class="text-xs text-zinc-500">ZMW</span>
                                    <span class="text-2xl md:text-3xl font-headline font-extrabold">${trip.fare.toLocaleString()}</span>
                                </div>
                                <div class="text-xs font-bold ${trip.available_seats <= 4 && trip.available_seats > 0 ? 'text-red-500' : 'text-primary'}">
                                    ${isSoldOut ? 'Sold Out' : trip.available_seats + ' seats left'}
                                </div>
                                ${!isSoldOut ? `
                                    <a href="/booking/seats/${trip.id}" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-primary-dark transition-all inline-flex items-center gap-2">
                                        <span>Select Seats</span>
                                        <span class="material-symbols-outlined text-sm">arrow_forward</span>
                                    </a>
                                ` : `
                                    <button class="bg-zinc-100 hover:bg-zinc-200 text-gray-800 px-6 py-2.5 rounded-xl font-bold text-sm transition-all inline-flex items-center gap-2" onclick="notifyMe(${trip.id})">
                                        <span class="material-symbols-outlined text-sm">notifications</span>
                                        Notify Me
                                    </button>
                                `}
                            </div>
                        </div>

                        <!-- Amenities -->
                        ${trip.bus?.amenities && trip.bus.amenities.length > 0 ? `
                            <div class="flex flex-wrap items-center gap-4 mt-4 pt-4 border-t border-zinc-100">
                                ${trip.bus.amenities.slice(0, 3).map(amenity => `
                                    <span class="flex items-center gap-1 text-xs text-zinc-600">
                                        <span class="material-symbols-outlined text-sm text-primary">${amenity.icon || 'check_circle'}</span>
                                        ${amenity.name}
                                    </span>
                                `).join('')}
                                ${trip.bus.amenities.length > 3 ? `
                                    <span class="text-xs text-zinc-400">+${trip.bus.amenities.length - 3} more</span>
                                ` : ''}
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        resultsContainer.innerHTML = html;
    }

    // Filter Functions
    function applyFilters() {
        renderTrips();
        showToast('Filters updated');
    }

    window.clearAllFilters = function() {
        activeFilters = { time: 'all', maxPrice: {{ $maxPrice }}, operators: [] };

        // Reset UI
        document.querySelectorAll('.filter-time-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelectorAll('.operator-item .checkbox').forEach(el => {
            el.classList.remove('checked', 'bg-primary', 'border-primary');
            el.classList.add('border-zinc-300');
            el.innerHTML = '';
        });

        if (priceSlider) {
            priceSlider.value = activeFilters.maxPrice;
            priceValue.textContent = 'K' + activeFilters.maxPrice;
        }

        renderTrips();
        showToast('All filters cleared');
    }

    window.goBack = function() {
        window.location.href = '{{ route('home') }}';
    }

    window.notifyMe = function(tripId) {
        showToast('You will be notified when seats become available');
        // You can implement actual notification logic here
    }

    // Event Listeners
    if (priceSlider) {
        priceSlider.addEventListener('input', function() {
            const value = parseInt(this.value);
            activeFilters.maxPrice = value;
            priceValue.textContent = 'K' + value;
            renderTrips();
        });
    }

    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            renderTrips();
        });
    }

    // Time filter buttons
    document.querySelectorAll('.filter-time-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-time-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeFilters.time = this.dataset.time;
            renderTrips();
            showToast('Filtered by ' + this.querySelector('span:last-child').textContent);
        });
    });

    // Operator filter checkboxes
    document.querySelectorAll('.operator-item').forEach(item => {
        item.addEventListener('click', function() {
            const opId = this.dataset.op;
            const checkbox = this.querySelector('.checkbox');

            if (activeFilters.operators.includes(opId)) {
                activeFilters.operators = activeFilters.operators.filter(id => id !== opId);
                checkbox.classList.remove('checked', 'bg-primary', 'border-primary');
                checkbox.classList.add('border-zinc-300');
                checkbox.innerHTML = '';
            } else {
                activeFilters.operators.push(opId);
                checkbox.classList.add('checked', 'bg-primary', 'border-primary');
                checkbox.classList.remove('border-zinc-300');
                checkbox.innerHTML = '<span class="material-symbols-outlined text-white text-xs">check</span>';
            }
            renderTrips();
        });
    });

    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', window.clearAllFilters);
    }

    // Initial render
    renderTrips();
});
</script>
@endpush
