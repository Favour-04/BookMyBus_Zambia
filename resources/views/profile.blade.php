<!doctype html>
<html class="light" lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>My Profile — BookMyBus Zambia</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
    rel="stylesheet" />
  <script id="tailwind-config">
    tailwind.config = {
      darkMode: "class",
      theme: {
        extend: {
          colors: {
            "on-secondary": "#ffffff",
            "on-secondary-container": "#632f00",
            "secondary-container": "#ff8921",
            "primary-fixed-dim": "#7edb83",
            "inverse-primary": "#7edb83",
            background: "#f9f9fc",
            "tertiary-container": "#d1200f",
            "surface-container-lowest": "#ffffff",
            "outline-variant": "#bfcaba",
            "on-tertiary": "#ffffff",
            surface: "#f9f9fc",
            "on-primary": "#ffffff",
            "primary-fixed": "#99f89d",
            "surface-variant": "#e2e2e5",
            "surface-bright": "#f9f9fc",
            "surface-dim": "#dadadc",
            outline: "#6f7a6c",
            primary: "#00601f",
            "surface-container-low": "#f3f3f6",
            "on-surface-variant": "#3f493e",
            "surface-container-high": "#e8e8ea",
            "on-surface": "#1a1c1e",
            "primary-container": "#197b30",
            "error-container": "#ffdad6",
            "surface-container-highest": "#e2e2e5",
            error: "#ba1a1a",
            "surface-container": "#eeeef0",
            secondary: "#954a00",
            tertiary: "#a80800",
            "on-error": "#ffffff",
            "on-background": "#1a1c1e",
          },
          fontFamily: {
            headline: ["Manrope"],
            body: ["Inter"],
          },
        },
      },
    };
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24;
      vertical-align: middle;
    }
    .hero-gradient { background: linear-gradient(135deg, #00601f 0%, #197b30 100%); }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
  </style>
</head>

<body class="bg-surface font-body text-on-surface">

  <!-- TopNavBar -->
  <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none">
    <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
      <div class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline">
        <a href="{{ route('home') }}">BookMyBus Zambia</a>
      </div>
      <div class="hidden md:flex items-center gap-8">
        <a class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="{{ route('home') }}">Find Trips</a>
        <a class="font-headline tracking-tight font-bold text-sm text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1"
          href="{{ route('profile') }}">My Account</a>
        <a class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="{{ route('booking.lookup') }}">My Bookings</a>
        <a class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="{{ route('operator.login') }}">Operator Portal</a>
        <a class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="{{ route('support.page') }}">Support</a>
      </div>
      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
          class="text-green-800 dark:text-green-400 font-headline font-bold text-sm px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors rounded-lg flex items-center gap-2">
          <span>Sign Out</span>
          <span class="material-symbols-outlined text-lg">logout</span>
        </button>
      </form>
    </div>
  </nav>

  <main class="pt-28 pb-20 max-w-5xl mx-auto px-6">

    <!-- Status messages -->
    @if(session('status'))
      <div class="mb-6 p-4 rounded-xl bg-primary-fixed/30 border border-primary-container/30 flex items-start gap-3">
        <span class="material-symbols-outlined text-primary text-sm mt-0.5">check_circle</span>
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
      <button onclick="setTab('details', this)" class="tab-btn px-4 py-2 rounded-lg text-sm font-bold bg-primary text-white transition-all">
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
          <button type="submit" class="hero-gradient text-white font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
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
          <button type="submit" class="hero-gradient text-white font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
            <span class="material-symbols-outlined text-lg">lock_reset</span>
            Update Password
          </button>
        </div>
      </form>
    </div>

    <!-- Booking History Tab -->
    <div id="tab-bookings" class="tab-panel">
      <div class="bg-surface-container-lowest rounded-2xl border border-outline-variant/15 editorial-shadow overflow-hidden">

        @forelse($bookings as $booking)
          <div class="flex items-center justify-between px-6 py-5 border-b border-outline-variant/10 last:border-0 hover:bg-surface-container-low transition-colors">
            <div class="flex items-center gap-4">
              <div class="h-12 w-12 rounded-xl bg-primary-fixed/40 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-primary">directions_bus</span>
              </div>
              <div>
                <p class="font-headline font-bold text-on-surface">
                  {{ $booking->route->origin ?? 'N/A' }} → {{ $booking->route->destination ?? 'N/A' }}
                </p>
                <p class="text-on-surface-variant text-xs font-body mt-1">
                  Ref: {{ $booking->reference_id }} · Seat {{ $booking->seat_number }} ·
                  {{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y') }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-4">
              <span class="text-sm font-bold text-on-surface">ZMW {{ number_format($booking->amount, 2) }}</span>
              @php
                $statusStyles = [
                  'confirmed' => 'bg-primary-fixed/40 text-on-primary-fixed-variant',
                  'pending'   => 'bg-secondary-container/30 text-on-secondary-container',
                  'cancelled' => 'bg-error-container text-error',
                ];
              @endphp
              <span class="text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full {{ $statusStyles[$booking->status] ?? 'bg-surface-container text-on-surface-variant' }}">
                {{ $booking->status }}
              </span>
            </div>
          </div>
        @empty
          <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
            <span class="material-symbols-outlined text-5xl text-outline mb-3">confirmation_number</span>
            <p class="font-headline font-bold text-on-surface mb-1">No bookings yet</p>
            <p class="text-on-surface-variant text-sm font-body mb-5">Your trip history will show up here once you book a seat.</p>
            <a href="{{ route('home') }}" class="hero-gradient text-white font-headline font-bold text-sm px-6 py-3 rounded-xl hover:brightness-110 transition-all">
              Find a Trip
            </a>
          </div>
        @endforelse
      </div>
    </div>

  </main>

  <script>
    function setTab(tab, btn) {
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      document.getElementById('tab-' + tab).classList.add('active');

      document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('bg-primary', 'text-white', 'font-bold');
        b.classList.add('text-on-surface-variant', 'font-medium');
      });
      btn.classList.add('bg-primary', 'text-white', 'font-bold');
      btn.classList.remove('text-on-surface-variant', 'font-medium');
    }

    @if($errors->has('current_password') || ($errors->has('password') && !$errors->has('full_name')))
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.tab-btn')[1].click();
      });
    @endif
  </script>
</body>
</html>
