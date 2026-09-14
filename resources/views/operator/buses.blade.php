@extends('layouts.operator')

@section('title', 'Fleet Management')
@section('page_title', 'Fleet Management')

@push('head')
<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #bfcaba; border-radius: 10px; }
        .drawer-panel { transform: translateX(100%); transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1); }
        .drawer-panel.open { transform: translateX(0); }
        .drawer-backdrop { opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
        .drawer-backdrop.open { opacity: 1; pointer-events: auto; }
</style>
@endpush

@section('header_actions')
                <button onclick="openDrawer()" class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl text-sm font-bold hover:bg-primary/90 transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span> Add Bus
                </button>
@endsection

@section('content')

            @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-primary/5 border border-primary/10 rounded-xl text-primary font-bold text-sm">
                <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="mb-6 flex items-center gap-3 px-5 py-4 bg-error-container/20 border border-error-container/30 rounded-xl text-error font-bold text-sm">
                <span class="material-symbols-outlined">error</span>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            <!-- Stats -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($totalBuses) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Buses</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-on-surface">{{ number_format($activeBuses) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Active</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold text-primary">{{ number_format($totalCapacity) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Total Seats</p>
                </div>
                <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 p-4">
                    <p class="text-2xl font-headline font-extrabold {{ $maintenanceDue > 0 ? 'text-tertiary' : 'text-on-surface' }}">{{ number_format($maintenanceDue) }}</p>
                    <p class="text-xs font-bold text-on-surface-variant uppercase mt-1">Maintenance Due (7 days)</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1">
                        @php $currentStatus = request('status', ''); @endphp
                        @foreach(['' => 'All', 'active' => 'Active', 'inactive' => 'Inactive'] as $val => $label)
                        <a href="{{ route('operator.buses.index', array_merge(request()->except('status', 'page'), ['status' => $val ?: null])) }}"
                           class="px-3 py-1.5 rounded-lg text-sm font-bold transition-all {{ ($currentStatus === $val) ? 'bg-primary text-on-primary' : 'text-on-surface-variant hover:bg-surface-container-low' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                    <select name="class" onchange="window.location=this.value"
                            class="pl-3 pr-8 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl text-sm text-on-surface-variant">
                        <option value="{{ route('operator.buses.index', request()->except('class', 'page')) }}">All Classes</option>
                        @foreach(['economy' => 'Economy', 'business' => 'Business', 'luxury' => 'Luxury'] as $val => $label)
                        <option value="{{ route('operator.buses.index', array_merge(request()->except('class', 'page'), ['class' => $val])) }}"
                            {{ request('class') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <form method="GET" action="{{ route('operator.buses.index') }}" class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 material-symbols-outlined text-outline text-sm">search</span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by plate or model..."
                           class="pl-10 pr-4 py-2 bg-surface-container-lowest border border-outline-variant/15 rounded-full w-72 text-sm focus:ring-2 focus:ring-primary transition-all">
                </form>
            </div>

            <!-- Table -->
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Plate / Model</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Class</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Capacity</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Amenities</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Last Service</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Mileage</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Status</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($buses as $bus)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded-lg bg-primary/10 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-primary text-sm">directions_bus</span>
                                        </div>
                                        <div>
                                            <span class="font-bold">{{ $bus->registration_number }}</span>
                                            @if($bus->model)
                                                <span class="block text-xs text-on-surface-variant">{{ $bus->model }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="capitalize">{{ $bus->bus_class }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="font-bold">{{ $bus->seat_capacity }} seats</span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($bus->amenities)
                                        <div class="flex gap-1 flex-wrap">
                                            @foreach($bus->amenities as $amenity)
                                                <span class="px-1.5 py-0.5 bg-surface-container rounded text-[10px] font-bold text-on-surface-variant">{{ $amenity }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-outline">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    {{ $bus->last_maintenance_date ? $bus->last_maintenance_date->format('d M Y') : '—' }}
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    {{ $bus->mileage_km ? number_format($bus->mileage_km) . ' km' : '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    @if($bus->is_active)
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary">Active</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-error-container/20 text-error">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('operator.buses.show', $bus->id) }}"
                                       class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors inline-block">
                                        <span class="material-symbols-outlined" style="font-size:18px">visibility</span>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="py-16 text-center">
                                <span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">directions_bus</span>
                                <p class="font-medium text-on-surface-variant">No buses in your fleet yet.</p>
                                <button onclick="openDrawer()" class="mt-2 text-sm font-bold text-primary hover:underline">Add your first bus</button>
                            </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-sm text-on-surface-variant">Showing {{ $buses->firstItem() ?? 0 }} - {{ $buses->lastItem() ?? 0 }} of {{ $buses->total() }} buses</span>
                    <div class="flex items-center gap-1">
                        @if($buses->previousPageUrl())
                        <a href="{{ $buses->previousPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_left</span></a>
                        @endif
                        <span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-sm font-bold">{{ $buses->currentPage() }}</span>
                        @if($buses->nextPageUrl())
                        <a href="{{ $buses->nextPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_right</span></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <!-- Drawer Backdrop -->
    <div id="drawer-backdrop" class="drawer-backdrop fixed inset-0 bg-on-surface/30 z-30" onclick="closeDrawer()"></div>

    <!-- Add Bus Drawer -->
    <aside id="add-bus-drawer" class="drawer-panel fixed top-0 right-0 h-full w-[480px] bg-surface-container-lowest border-l border-outline-variant/15 z-40 flex flex-col shadow-xl">
        <div class="px-6 py-5 border-b border-outline-variant/15 flex items-center justify-between shrink-0">
            <div>
                <h3 class="font-headline font-bold text-lg text-on-surface">Add New Bus</h3>
                <p class="text-sm text-on-surface-variant mt-0.5">Register a new vehicle to your fleet</p>
            </div>
            <button onclick="closeDrawer()" class="p-2 rounded-xl hover:bg-surface-container transition-colors text-on-surface-variant">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="{{ route('operator.buses.store') }}" method="POST" class="flex-grow overflow-y-auto custom-scrollbar px-6 py-6 space-y-5">
            @csrf
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Registration Number *</label>
                <input type="text" name="registration_number" required placeholder="e.g. ZTC-8812"
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Model</label>
                <input type="text" name="model" placeholder="e.g. Toyota Hiace, Yutong ZK6118"
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Seat Capacity *</label>
                    <input type="number" name="seat_capacity" required min="1" max="100" placeholder="e.g. 49"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Class *</label>
                    <select name="bus_class" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                        <option value="economy">Economy</option>
                        <option value="business">Business</option>
                        <option value="luxury">Luxury</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Amenities</label>
                <div class="grid grid-cols-2 gap-2">
                    @php $amenitiesList = ['wifi' => 'WiFi', 'ac' => 'Air Conditioning', 'usb_charging' => 'USB Charging', 'entertainment' => 'Entertainment', 'refreshments' => 'Refreshments', 'restroom' => 'Restroom']; @endphp
                    @foreach($amenitiesList as $key => $label)
                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg bg-surface-container-low text-sm cursor-pointer hover:bg-surface-container transition-colors">
                        <input type="checkbox" name="amenities[]" value="{{ $key }}" class="accent-primary rounded">
                        <span>{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Last Maintenance</label>
                    <input type="date" name="last_maintenance_date"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Next Maintenance</label>
                    <input type="date" name="next_maintenance_date"
                           class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Mileage (km)</label>
                <input type="number" name="mileage_km" min="0" placeholder="e.g. 50000"
                       class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold uppercase text-on-surface-variant tracking-wider mb-1.5">Notes</label>
                <textarea name="notes" rows="3" placeholder="Additional notes about this bus..."
                    class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant/30 rounded-xl text-sm focus:ring-2 focus:ring-primary transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-4 border-t border-outline-variant/15">
                <button type="button" onclick="closeDrawer()"
                    class="flex-1 py-3 rounded-xl border border-outline-variant/30 text-sm font-bold text-on-surface-variant hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit"
                    class="flex-[2] py-3 rounded-xl bg-primary text-on-primary text-sm font-bold hover:bg-primary/90 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">add_circle</span> Add Bus
                </button>
            </div>
        </form>
    </aside>

@endsection

@push('scripts')
<script>
        function openDrawer() { document.getElementById('add-bus-drawer').classList.add('open'); document.getElementById('drawer-backdrop').classList.add('open'); }
        function closeDrawer() { document.getElementById('add-bus-drawer').classList.remove('open'); document.getElementById('drawer-backdrop').classList.remove('open'); }
    </script>
@endpush

