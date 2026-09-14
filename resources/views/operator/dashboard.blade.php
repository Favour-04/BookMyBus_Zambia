@extends('layouts.operator')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@push('head')
<style>
.bento-grid {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 24px;
        }

        /* Drawer transition */
        #trip-drawer {
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        #trip-drawer.open {
            transform: translateX(0);
        }

        #drawer-backdrop {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }

        #drawer-backdrop.open {
            opacity: 1;
            pointer-events: auto;
        }

        /* Row hover action reveal */
        .trip-row .row-actions {
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .trip-row:hover .row-actions {
            opacity: 1;
        }
</style>
@endpush

@section('content')


            <!-- Key Performance Indicators Row -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                <!-- Total Bookings -->
                <div
                    class="bg-surface-container-low dark:bg-surface-container rounded-2xl p-5 border border-outline-variant/10 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-label-md font-bold text-on-surface-variant uppercase tracking-wider">Total
                            Bookings</span>
                        <span class="material-symbols-outlined text-primary text-headline-sm">confirmation_number</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-headline-lg font-black tracking-tight text-on-surface">{{
                            number_format($total_bookings) }}</h3>
                        <p class="text-body-sm text-on-surface-variant mt-1 flex items-center gap-1">
                            <span class="text-primary font-bold">{{ $bookings_trend }}</span> vs last month
                        </p>
                    </div>
                </div>

                <div
                    class="bg-surface-container-low dark:bg-surface-container rounded-2xl p-5 border border-outline-variant/10 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-label-md font-bold text-on-surface-variant uppercase tracking-wider">Revenue
                            Generated</span>
                        <span class="material-symbols-outlined text-secondary text-headline-sm">payments</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-headline-lg font-black tracking-tight text-on-surface">ZMW {{
                            number_format($revenue, 2) }}</h3>
                        <p class="text-body-sm text-on-surface-variant mt-1 flex items-center gap-1">
                            <span class="text-secondary font-bold">{{ $revenue_trend }}</span> Daily Avg: <span
                                class="font-bold text-on-surface">{{ $revenue_average }}</span>
                        </p>
                    </div>
                </div>

                <div
                    class="bg-surface-container-low dark:bg-surface-container rounded-2xl p-5 border border-outline-variant/10 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-label-md font-bold text-on-surface-variant uppercase tracking-wider">Active
                            Fleet</span>
                        <span class="material-symbols-outlined text-tertiary text-headline-sm">directions_bus</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-headline-lg font-black tracking-tight text-on-surface">{{
                            $active_trips_count }}/{{ $total_trips_today }}</h3>
                        <p class="text-body-sm text-on-surface-variant mt-1 flex items-center gap-1">
                            Avg Occupancy: <span class="font-bold text-primary">{{ $avg_occupancy }}%</span>
                        </p>
                    </div>
                </div>

                <!-- Today's Bookings -->
                <div
                    class="bg-surface-container-low dark:bg-surface-container rounded-2xl p-5 border border-outline-variant/10 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-label-md font-bold text-on-surface-variant uppercase tracking-wider">Today's
                            Bookings</span>
                        <span class="material-symbols-outlined text-primary text-headline-sm">event_available</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-headline-lg font-black tracking-tight text-on-surface">{{
                            number_format($today_bookings) }}</h3>
                        <p class="text-body-sm text-on-surface-variant mt-1 flex items-center gap-1">
                            <span class="text-primary font-bold">{{ $pending_bookings }} pending</span> awaiting payment
                        </p>
                    </div>
                </div>

                <!-- Cancelled Bookings -->
                <div
                    class="bg-surface-container-low dark:bg-surface-container rounded-2xl p-5 border border-outline-variant/10 shadow-sm flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <span class="text-label-md font-bold text-on-surface-variant uppercase tracking-wider">Cancelled
                            (Month)</span>
                        <span class="material-symbols-outlined text-tertiary text-headline-sm">cancel</span>
                    </div>
                    <div class="mt-4">
                        <h3 class="text-headline-lg font-black tracking-tight text-on-surface">{{
                            number_format($cancelled_bookings) }}</h3>
                        <p class="text-body-sm text-on-surface-variant mt-1">This month</p>
                    </div>
                </div>
            </div>

            <!-- Revenue Card (ZMW) -->
            <div
                class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-on-surface-variant font-label-caps text-label-caps uppercase">Revenue
                        (ZMW)</span>
                    <div class="p-2 bg-secondary-container/10 rounded-lg">
                        <span class="material-symbols-outlined text-secondary" data-icon="payments">payments</span>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-headline-lg text-headline-lg text-on-surface">{{ number_format($revenue)
                        }}</span>
                    <span class="text-seat-available text-body-sm font-bold flex items-center">
                        <span class="material-symbols-outlined text-sm mr-0.5"
                            data-icon="trending_up">trending_up</span>
                        {{ $revenue_trend }}
                    </span>
                </div>
                <p class="text-on-surface-variant text-body-sm mt-1">Daily average: {{ $revenue_average }}</p>
            </div>

            <!-- Active Trips Card -->
            <div
                class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-on-surface-variant font-label-caps text-label-caps uppercase">Active
                        Trips</span>
                    <div class="p-2 bg-primary-container/10 rounded-lg">
                        <span class="material-symbols-outlined text-primary" data-icon="route">route</span>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-headline-lg text-headline-lg text-on-surface">{{ $active_trips_count }}</span>
                    <span class="text-on-surface-variant text-body-sm font-medium">/ {{ $total_trips_today }}
                        today</span>
                </div>
                <div class="w-full bg-surface-container rounded-full h-1.5 mt-4">
                    <div class="bg-primary h-1.5 rounded-full"
                        style="width: {{ ($active_trips_count / max($total_trips_today, 1)) * 100 }}%"></div>
                </div>
            </div>

            <!-- Average Occupancy Highlight Card -->
            <div class="bg-primary p-6 rounded-xl shadow-lg shadow-primary/20 relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-primary-fixed font-label-caps text-label-caps uppercase">Avg
                            Occupancy</span>
                        <span class="material-symbols-outlined text-primary-fixed" data-icon="groups">groups</span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="font-headline-lg text-headline-lg text-on-primary">{{ $avg_occupancy }}%</span>
                        <span class="text-primary-fixed text-body-sm font-bold flex items-center">
                            <span class="material-symbols-outlined text-sm mr-0.5"
                                data-icon="keyboard_double_arrow_up">keyboard_double_arrow_up</span>
                            High
                        </span>
                    </div>
                    <p class="text-primary-fixed/80 text-body-sm mt-1">Across all inter-city routes</p>
                </div>
                <!-- Decorative background vector element -->
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <span class="material-symbols-outlined text-9xl text-on-primary"
                        data-icon="directions_bus">directions_bus</span>
                </div>
            </div>

        </div>

        <!-- Dashboard Content Bento Grid -->
        <div class="bento-grid">

            <!-- Section 1: Live Fleet Status -->
            <div
                class="col-span-12 lg:col-span-4 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 flex flex-col h-[500px]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Live Fleet Status</h3>
                    <button class="text-primary text-body-sm font-bold flex items-center gap-1 hover:underline">
                        Map View <span class="material-symbols-outlined text-sm" data-icon="map">map</span>
                    </button>
                </div>

                <div class="flex-grow overflow-y-auto custom-scrollbar pr-2 space-y-4">
                    @forelse($fleet_status as $bus)
                    <div class="bg-surface border border-outline-variant/20 p-4 rounded-xl shadow-xs">
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <span class="text-body-md font-bold text-on-surface tracking-wide">{{ $bus['plate']
                                    }}</span>
                                <span class="text-body-sm text-on-surface-variant block mt-0.5">{{ $bus['route']
                                    }}</span>
                            </div>
                            <span class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $bus['badge_class'] }}">
                                {{ $bus['status'] }}
                            </span>
                        </div>

                        <div class="w-full bg-surface-container-highest rounded-full h-2 mt-3 overflow-hidden">
                            <div class="{{ $bus['bar_class'] }} h-2 rounded-full transition-all duration-500"
                                style="width: {{ $bus['progress'] }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1.5">
                            <span class="text-label-sm font-medium text-on-surface-variant">{{
                                $bus['progress_label'] }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-body-md text-on-surface-variant text-center py-4">No active trips dispatched on
                        the terminal lines today.</p>
                    @endforelse
                </div>
            </div>

            <!-- Section 2: Upcoming Trips Table -->
            <div
                class="col-span-12 lg:col-span-8 bg-surface-container-lowest rounded-xl border border-outline-variant/15 flex flex-col h-[500px] overflow-hidden">
                <div class="p-6 border-b border-outline-variant/15 flex items-center justify-between">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Upcoming Trips</h3>
                    <div class="flex gap-2">
                        <button
                            class="px-3 py-1 bg-surface-container text-body-sm font-bold rounded-lg hover:bg-surface-container-high transition-colors">Today</button>
                        <button
                            class="px-3 py-1 text-on-surface-variant text-body-sm font-medium rounded-lg hover:bg-surface-container-low transition-colors">Tomorrow</button>
                    </div>
                </div>

                <div class="flex-grow overflow-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-high sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">
                                    Trip ID</th>
                                <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">
                                    Route</th>
                                <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">
                                    Departure</th>
                                <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">
                                    Occupancy</th>
                                <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">
                                    Status</th>
                                <th class="px-6 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10 w-full">
                            @forelse($upcoming_trips as $trip)
                            <tr class="hover:bg-surface-container-low transition-colors group w-full">
                                <td class="py-3 px-4 font-bold text-on-surface tracking-wide">{{ $trip['id'] }}</td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-on-surface flex items-center gap-1">
                                        {{ $trip['route_from'] }} <span
                                            class="material-symbols-outlined text-sm text-on-surface-variant">arrow_forward</span>
                                        {{ $trip['route_to'] }}
                                    </div>
                                    <div class="text-body-sm text-on-surface-variant capitalize mt-0.5">{{
                                        $trip['class'] }}</div>
                                </td>
                                <td class="py-3 px-4 font-bold text-on-surface">{{ $trip['departure'] }} hrs</td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-16 bg-surface-container-highest rounded-full h-1.5 overflow-hidden hidden sm:block">
                                            <div class="bg-primary h-1.5 rounded-full"
                                                style="width: {{ $trip['occupancy_percentage'] }}%"></div>
                                        </div>
                                        <span class="font-semibold text-on-surface-variant text-body-sm">
                                            {{ $trip['booked'] }}/{{ $trip['capacity'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-right min-w-max">
                                    <span
                                        class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $trip['status_type'] === 'delayed' ? 'bg-tertiary-container/10 text-tertiary' : 'bg-primary/10 text-primary' }}">
                                        {{ $trip['status'] }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 px-4 text-center text-on-surface-variant">
                                    No upcoming line manifests scheduled for this operator profile.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 3: Recent Bookings -->
            <div
                class="col-span-12 lg:col-span-6 bg-surface-container-lowest rounded-xl border border-outline-variant/15 flex flex-col h-[400px] overflow-hidden">
                <div class="p-6 border-b border-outline-variant/15 flex items-center justify-between">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Recent Bookings</h3>
                    <a href="{{ route('operator.bookings.index') }}"
                        class="text-primary text-body-sm font-bold flex items-center gap-1 hover:underline">
                        View All <span class="material-symbols-outlined text-sm">arrow_forward</span>
                    </a>
                </div>

                <div class="flex-grow overflow-auto custom-scrollbar">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-high sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase">Reference</th>
                                <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase">Passenger</th>
                                <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase">Route</th>
                                <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase">Seat</th>
                                <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase">Amount</th>
                                <th class="px-6 py-3 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($recent_bookings as $booking)
                            <tr class="hover:bg-surface-container-low transition-colors">
                                <td class="py-3 px-4 font-bold text-on-surface text-body-sm">{{ $booking['reference'] }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface">{{ $booking['passenger'] }}</td>
                                <td class="py-3 px-4 text-body-sm text-on-surface-variant">{{ $booking['route'] }}</td>
                                <td class="py-3 px-4 text-body-sm font-semibold text-on-surface">{{ $booking['seat'] }}</td>
                                <td class="py-3 px-4 text-body-sm font-bold text-on-surface">ZMW {{ number_format($booking['amount'], 2) }}</td>
                                <td class="py-3 px-4">
                                    <span class="text-label-sm font-bold px-2.5 py-1 rounded-full 
                                        {{ $booking['status'] === 'confirmed' ? 'bg-primary/10 text-primary' : ($booking['status'] === 'pending' ? 'bg-secondary-container/10 text-secondary' : 'bg-tertiary-container/10 text-tertiary') }}">
                                        {{ ucfirst($booking['status']) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 px-4 text-center text-on-surface-variant text-body-sm">
                                    No recent bookings found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Section 4: Top Routes -->
            <div
                class="col-span-12 lg:col-span-6 bg-surface-container-lowest rounded-xl border border-outline-variant/15 flex flex-col h-[400px] overflow-hidden">
                <div class="p-6 border-b border-outline-variant/15 flex items-center justify-between">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Top Routes</h3>
                    <span class="text-body-sm text-on-surface-variant font-medium">By bookings</span>
                </div>

                <div class="flex-grow overflow-auto custom-scrollbar p-6 space-y-4">
                    @forelse($top_routes as $index => $route)
                    <div class="flex items-center gap-4">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="font-bold text-primary text-body-sm">#{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-bold text-body-sm text-on-surface truncate">{{ $route['route'] }}</span>
                                <span class="text-body-sm font-semibold text-on-surface-variant">{{ $route['bookings_count'] }} bookings</span>
                            </div>
                            <div class="w-full bg-surface-container-highest rounded-full h-1.5 overflow-hidden">
                                <div class="bg-primary h-1.5 rounded-full"
                                    style="width: {{ min(($route['bookings_count'] / max($top_routes[0]['bookings_count'] ?? 1, 1)) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-body-md text-on-surface-variant text-center py-4">No route data available yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Section 5: Recent Notifications & Critical Operational Alerts -->
            <div class="col-span-12 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-headline-md text-headline-md text-on-surface">Recent Notifications & Alerts</h3>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 bg-tertiary rounded-full"></span>
                        <span class="text-body-sm font-bold text-tertiary">2 High Priority Alerts</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($alerts as $alert)
                    <div class="p-4 rounded-xl border {{ $alert['bg_class'] }} flex gap-4">
                        <div
                            class="w-10 h-10 rounded-lg {{ $alert['icon_bg'] }} flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined" data-icon="{{ $alert['icon'] }}">{{
                                $alert['icon'] }}</span>
                        </div>
                        <div class="flex-grow">
                            <h4 class="font-bold text-body-md {{ $alert['title_class'] }}">{{ $alert['title'] }}
                            </h4>
                            <p class="text-body-sm text-on-surface-variant mt-1 leading-relaxed">{{
                                $alert['message'] }}</p>
                            <button
                                class="mt-3 {{ $alert['btn_class'] }} font-bold text-body-sm flex items-center gap-1 hover:underline cursor-pointer">
                                {{ $alert['action_label'] }}
                                <span class="material-symbols-outlined text-sm"
                                    data-icon="{{ $alert['action_icon'] }}">{{ $alert['action_icon'] }}</span>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div> <!-- End Bento Grid -->

<!-- New Trip Drawer -->
    <aside id="trip-drawer"
        class="fixed top-0 right-0 h-full w-[440px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">

        <!-- Drawer header -->
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">New trip</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Schedule a departure for your fleet
                </p>
            </div>
            <button onclick="closeDrawer()"
                class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Drawer form -->
        <form action="{{ route('operator.trips.store') }}" method="POST"
            class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-6">
            @csrf

            <!-- Route -->
            <div>
                <label
                    class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Route</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">From</label>
                        <select name="origin"
                            class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                            @foreach($routes as $route)
                            <option value="{{ $route['from'] }}">{{ $route['from'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">To</label>
                        <select name="destination"
                            class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                            @foreach($routes as $route)
                            <option value="{{ $route['to'] }}">{{ $route['to'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Date &amp;
                    time</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Departure
                            date</label>
                        <input type="date" name="travel_date"
                            class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Departure
                            time</label>
                        <input type="time" name="departure_time"
                            class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Bus assignment -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Assign
                    bus</label>
                <div class="space-y-2">
                    @foreach($buses as $bus)
                    <label
                        class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/20 bg-surface-container-low cursor-pointer hover:border-primary/40 hover:bg-surface-container transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                        <input type="radio" name="bus_id" value="{{ $bus['id'] }}" class="accent-primary">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary"
                                style="font-size:16px">directions_bus</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-bold text-body-sm text-on-surface block">{{ $bus['plate'] }}</span>
                            <span class="text-body-sm text-on-surface-variant">{{ $bus['model'] }} · {{ $bus['capacity']
                                }} seats</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Driver assignment -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Assign
                    driver <span class="normal-case text-on-surface-variant/50">(optional)</span></label>
                <select name="driver_id"
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    <option value="">Select driver...</option>
                    @foreach($drivers as $driver)
                    <option value="{{ $driver['id'] }}">{{ $driver['full_name'] }} ({{ $driver['license_number'] }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Trip class & Fare -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Trip
                        class</label>
                    <select name="class"
                        class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                        <option value="economy">Economy</option>
                        <option value="business">Business</option>
                    </select>
                </div>
                <div>
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Fare
                        (ZMW)</label>
                    <div class="relative">
                        <span
                            class="absolute left-3 top-1/2 -translate-y-1/2 font-body-sm text-body-sm text-on-surface-variant">K</span>
                        <input type="number" name="fare" placeholder="0.00"
                            class="w-full pl-7 rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Notes <span
                        class="normal-case text-on-surface-variant/50">(optional)</span></label>
                <textarea name="notes" rows="3" placeholder="Driver assignment, stop notes, special instructions..."
                    class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3 resize-none"></textarea>
            </div>

            <!-- Drawer footer -->
            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawer()"
                    class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-body-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-2 flex-grow-[2] py-3 rounded-xl bg-primary text-on-primary text-body-sm font-bold hover:brightness-110 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">check_circle</span>
                    Schedule trip
                </button>
            </div>
        </form>
    </aside>

    <!-- Micro-interaction Event Handlers for Buttons/Links -->
@endsection

@push('scripts')
<script>
        // Drawer open/close
        function openDrawer() {
            document.getElementById('trip-drawer').classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }
        function closeDrawer() {
            document.getElementById('trip-drawer').classList.remove('open');
            document.getElementById('drawer-backdrop').classList.remove('open');
        }

        // Status filter
        let activeFilter = 'all';
        function setFilter(status, btn) {
            activeFilter = status;
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('bg-primary', 'text-on-primary');
                b.classList.add('text-on-surface-variant');
            });
            btn.classList.add('bg-primary', 'text-on-primary');
            btn.classList.remove('text-on-surface-variant');
            applyFilters();
        }

        // Search filter
        function filterTrips() { applyFilters(); }

        function applyFilters() {
            const q = document.getElementById('search-input').value.toLowerCase();
            document.querySelectorAll('.trip-row').forEach(row => {
                const matchStatus = activeFilter === 'all' || row.dataset.status === activeFilter;
                const matchSearch = !q || row.dataset.search.includes(q);
                row.style.display = matchStatus && matchSearch ? '' : 'none';
            });
        }

        // Micro-interactions
        document.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('mousedown', () => el.classList.add('scale-[0.98]'));
            el.addEventListener('mouseup',   () => el.classList.remove('scale-[0.98]'));
            el.addEventListener('mouseleave',() => el.classList.remove('scale-[0.98]'));
        });
    </script>
@endpush
