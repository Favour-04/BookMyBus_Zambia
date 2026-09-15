<a aria-current="{{ (request()->routeIs('operator.dashboard')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.dashboard')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.dashboard') }}">
    <span class="material-symbols-outlined">dashboard</span><span>Dashboard</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.trips.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.trips.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.trips.index') }}">
    <span class="material-symbols-outlined">directions_bus</span><span>Manage Trips</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.bookings.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.bookings.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.bookings.index') }}">
    <span class="material-symbols-outlined">book_online</span><span>All Bookings</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.revenue')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.revenue')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.revenue') }}">
    <span class="material-symbols-outlined">payments</span><span>Revenue</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.audit-log.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.audit-log.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.audit-log.index') }}">
    <span class="material-symbols-outlined">history</span><span>Audit Log</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.customers.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.customers.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.customers.index') }}">
    <span class="material-symbols-outlined">groups</span><span>Customers</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.buses.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.buses.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.buses.index') }}">
    <span class="material-symbols-outlined">airport_shuttle</span><span>Fleet</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.drivers.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.drivers.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.drivers.index') }}">
    <span class="material-symbols-outlined">badge</span><span>Drivers</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.fare-rules.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.fare-rules.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.fare-rules.index') }}">
    <span class="material-symbols-outlined">sell</span><span>Fare Rules</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.promo-codes.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.promo-codes.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.promo-codes.index') }}">
    <span class="material-symbols-outlined">confirmation_number</span><span>Promo Codes</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.route-templates.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.route-templates.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.route-templates.index') }}">
    <span class="material-symbols-outlined">route</span><span>Route Templates</span>
</a>
<a aria-current="{{ (request()->routeIs('operator.passengers.*')) ? 'page' : 'false' }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ (request()->routeIs('operator.passengers.*')) ? 'text-primary font-bold border-r-4 border-primary bg-surface-container-highest' : 'text-on-surface-variant hover:text-primary hover:bg-surface-container-highest' }} transition-colors" href="{{ route('operator.passengers.index') }}">
    <span class="material-symbols-outlined">fact_check</span><span>Passengers</span>
</a>