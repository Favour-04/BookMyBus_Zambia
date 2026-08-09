<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drivers | BookMyBus Zambia</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=Manrope:wght@100..900&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,200..0&display=swap" rel="stylesheet" />
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; vertical-align: middle; }
        body { font-family: 'Inter', sans-serif; background-color: #f9f9fc; }
        #driver-drawer { transform: translateX(100%); transition: transform 0.25s cubic-bezier(0.4,0,0.2,1); }
        #driver-drawer.open { transform: translateX(0); }
        #drawer-backdrop { opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
        #drawer-backdrop.open { opacity: 1; pointer-events: auto; }
    </style>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: { extend: { colors: { "primary": "#004614", "on-primary": "#ffffff", "surface-container-low": "#f3f3f6", "surface-container": "#edeef1", "surface-container-highest": "#e2e2e5", "surface-container-lowest": "#ffffff", "surface": "#f9f9fc", "on-surface": "#1a1c1e", "on-surface-variant": "#40493e", "outline-variant": "#bfcaba", "outline": "#6f7a6c", "tertiary": "#7c0400", "error-container": "#ffdad6", "error": "#ba1a1a", }, fontFamily: { 'headline': ['Manrope', 'sans-serif'], 'body': ['Inter', 'sans-serif'] } } }
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
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-primary font-bold border-r-4 border-primary bg-surface-container-highest" href="{{ route('operator.drivers.index') }}"><span class="material-symbols-outlined">badge</span><span>Drivers</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.buses.index') }}"><span class="material-symbols-outlined">directions_bus</span><span>Fleet</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.bookings.index') }}"><span class="material-symbols-outlined">book_online</span><span>Bookings</span></a>
            <a class="flex items-center gap-3 px-4 py-3 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container-highest transition-colors" href="{{ route('operator.revenue') }}"><span class="material-symbols-outlined">payments</span><span>Revenue</span></a>
        </nav>
        <div class="mt-auto pt-6 border-t border-outline-variant/20">
            <a class="flex items-center gap-3 px-4 py-2 rounded-lg text-on-surface-variant hover:text-primary transition-colors" href="{{ route('operator.profile') }}"><span class="material-symbols-outlined">settings</span><span>Settings</span></a>
        </div>
    </aside>
    <main class="ml-64 min-h-screen">
        <header class="h-16 px-8 flex items-center justify-between sticky top-0 bg-surface-container-low border-b border-outline-variant/15 z-10">
            <h2 class="font-headline text-base font-bold text-primary">Driver Management</h2>
            <button onclick="openDrawer()" class="flex items-center gap-2 px-4 py-2 bg-primary text-on-primary rounded-xl text-sm font-bold hover:brightness-110 transition-all"><span class="material-symbols-outlined text-sm">add</span> Add Driver</button>
        </header>
        <div class="p-8">
            @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-primary/10 border border-primary/20 flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
                    <p class="text-on-surface text-sm font-medium">{{ session('status') }}</p>
                </div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-error-container/30 border border-error/20 flex items-start gap-3">
                    <span class="material-symbols-outlined text-error text-sm mt-0.5">error</span>
                    <div>@foreach($errors->all() as $error)<p class="text-error text-sm">{{ $error }}</p>@endforeach</div>
                </div>
            @endif
            <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-surface-container-low border-b border-outline-variant/15">
                            <tr>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Name</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Phone</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">License #</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">License Expiry</th>
                                <th class="px-5 py-4 text-xs font-bold uppercase text-on-surface-variant">Status</th>
                                <th class="px-5 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline-variant/10">
                            @forelse($drivers as $driver)
                            <tr class="hover:bg-surface-container-low/60 transition-colors">
                                <td class="px-5 py-4"><span class="font-medium">{{ $driver->full_name }}</span></td>
                                <td class="px-5 py-4 text-on-surface-variant">{{ $driver->phone_number }}</td>
                                <td class="px-5 py-4 font-mono text-sm">{{ $driver->license_number }}</td>
                                <td class="px-5 py-4 text-sm">
                                    @if($driver->license_expiry_date)
                                        <span class="{{ $driver->license_expiry_date->isPast() ? 'text-error' : 'text-on-surface-variant' }}">{{ $driver->license_expiry_date->format('d M Y') }}</span>
                                    @else<span class="text-on-surface-variant">—</span>@endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $driver->is_active ? 'bg-primary/10 text-primary' : 'bg-surface-container-high text-on-surface-variant' }}">
                                        {{ $driver->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <button onclick="editDriver({{ $driver->id }})" class="p-1.5 rounded-lg text-on-surface-variant hover:text-primary hover:bg-surface-container transition-colors" title="Edit">
                                        <span class="material-symbols-outlined" style="font-size:18px">edit</span>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="py-16 text-center"><span class="material-symbols-outlined text-5xl text-outline-variant block mb-3">badge</span><p class="font-medium text-on-surface-variant">No drivers added yet.</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 border-t border-outline-variant/10 flex items-center justify-between">
                    <span class="text-sm text-on-surface-variant">{{ $drivers->total() }} driver(s)</span>
                    <div class="flex items-center gap-1">@if($drivers->previousPageUrl())<a href="{{ $drivers->previousPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_left</span></a>@endif<span class="px-3 py-1 rounded-lg bg-primary text-on-primary text-sm font-bold">{{ $drivers->currentPage() }}</span>@if($drivers->nextPageUrl())<a href="{{ $drivers->nextPageUrl() }}" class="p-1.5 rounded-lg text-on-surface-variant hover:bg-surface-container transition-colors"><span class="material-symbols-outlined" style="font-size:18px">chevron_right</span></a>@endif</div>
                </div>
            </div>
        </div>
    </main>

    <!-- Drawer Backdrop -->
    <div id="drawer-backdrop" class="fixed inset-0 bg-black/20 z-30" onclick="closeDrawer()"></div>
    <!-- Add/Edit Driver Drawer -->
    <div id="driver-drawer" class="fixed top-0 right-0 h-full w-full max-w-lg bg-surface-container-lowest shadow-2xl z-40 overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-headline text-lg font-bold" id="drawer-title">Add Driver</h3>
                <button onclick="closeDrawer()" class="p-1 rounded-lg hover:bg-surface-container transition-colors"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form id="driver-form" method="POST" action="{{ route('operator.drivers.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <input type="hidden" name="driver_id" id="driver-id" value="">
                <div class="space-y-4">
                    <div><label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Full Name</label><input type="text" name="full_name" id="field-full_name" required class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></div>
                    <div><label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Phone Number</label><input type="text" name="phone_number" id="field-phone_number" required class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></div>
                    <div><label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email</label><input type="email" name="email" id="field-email" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></div>
                    <div><label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">License Number</label><input type="text" name="license_number" id="field-license_number" required class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></div>
                    <div><label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">License Expiry Date</label><input type="date" name="license_expiry_date" id="field-license_expiry_date" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></div>
                    <div><label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Address</label><input type="text" name="address" id="field-address" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></div>
                    <div><label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Notes</label><textarea name="notes" id="field-notes" rows="2" class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary"></textarea></div>
                    <div class="flex items-center gap-2" id="active-toggle-wrapper" style="display:none"><input type="checkbox" name="is_active" id="field-is_active" value="1" checked class="rounded border-outline-variant text-primary focus:ring-primary"><label class="text-sm font-medium">Active</label></div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeDrawer()" class="px-4 py-2 border border-outline-variant rounded-xl text-sm font-medium text-on-surface-variant hover:bg-surface-container">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-on-primary rounded-xl text-sm font-bold hover:brightness-110">Save Driver</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDrawer() { document.getElementById('drawer-title').textContent = 'Add Driver'; document.getElementById('driver-form').action = '{{ route("operator.drivers.store") }}'; document.getElementById('form-method').value = 'POST'; document.getElementById('active-toggle-wrapper').style.display = 'none'; document.getElementById('driver-id').value = ''; ['full_name','phone_number','email','license_number','license_expiry_date','address','notes'].forEach(f => document.getElementById('field-'+f).value = ''); document.getElementById('driver-drawer').classList.add('open'); document.getElementById('drawer-backdrop').classList.add('open'); }
        function closeDrawer() { document.getElementById('driver-drawer').classList.remove('open'); document.getElementById('drawer-backdrop').classList.remove('open'); }
        function editDriver(id) {
            document.getElementById('drawer-title').textContent = 'Edit Driver';
            document.getElementById('driver-form').action = '{{ route("operator.drivers.update", "") }}/' + id;
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('driver-id').value = id;
            document.getElementById('active-toggle-wrapper').style.display = 'flex';
            fetch('/operator/drivers/' + id + '/json').then(r => r.json()).then(d => {
                if(d.success) { const dr = d.driver; ['full_name','phone_number','email','license_number','license_expiry_date','address','notes'].forEach(f => { const el = document.getElementById('field-'+f); if(el) el.value = dr[f] || ''; }); document.getElementById('field-is_active').checked = dr.is_active; }
            });
            document.getElementById('driver-drawer').classList.add('open');
            document.getElementById('drawer-backdrop').classList.add('open');
        }
    </script>
</body>
</html>