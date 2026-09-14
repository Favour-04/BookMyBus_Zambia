@extends('layouts.operator')

@section('title', 'Manage Trips')
@section('page_title', 'Manage Trips')

@push('head')
<style>
/* ── Tom Select MD3 overrides ── */
        .ts-wrapper { width: 100%; }

        .ts-control {
            border: 1px solid rgba(191, 202, 186, 0.3) !important;
            border-radius: 0.75rem !important;
            background: #f3f3f6 !important;
            padding: 0.625rem 0.75rem !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 12px !important;
            color: #1a1c1e !important;
            box-shadow: none !important;
            min-height: unset !important;
            cursor: text;
        }
        .ts-control input {
            font-family: 'Inter', sans-serif !important;
            font-size: 12px !important;
            color: #1a1c1e !important;
            line-height: 1.4 !important;
        }
        .ts-control input::placeholder { color: #40493e; opacity: 0.5; }

        .ts-wrapper.focus .ts-control {
            border-color: #004614 !important;
            box-shadow: 0 0 0 2px rgba(0, 70, 20, 0.15) !important;
            outline: none !important;
        }

        .ts-dropdown {
            border: 1px solid rgba(191, 202, 186, 0.2) !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important;
            font-family: 'Inter', sans-serif !important;
            font-size: 12px !important;
            color: #1a1c1e !important;
            background: #ffffff !important;
            margin-top: 4px !important;
            overflow: hidden;
        }
        .ts-dropdown-content { padding: 4px !important; max-height: 220px; }

        /* Province group headers */
        .ts-dropdown .optgroup-header {
            font-family: 'Inter', sans-serif !important;
            font-size: 10px !important;
            font-weight: 700 !important;
            letter-spacing: 0.1em !important;
            text-transform: uppercase !important;
            color: #40493e !important;
            padding: 8px 10px 4px !important;
            background: transparent !important;
            border-top: 1px solid rgba(191,202,186,0.2);
            margin-top: 2px;
        }
        .ts-dropdown .optgroup:first-child .optgroup-header { border-top: none; margin-top: 0; }

        /* City options */
        .ts-dropdown .option {
            padding: 7px 10px 7px 18px !important;
            border-radius: 6px !important;
            color: #1a1c1e !important;
            font-size: 12px !important;
            cursor: pointer;
        }
        .ts-dropdown .option:hover,
        .ts-dropdown .option.active { background: #f3f3f6 !important; color: #004614 !important; }
        .ts-dropdown .option.selected { background: rgba(0,70,20,0.08) !important; color: #004614 !important; font-weight: 600; }

        /* No results */
        .ts-dropdown .no-results {
            padding: 12px 10px !important;
            color: #40493e !important;
            font-size: 12px !important;
            text-align: center;
        }

        /* Hide default Tom Select arrow — we use Material icon */
        .ts-wrapper.single .ts-control:after { display: none !important; }
        .ts-wrapper .clear-button { color: #40493e !important; }

        
        
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }

        /* Drawer transition */
        #trip-drawer {
            transform: translateX(100%);
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #trip-drawer.open { transform: translateX(0); }
        #drawer-backdrop {
            opacity: 0; pointer-events: none;
            transition: opacity 0.25s ease;
        }
        #drawer-backdrop.open { opacity: 1; pointer-events: auto; }

        /* Row hover action reveal */
        .trip-row .row-actions { opacity: 0; transition: opacity 0.15s ease; }
        .trip-row:hover .row-actions { opacity: 1; }
</style>
@endpush

@section('header_actions')
            <div class="flex items-center gap-6">
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
                    <input
                        class="pl-10 pr-4 py-2 bg-surface-container-low border-none rounded-full w-64 text-body-sm focus:ring-2 focus:ring-primary transition-all"
                        placeholder="Search trips, buses, routes..."
                        type="text"
                        id="search-input"
                        oninput="filterTrips()" />
                </div>
                <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors relative" data-icon="notifications">
                        notifications
                        <span class="absolute top-0 right-0 w-2 h-2 bg-tertiary rounded-full"></span>
                    </button>
                    <button class="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors" data-icon="schedule">schedule</button>
@endsection

@section('content')


            <!-- Filter + Action Bar -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <!-- Status filter pills -->
                    <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1">
                        <button onclick="setFilter('all', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-bold bg-primary text-on-primary transition-all">
                            All trips
                        </button>
                        <button onclick="setFilter('scheduled', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            Scheduled
                        </button>
                        <button onclick="setFilter('on_route', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            On route
                        </button>
                        <button onclick="setFilter('delayed', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            Delayed
                        </button>
                        <button onclick="setFilter('completed', this)"
                            class="filter-btn px-3 py-1.5 rounded-lg text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
                            Completed
                        </button>
                    </div>

                    <!-- Date filter -->
                    <select class="pl-3 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-body-sm text-on-surface-variant focus:ring-2 focus:ring-primary">
                        <option>Today — {{ date('d M') }}</option>
                        <option>Yesterday</option>
                        <option>This week</option>
                        <option>This month</option>
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    <button class="flex items-center gap-2 px-4 py-2 border border-outline-variant/30 rounded-xl text-body-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-colors">
                        <span class="material-symbols-outlined text-sm">download</span>
                        Export
                    </button>
                    <button onclick="openDrawer()"
                        class="flex items-center gap-2 px-5 py-2 bg-primary text-on-primary rounded-xl text-body-sm font-bold hover:brightness-110 transition-all">
                        <span class="material-symbols-outlined text-sm">add</span>
                        New trip
                    </button>
                </div>
            </div>

            <!-- Trips Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="trips-table">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Trip</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Route</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Date &amp; time</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Bus</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Occupancy</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Fare</th>
                                <th class="px-5 py-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Status</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10" id="trips-tbody">
                            @forelse($trips as $trip)
                            @php
                                $occ_pct = round(($trip['booked'] / max($trip['capacity'], 1)) * 100);
                                $badge   = $status_styles[$trip['status_type']] ?? 'bg-surface-container text-on-surface-variant';
                            @endphp
                            <tr class="trip-row hover:bg-surface-container-low/60 transition-colors group"
                                data-status="{{ $trip['status_type'] }}"
                                data-search="{{ strtolower($trip['id'] . ' ' . $trip['route_from'] . ' ' . $trip['route_to'] . ' ' . $trip['bus']) }}">
                                <td class="px-5 py-4">
                                    <span class="font-bold text-body-sm text-on-surface tracking-wide">{{ $trip['id'] }}</span>
                                    <span class="block text-body-sm text-on-surface-variant capitalize mt-0.5">{{ $trip['class'] }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 font-bold text-body-md text-on-surface">
                                        {{ $trip['route_from'] }}
                                        <span class="material-symbols-outlined text-sm text-outline">arrow_forward</span>
                                        {{ $trip['route_to'] }}
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-medium text-body-sm text-on-surface">{{ $trip['date'] }}</span>
                                    <span class="block text-body-sm text-on-surface-variant">{{ $trip['departure'] }} hrs</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-primary/8 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-primary" style="font-size:15px">directions_bus</span>
                                        </div>
                                        <span class="font-medium text-body-sm text-on-surface">{{ $trip['bus'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-20 bg-surface-container-highest rounded-full h-1.5 overflow-hidden">
                                            <div class="bg-primary h-1.5 rounded-full transition-all" style="width: {{ $occ_pct }}%"></div>
                                        </div>
                                        <span class="text-body-sm font-semibold text-on-surface-variant whitespace-nowrap">
                                            {{ $trip['booked'] }}/{{ $trip['capacity'] }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold text-body-sm text-on-surface">ZMW {{ number_format($trip['fare']) }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="text-label-sm font-bold px-2.5 py-1 rounded-full {{ $badge }}">
                                        {{ $trip['status'] }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="row-actions flex items-center gap-1 justify-end">
                                        <a href="{{ route('operator.trips.bookings', $trip['route_id']) }}"
                                           class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-flex"
                                           title="View bookings">
                                            <span class="material-symbols-outlined" style="font-size:18px">book_online</span>
                                        </a>
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors" title="Edit trip">
                                            <span class="material-symbols-outlined" style="font-size:18px">edit</span>
                                        </button>
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors" title="View seat map">
                                            <span class="material-symbols-outlined" style="font-size:18px">event_seat</span>
                                        </button>
                                        @if(in_array($trip['status_type'], ['scheduled', 'delayed']))
                                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:text-error hover:bg-error-container/20 transition-colors" title="Cancel trip">
                                            <span class="material-symbols-outlined" style="font-size:18px">cancel</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-16 text-center">
                                    <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">directions_bus</span>
                                    <p class="font-body-md text-body-md text-on-surface-variant">No trips found.</p>
                                    <button onclick="openDrawer()" class="mt-4 text-primary font-bold text-body-sm hover:underline">Schedule your first trip</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Table footer -->
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-body-sm text-on-surface-variant">Showing {{ count($trips) }} trips</span>
                    <div class="flex items-center gap-1">
                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors disabled:opacity-40" disabled>
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_left</span>
                        </button>
                        <span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-body-sm font-bold">1</span>
                        <button class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                            <span class="material-symbols-outlined" style="font-size:18px">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

<!-- Drawer Backdrop -->
    <div id="drawer-backdrop"
        class="fixed inset-0 bg-on-surface/30 z-30"
        onclick="closeDrawer()">
    </div>

    <!-- New Trip Drawer -->
    <aside id="trip-drawer"
        class="fixed top-0 right-0 h-full w-[440px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">

        <!-- Drawer header -->
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline-md text-headline-md text-on-surface">New trip</h3>
                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5">Schedule a departure for your fleet</p>
            </div>
            <button onclick="closeDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Drawer form -->
        <form action="{{ route('operator.trips.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-6">
            @csrf

            <!-- Route -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Route</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="select-origin" class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">From</label>
                        <select id="select-origin" name="origin" placeholder="Search city..." required>
                            <option value="">Select origin</option>
                            @foreach(config('zambia_cities') as $province => $cities)
                                <optgroup label="{{ $province }}">
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="select-destination" class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">To</label>
                        <select id="select-destination" name="destination" placeholder="Search city..." required>
                            <option value="">Select destination</option>
                            @foreach(config('zambia_cities') as $province => $cities)
                                <optgroup label="{{ $province }}">
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}">{{ $city }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Date & Time -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Date &amp; time</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Departure date</label>
                        <input type="date" name="travel_date" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                    <div>
                        <label class="font-body-sm text-body-sm text-on-surface-variant block mb-1.5">Departure time</label>
                        <input type="time" name="departure_time" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Bus assignment -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Assign bus</label>
                <div class="space-y-2">
                    @foreach($buses as $bus)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-outline-variant/20 bg-surface-container-low cursor-pointer hover:border-primary/40 hover:bg-surface-container transition-all has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                        <input type="radio" name="bus_id" value="{{ $bus['id'] }}" class="accent-primary">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-primary" style="font-size:16px">directions_bus</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-bold text-body-sm text-on-surface block">{{ $bus['plate'] }}</span>
                            <span class="text-body-sm text-on-surface-variant">{{ $bus['model'] }} · {{ $bus['capacity'] }} seats</span>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- Trip class & Fare -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Trip class</label>
                    <select name="class" class="w-full rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                        <option value="economy">Economy</option>
                        <option value="business">Business</option>
                    </select>
                </div>
                <div>
                    <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Fare (ZMW)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 font-body-sm text-body-sm text-on-surface-variant">K</span>
                        <input type="number" name="fare" placeholder="0.00"
                            class="w-full pl-7 rounded-xl border-outline-variant/30 bg-surface-container-low text-body-sm text-on-surface focus:ring-2 focus:ring-primary py-2.5 px-3">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="font-label-caps text-label-caps text-on-surface-variant uppercase block mb-3">Notes <span class="normal-case text-on-surface-variant/50">(optional)</span></label>
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
@endsection

@push('head')
    <!-- Tom Select JS -->
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
@endpush

@push('scripts')
<script>
        // ── Tom Select: Origin & Destination ──────────────────────────────
        const tsConfig = {
            maxOptions: null,
            placeholder: 'Search city...',
            allowEmptyOption: false,
            openOnFocus: true,
            selectOnTab: true,
            // Prevent same city being chosen for both From and To
            onItemAdd(value, item) {
                const otherId = this.input.id === 'select-origin'
                    ? 'select-destination'
                    : 'select-origin';
                const other = document.getElementById(otherId).tomselect;
                if (other && other.getValue() === value) {
                    other.clear();
                }
            },
            render: {
                no_results(data, escape) {
                    return `<div class="no-results">No city found for "<strong>${escape(data.input)}</strong>"</div>`;
                }
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            new TomSelect('#select-origin', tsConfig);
            new TomSelect('#select-destination', tsConfig);
        });
    </script>

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
