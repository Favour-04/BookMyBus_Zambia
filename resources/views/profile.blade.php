@extends('layouts.app')

@section('title', 'My Profile — BookMyBus Zambia')
@section('main-class', 'pb-20 max-w-5xl mx-auto px-6')

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-container) 100%);
    }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

        /* SVG icon color — theme-aware. SVGs using stroke="currentColor" or
           fill="currentColor" inherit this automatically. */
        .icon {
            color: var(--color-on-surface);
            transition: color 0.2s ease;
        }
        .icon-primary { color: var(--color-primary); }
        .icon-secondary { color: var(--color-secondary); }
        .icon-muted { color: var(--color-on-surface-variant); }
        .icon-error { color: var(--color-error); }
        .icon-on-primary { color: var(--color-on-primary); }
</style>
@endpush

@section('content')
<!-- Status messages -->
@if(session('status'))
  <div class="mb-6 p-4 rounded-xl bg-primary-fixed/30 border border-primary-container/30 flex items-start gap-3">
    <span class="material-symbols-outlined text-on-primary-fixed-variant text-sm mt-0.5">check_circle</span>
    <p class="text-on-surface text-sm font-body">{{ session('status') }}</p>
  </div>
@endif

@if($errors->any())
  <div class="mb-6 p-4 rounded-xl bg-error-container border border-error/20 flex items-start gap-3">
    <span class="material-symbols-outlined text-error text-sm mt-0.5">error</span>
    <div>
      @foreach($errors->all() as $error)
        <p class="text-error text-sm font-body">{{ $error }}</p>
      @endforeach
    </div>
  </div>
@endif

<!-- Profile Header -->
<div class="flex items-center gap-5 mb-8">
  <div class="h-20 w-20 rounded-full bg-primary-container flex items-center justify-center flex-shrink-0">
    <span class="material-symbols-outlined text-white text-4xl">person</span>
  </div>
  <div>
    <h1 class="font-headline text-3xl font-extrabold tracking-tight text-on-surface">{{ $user->full_name }}</h1>
    <p class="text-on-surface-variant text-sm font-body mt-1">{{ $user->email }}</p>
  </div>
</div>

<!-- Tabs -->
<div class="flex items-center gap-2 bg-surface-container-lowest border border-outline-variant/15 rounded-xl p-1 mb-8 w-fit editorial-shadow">
  <button onclick="setTab('details', this)" class="tab-btn px-4 py-2 rounded-lg text-sm font-bold bg-primary text-on-primary transition-all">
    Account Details
  </button>
  <button onclick="setTab('password', this)" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
    Change Password
  </button>
  <button onclick="setTab('bookings', this)" class="tab-btn px-4 py-2 rounded-lg text-sm font-medium text-on-surface-variant hover:bg-surface-container-low transition-all">
    Booking History
  </button>
</div>

<!-- Account Details Tab -->
<div id="tab-details" class="tab-panel active">
  <form action="{{ route('profile.update') }}" method="POST" class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 editorial-shadow max-w-xl">
    @csrf
    @method('PUT')

    <h3 class="font-headline text-xl font-bold mb-5">Personal Information</h3>

    <div class="flex flex-col gap-5">
      <div>
        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Full Name</label>
        <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
      </div>

      <div>
        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Email Address</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
      </div>

      <div>
        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Phone Number</label>
        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required
          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
      </div>

      <div>
        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Preferred Language</label>
        <select name="preferred_language"
          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors">
          <option value="en" @selected(old('preferred_language', $user->preferred_language) === 'en')>English</option>
          <option value="ny" @selected(old('preferred_language', $user->preferred_language) === 'ny')>Nyanja</option>
          <option value="be" @selected(old('preferred_language', $user->preferred_language) === 'be')>Bemba</option>
        </select>
      </div>
    </div>

    <div class="flex justify-end mt-6">
      <button type="submit" class="hero-gradient text-on-primary font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">save</span>
        Save Changes
      </button>
    </div>
  </form>
</div>

<!-- Change Password Tab -->
<div id="tab-password" class="tab-panel">
  <form action="{{ route('profile.password') }}" method="POST" class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant/15 editorial-shadow max-w-xl">
    @csrf
    @method('PUT')

    <h3 class="font-headline text-xl font-bold mb-5">Change Password</h3>

    <div class="flex flex-col gap-5">
      <div>
        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Current Password</label>
        <input type="password" name="current_password" required
          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
      </div>

      <div>
        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">New Password</label>
        <input type="password" name="password" required
          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
      </div>

      <div>
        <label class="block text-[10px] uppercase font-bold text-outline tracking-widest mb-2">Confirm New Password</label>
        <input type="password" name="password_confirmation" required
          class="w-full px-4 py-3 bg-surface-container-low border border-outline-variant rounded-lg text-on-surface text-sm focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-colors" />
      </div>
    </div>

    <div class="flex justify-end mt-6">
      <button type="submit" class="hero-gradient text-on-primary font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
        <span class="material-symbols-outlined text-lg">lock_reset</span>
        Update Password
      </button>
    </div>
  </form>
</div>

