<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.dashboard')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.dashboard') }}">
    <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.operators.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.operators.index') }}">
    <span class="material-symbols-outlined">directions_bus</span><span>Operators</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.operators.index') && request('status') === 'pending') ? 'text-tertiary font-bold' : '' }} transition-colors" href="{{ route('admin.operators.index', ['status' => 'pending']) }}" title="Operators awaiting verification">
    <span class="material-symbols-outlined text-sm">verified_user</span><span class="text-sm">Verifications</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.users.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.users.index') }}">
    <span class="material-symbols-outlined">group</span><span>Travelers</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.bookings.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.bookings.index') }}">
    <span class="material-symbols-outlined">book_online</span><span>Bookings</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.trips.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.trips.index') }}">
    <span class="material-symbols-outlined">route</span><span>Trips</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.payments.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.payments.index') }}">
    <span class="material-symbols-outlined">payments</span><span>Payments</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.reports.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.reports.index') }}">
    <span class="material-symbols-outlined">monitoring</span><span>Reports</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.audit-log.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.audit-log.index') }}">
    <span class="material-symbols-outlined">history</span><span>Audit Log</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('admin.profile*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('admin.profile') }}">
    <span class="material-symbols-outlined">settings</span><span>Settings</span>
</a>