<!-- Booking History Tab -->
<div id="tab-bookings" class="tab-panel">
  <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 editorial-shadow overflow-hidden">

    @php
      $statusStyles = [
        'confirmed' => 'bg-primary-fixed/40 text-on-primary-fixed-variant',
        'pending'   => 'bg-secondary-container/30 text-on-secondary-container',
        'cancelled' => 'bg-error-container text-error',
      ];
    @endphp

    @forelse($trips as $trip)
      @php
        $primary = $trip->primary;
        $route = $primary->route;
        $departure = $route
            ? \Carbon\Carbon::parse($route->travel_date)->setTimeFromTimeString((string) $route->departure_time)
            : now();
        $tripNotDeparted = now()->lt($departure);
      @endphp
      <div class="border-b border-outline-variant/10 last:border-0">
        <div class="flex items-center justify-between px-6 py-5 hover:bg-surface-container-low transition-colors">
          <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-xl bg-primary-fixed/40 flex items-center justify-center flex-shrink-0">
              <span class="material-symbols-outlined text-on-primary-fixed-variant">directions_bus</span>
            </div>
            <div>
              <p class="font-headline font-bold text-on-surface">
                {{ $route->origin ?? 'N/A' }} → {{ $route->destination ?? 'N/A' }}
              </p>
              <p class="text-on-surface-variant text-xs font-body mt-1">
                Ref: {{ $primary->reference_id }} ·
                {{ $trip->is_group ? count($trip->seat_numbers) . ' seats (' . implode(', ', $trip->seat_numbers) . ')' : 'Seat ' . $primary->seat_number }} ·
                {{ \Carbon\Carbon::parse($trip->created_at)->format('d M Y') }}
              </p>
            </div>
          </div>
          <div class="flex items-center gap-4">
            <span class="text-sm font-bold text-on-surface">ZMW {{ number_format($trip->total_amount, 2) }}</span>
            <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full {{ $statusStyles[$primary->status] ?? 'bg-surface-container text-on-surface-variant' }}">
              {{ $primary->status }}
            </span>
            @if(in_array($primary->status, ['pending', 'confirmed'], true))
            <a href="{{ route('booking.success', $primary->id) }}"
               class="px-3 py-1.5 rounded-lg border border-primary/30 text-primary text-xs font-bold uppercase tracking-wider hover:bg-primary-fixed/20 transition-colors flex items-center gap-1">
              <span class="material-symbols-outlined text-sm">confirmation_number</span>
              View Ticket
            </a>
            @endif
          </div>
        </div>

        @if($trip->is_group)
        <div class="pl-20 pr-6 pb-4 space-y-2">
          @foreach($trip->bookings as $seatBooking)
            @php
              $seatCanCancel = in_array($seatBooking->status, ['pending', 'confirmed'], true) && $tripNotDeparted;
            @endphp
            <div class="flex items-center justify-between text-xs bg-surface-container-low/60 rounded-lg px-4 py-2.5">
              <span class="font-medium text-on-surface-variant">
                Seat {{ $seatBooking->seat_number }} — {{ $seatBooking->passenger_name ?? 'N/A' }}
              </span>
              <div class="flex items-center gap-3">
                <span class="font-bold uppercase tracking-wider px-2 py-1 rounded-full text-[10px] {{ $statusStyles[$seatBooking->status] ?? 'bg-surface-container text-on-surface-variant' }}">
                  {{ $seatBooking->status }}
                </span>
                @if($seatCanCancel)
                <form method="POST" action="{{ route('bookings.cancel', $seatBooking) }}"
                      onsubmit="return confirm('Cancel seat {{ $seatBooking->seat_number }}?{{ $seatBooking->status === 'confirmed' ? ' A refund will be calculated based on the operator cancellation policy.' : '' }}');">
                  @csrf
                  <button type="submit" class="text-error font-bold uppercase tracking-wider hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">cancel</span>
                    Cancel
                  </button>
                </form>
                @endif
              </div>
            </div>
          @endforeach
        </div>
        @elseif(in_array($primary->status, ['pending', 'confirmed'], true) && $tripNotDeparted)
        <div class="pl-20 pr-6 pb-4">
          <form method="POST" action="{{ route('bookings.cancel', $primary) }}"
                onsubmit="return confirm('Cancel this booking?{{ $primary->status === 'confirmed' ? ' A refund will be calculated based on the operator cancellation policy.' : '' }}');">
            @csrf
            <button type="submit" class="px-3 py-1.5 rounded-lg border border-error/30 text-error text-xs font-bold uppercase tracking-wider hover:bg-error-container/40 transition-colors flex items-center gap-1">
              <span class="material-symbols-outlined text-sm">cancel</span>
              Cancel Booking
            </button>
          </form>
        </div>
        @endif
      </div>
    @empty
      <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
        <span class="material-symbols-outlined text-5xl text-outline mb-3">confirmation_number</span>
        <p class="font-headline font-bold text-on-surface mb-1">No bookings yet</p>
        <p class="text-on-surface-variant text-sm font-body mb-5">Your trip history will show up here once you book a seat.</p>
        <a href="{{ route('home') }}" class="hero-gradient text-on-primary font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 transition-all">
          Find a Trip
        </a>
      </div>
    @endforelse
  </div>

  @if($trips->hasPages())
  <div class="mt-6">
    {{ $trips->links() }}
  </div>
  @endif
</div>
@endsection

@push('scripts')
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

  @if($errors->has('current_password') || ($errors->has('password') && !$errors->has('full_name')))
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.tab-btn')[1].click();
    });
  @endif

  @if(session('active_tab') === 'bookings')
    document.addEventListener('DOMContentLoaded', () => {
      document.querySelectorAll('.tab-btn')[2].click();
    });
  @endif
</script>
@endpush