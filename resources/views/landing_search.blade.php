<!-- TODO: This view should extend layouts.app when a layout is available -->

<!doctype html>

<html class="light" lang="en">

<head>
  <meta charset="utf-8" />
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <title>BookMyBus Zambia - Premium Travel Excellence</title>
  <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
    rel="stylesheet" />
  <script id="tailwind-config">
    tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "on-secondary": "#ffffff",
              "on-secondary-container": "#632f00",
              "on-secondary-fixed": "#301400",
              "inverse-surface": "#2f3133",
              "on-primary-container": "#b1ffb1",
              "secondary-container": "#ff8921",
              "primary-fixed-dim": "#7edb83",
              "inverse-primary": "#7edb83",
              background: "#f9f9fc",
              "inverse-on-surface": "#f0f0f3",
              "tertiary-container": "#d1200f",
              "secondary-fixed": "#ffdcc6",
              "surface-container-lowest": "#ffffff",
              "outline-variant": "#bfcaba",
              "on-tertiary": "#ffffff",
              surface: "#f9f9fc",
              "on-primary": "#ffffff",
              "primary-fixed": "#99f89d",
              "surface-variant": "#e2e2e5",
              "secondary-fixed-dim": "#ffb784",
              "on-primary-fixed": "#002106",
              "surface-bright": "#f9f9fc",
              "on-error-container": "#93000a",
              "surface-dim": "#dadadc",
              outline: "#6f7a6c",
              "on-secondary-fixed-variant": "#713700",
              primary: "#00601f",
              "on-primary-fixed-variant": "#00531a",
              "surface-container-low": "#f3f3f6",
              "on-surface-variant": "#3f493e",
              "surface-container-high": "#e8e8ea",
              "on-surface": "#1a1c1e",
              "primary-container": "#197b30",
              "error-container": "#ffdad6",
              "tertiary-fixed": "#ffdad4",
              "on-tertiary-fixed": "#400100",
              "tertiary-fixed-dim": "#ffb4a7",
              "surface-container-highest": "#e2e2e5",
              "on-tertiary-container": "#ffe7e3",
              "on-tertiary-fixed-variant": "#920600",
              "surface-tint": "#006e25",
              error: "#ba1a1a",
              "surface-container": "#eeeef0",
              secondary: "#954a00",
              tertiary: "#a80800",
              "on-error": "#ffffff",
              "on-background": "#1a1c1e",
            },
            borderRadius: {
              DEFAULT: "0.125rem",
              lg: "0.25rem",
              xl: "0.5rem",
              full: "0.75rem",
            },
            fontFamily: {
              headline: ["Manrope"],
              body: ["Inter"],
              label: ["Inter"],
            },
          },
        },
      };
      function decrementPassengers(event) {
        event.preventDefault(); // Prevents clicking the minus button from accidentally submitting the form
        const input = document.getElementById('passenger-count');
        let currentValue = parseInt(input.value) || 1;
        if (currentValue > 1) {
          input.value = currentValue - 1;
        }
      }

      function incrementPassengers(event) {
        event.preventDefault(); // Prevents clicking the plus button from accidentally submitting the form
        const input = document.getElementById('passenger-count');
        let currentValue = parseInt(input.value) || 1;
        if (currentValue < 5) {
          input.value = currentValue + 1;
        }
      }
  </script>
  <style>
    .material-symbols-outlined {
      font-variation-settings:
        "FILL" 0,
        "wght" 400,
        "GRAD" 0,
        "opsz" 24;
    }

    .hero-gradient {
      background: linear-gradient(135deg, #00601f 0%, #197b30 100%);
    }

    .editorial-shadow {
      box-shadow: 0 24px 40px rgba(26, 28, 30, 0.05);
    }

    input[type="date"]::-webkit-calendar-picker-indicator {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      background: transparent;
      color: transparent;
      cursor: pointer;
    }

    .search-icon {
      height: 24px;
      width: 24px;
      stroke: rgb(25 123 48 / var(--tw-bg-opacity, 1));
    }
  </style>
</head>

<body class="bg-surface font-body text-on-surface">
  <!-- TopNavBar -->
  <nav class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none">
    <div class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full">
      <div class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline">
        <a href="#">BookMyBus Zambia</a>
      </div>
      <div class="hidden md:flex items-center gap-8">
        <a class="font-headline tracking-tight font-bold text-sm text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1"
          href="#">Find Trips</a>
        <a class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="#">My Bookings</a>
        <a class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="#">Operator Portal</a>
        <a class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
          href="#">Support</a>
      </div>
        <button
          class="text-green-800 dark:text-green-400 font-headline font-bold text-sm px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors rounded-lg flex items-center gap-2">
          <span>Sign In</span>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
          class="search-icon">
          <path stroke-linecap="round" stroke-linejoin="round"
            d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
        </svg>
        </button>
        
    </div>
  </nav>
  <main class="pt-20">
    <!-- Hero Section -->
    <section
      style="position: relative; width: 100%; min-height: 650px; display: flex; align-items: center; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuDk0Dlz3kz8govksGNNICUM9-j8vL7Yon5vj-d_62Af9GeHSg1Xug_R0TlK9veZpFbgC_oejPsCpAQr0l8wvuUN_iOXbwgj4WRvh1fVY3FX3BZoWNw2sz9d4mW33rERvAfkJ_63pxYzhVQB46HITvif6J4bFr2yh4PYyGlUt3Kw4J9BJ7JhORkiTuUg1gplwcGy3laWI4Uvvnd6t1tnX1V9GktQ9QqNQVifCYfXCi60Wi4uTGLJ96VPI73U0OijKpre_xvwA1JOg7PX'); background-size: cover; background-position: center; overflow: hidden; padding-top: 5rem; padding-bottom: 5rem;">
      {{-- <section
        style="position: relative; width: 100%; min-height: 600px; display: flex; align-items: center; background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://lh3.googleusercontent.com/aida-public/AB6AXuDk0Dlz3kz8govksGNNICUM9-j8vL7Yon5vj-d_62Af9GeHSg1Xug_R0TlK9veZpFbgC_oejPsCpAQr0l8wvuUN_iOXbwgj4WRvh1fVY3FX3BZoWNw2sz9d4mW33rERvAfkJ_63pxYzhVQB46HITvif6J4bFr2yh4PYyGlUt3Kw4J9BJ7JhORkiTuUg1gplwcGy3laWI4Uvvnd6t1tnX1V9GktQ9QqNQVifCYfXCi60Wi4uTGLJ96VPI73U0OijKpre_xvwA1JOg7PX'); background-size: cover; background-position: center; overflow: hidden;">
        --}}
        {{-- <section
          style="position: relative; width: 100%; min-height: 550px; background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDk0Dlz3kz8govksGNNICUM9-j8vL7Yon5vj-d_62Af9GeHSg1Xug_R0TlK9veZpFbgC_oejPsCpAQr0l8wvuUN_iOXbwgj4WRvh1fVY3FX3BZoWNw2sz9d4mW33rERvAfkJ_63pxYzhVQB46HITvif6J4bFr2yh4PYyGlUt3Kw4J9BJ7JhORkiTuUg1gplwcGy3laWI4Uvvnd6t1tnX1V9GktQ9QqNQVifCYfXCi60Wi4uTGLJ96VPI73U0OijKpre_xvwA1JOg7PX'); background-size: cover; background-position: center; background-color: #000; overflow: hidden;">
          --}}
          {{-- <div
            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;">
            <img
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuDk0Dlz3kz8govksGNNICUM9-j8vL7Yon5vj-d_62Af9GeHSg1Xug_R0TlK9veZpFbgC_oejPsCpAQr0l8wvuUN_iOXbwgj4WRvh1fVY3FX3BZoWNw2sz9d4mW33rERvAfkJ_63pxYzhVQB46HITvif6J4bFr2yh4PYyGlUt3Kw4J9BJ7JhORkiTuUg1gplwcGy3laWI4Uvvnd6t1tnX1V9GktQ9QqNQVifCYfXCi60Wi4uTGLJ96VPI73U0OijKpre_xvwA1JOg7PX"
              style="width: 100%; height: 100%; object-fit: cover; opacity: 0.6; display: block;">
          </div> --}}
          <div style="position: relative; z-index: 10; width: 100%;"> {{-- <div
              style="position: relative; z-index: 10; width: 100%; min-height: 550px; display: flex; flex-direction: column; justify-content: center;">
              --}}
              <div class="max-w-7xl mx-auto w-full px-6">
                <div class="max-w-3xl mb-12">
                  <h1
                    class="font-headline text-5xl md:text-7xl font-extrabold text-white tracking-tighter leading-none mb-6">
                    Travel Zambia with
                    <span class="text-secondary-container">Confidence.</span>
                  </h1>
                  <p class="text-xl text-white/90 max-w-xl font-body">
                    Experience the gold standard in bus travel. Secure your seat on
                    premium carriers across the nation with effortless mobile
                    payments.
                  </p>
                </div>
                <!-- Search Bar Card -->
                <form action="{{ route('trips.search') }}" method="GET"
                  class="bg-surface-container-lowest p-2 rounded-xl editorial-shadow w-full max-w-5xl">
                  <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
                    <div
                      class="md:col-span-3 p-4 hover:bg-surface-container-low transition-colors rounded-lg cursor-pointer group">
                      <label class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1">From</label>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="search-icon">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>

                        <input
                          class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                          placeholder="Lusaka" required type="text" name="from" value="{{ request('from') }}" />
                      </div>
                    </div>
                    <div
                      class="md:col-span-3 p-4 hover:bg-surface-container-low  transition-colors rounded-lg cursor-pointer group">
                      <label class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1">To</label>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="search-icon">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z" />
                        </svg>

                        <input
                          class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                          placeholder="Kitwe" required type="text" name="to" value="{{ request('to') }}" />
                      </div>
                    </div>
                    <div
                      class="relative md:col-span-2 p-4 hover:bg-surface-container-low transition-colors rounded-lg cursor-pointer group">
                      <label class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1">Date</label>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="search-icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                      </svg>
                        <input
                          class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                          placeholder="{{\Carbon\Carbon::now() -> format('Y-m-d')}}" type="date" required
                          value="{{ request('date', \Carbon\Carbon::now() -> format('Y-m-d'))}}"
                          min="{{ \Carbon\Carbon::now() -> format('Y-m-d')}}"
                          max="{{ \Carbon\Carbon::now() -> addDays(30) -> format('Y-m-d')}}" />
                      </div>
                    </div>
                    {{-- <div
                      class="md:col-span-2 p-4 hover:bg-surface-container-low transition-colors rounded-lg cursor-pointer group">
                      <span
                        class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1">Passengers</span>
                      <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="search-icon">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                        </svg>

                        <input
                          class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                          placeholder="1 Adult" type="text" />
                      </div>
                    </div> --}}
                    <div
                      class="md:col-span-2 p-4 hover:bg-surface-container-low transition-colors rounded-lg group select-none">
                      <label class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1"> Passengers
                      </label>
                      <div class="flex items-center justify-between gap-1">
                        <div class="flex items-center gap-2 ">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="search-icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                          </svg>
                          <input id="passenger-count"
                            class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-9 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            type="number" name="passengers" value="{{ request('passengers', 1) }}" min="1" max="5"
                            readonly />
                        </div>

                        <div class="flex items-center gap-1 bg-surface-container rounded-lg p-0.5 z-20">
                          <button type="button" onclick="decrementPassengers(event)"
                            class="w-7 h-7 flex items-center justify-center text-sm font-bold text-on-surface rounded hover:bg-surface-container-high active:scale-90 transition-all">
                            &minus;
                          </button>
                          <button type="button" onclick="incrementPassengers(event)"
                            class="w-7 h-7 flex items-center justify-center text-sm font-bold text-on-surface rounded hover:bg-surface-container-high active:scale-90 transition-all">
                            +
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="md:col-span-2 p-2">
                      <button
                        class="w-full h-full hero-gradient text-white font-headline font-extrabold rounded-lg hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 py-4 md:py-0">
                        <span>Find Trips</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                          stroke="currentColor" class="size-6" height="24px" width="24px">
                          <path stroke-linecap="round" stroke-linejoin="round"
                            d="M17.25 8.25 21 12m0 0-3.75 3.75M21 12H3" />
                        </svg>

                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
        </section>
        <!-- Why Choose Us - Bento Grid Pattern -->
        <section class="py-24 px-6 max-w-7xl mx-auto">
          <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
            <div class="max-w-2xl">
              <span class="text-secondary font-bold tracking-widest text-xs uppercase">The Difference</span>
              <h2 class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2">
                Redefining the Journey
              </h2>
            </div>
            <p class="text-on-surface-variant max-w-xs font-body italic border-l-2 border-primary-container pl-4">
              "Setting the benchmark for transit technology in Central Africa."
            </p>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
              class="md:col-span-2 bg-surface-container-low rounded-2xl p-10 flex flex-col justify-between min-h-[320px]">
              <div class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center mb-6">
                <span class="material-symbols-outlined text-on-primary-container text-3xl">shield</span>
              </div>
              <div>
                <h3 class="font-headline text-2xl font-bold mb-3">
                  Secure Transactions
                </h3>
                <p class="text-on-surface-variant max-w-md">
                  Your security is our priority. We use end-to-end encryption for
                  every booking and partner only with vetted operators.
                </p>
              </div>
            </div>
            <div
              class="bg-secondary-container rounded-2xl p-10 text-on-secondary-container flex flex-col justify-between">
              <span class="material-symbols-outlined text-5xl">speed</span>
              <div>
                <h3 class="font-headline text-2xl font-bold mb-3">
                  Lightning Fast
                </h3>
                <p class="opacity-90">
                  Book your entire cross-country trip in under 60 seconds. No
                  queues, no hassle, just travel.
                </p>
              </div>
            </div>
            <div class="bg-primary text-white rounded-2xl p-10 flex flex-col justify-between">
              <span class="material-symbols-outlined text-5xl">smartphone</span>
              <div>
                <h3 class="font-headline text-2xl font-bold mb-3">
                  Mobile Money Ready
                </h3>
                <p class="text-primary-fixed">
                  Direct integration with Airtel and MTN. Pay directly from your
                  phone without needing a credit card.
                </p>
              </div>
            </div>
            <div
              class="md:col-span-2 bg-surface-container-high rounded-2xl p-10 flex flex-col md:flex-row gap-8 items-center overflow-hidden">
              <div class="flex-1">
                <h3 class="font-headline text-2xl font-bold mb-3">
                  24/7 Premium Support
                </h3>
                <p class="text-on-surface-variant mb-6">
                  Our dedicated team is always on standby to assist with
                  rebookings or travel queries.
                </p>
                <button
                  class="bg-surface-container-lowest px-6 py-2 rounded-full font-bold text-sm shadow-sm hover:translate-y-[-2px] transition-transform">
                  Speak to us
                </button>
              </div>
              <div class="flex-1 -mb-20 -mr-10">
                <img class="rounded-xl w-full h-48 object-cover grayscale brightness-110"
                  data-alt="friendly customer support professional smiling with a headset in a modern bright office environment"
                  src="https://lh3.googleusercontent.com/aida-public/AB6AXuD9dzPzJm2Ho4o7j_qsVncE-5BSIQyLPaHIeIyb5JRDGzb7QJ7PVIDRT6rIGuyYuXzoF7TlO9Ar5PJLJFV4erlBpM_lxrJe82vTm76iUEDCkmSTp0pZlfjUQOObNoZ3gf53ym5F5NJysCeZiG7-RazyOLqKuDbMYKehvNxJu9uGcluSNHndqg4MMGwHbdj5LWhFn7zDZVpTA4a0T3KvxlpCv4msUJIvyyY7K3ltQG5T-CjaZzlejB3dCGe77-rH4xzxheP82wziqdfi" />
              </div>
            </div>
          </div>
        </section>
        <!-- Popular Routes - Asymmetric Cards -->
        <section class="py-24 bg-surface-container-low">
          <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center mb-16">
              {{-- <h2 class="font-headline text-4xl font-extrabold tracking-tighter">
                Popular Routes
              </h2>
              <button class="text-primary font-bold flex items-center gap-2 hover:gap-3 transition-all">
                <span>View all destinations</span>
                <span class="material-symbols-outlined">east</span>
              </button> --}}
              @if($detectedCity)
                <div>
                  <h3 class="text-green-800 dark:text-green-400 text-2xl font-bold font-headline text-on-surface mb-2">Popular Routes from {{ $detectedCity }}</h3>
                  <p class="text-green-800 dark:text-green-400 text-on-surface-variant mb-6 text-sm">Handpicked direct bus trips heading out from your immediate area.</p>
                </div>
              @else
                <div>
                  <h3 class="text-green-800 dark:text-green-400 text-2xl font-bold font-headline text-on-surface mb-2">Trending Travel Routes</h3>
                  <p class="text-green-800 dark:text-green-400 text-on-surface-variant mb-6 text-sm">The most popular inter-city bus routes across Zambia today.</p>
                </div>
              @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
              @foreach($routes as $route)
              <a
                href="{{ route('trips.search', ['from' => $route->origin, 'to' => $route->destination, 'date' => \Carbon\Carbon::now()->format('Y-m-d')])}}">
                <div class="group cursor-pointer">
                  <div class="relative h-[400px] rounded-2xl overflow-hidden mb-6">
                    <img class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                      data-alt="Scenic route from {{ $route->origin }} to {{ $route->destination }}"
                      src="https://placehold.co/400x400?text={{ $route->origin }}+to+{{ $route->destination }}" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                    <div class="absolute bottom-6 left-6">
                      <span
                        class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full text-[10px] font-bold uppercase mb-2 inline-block">Daily
                        Trips</span>
                      <h4 class="text-white font-headline text-2xl font-bold">
                        {{ $route->origin }} to {{ $route->destination }}
                      </h4>
                    </div>
                  </div>
                  <div class="flex justify-between items-center px-2">
                    <span class="text-on-surface-variant font-medium">From ZMW {{ $route->fare }}</span>
                    <span class="text-on-surface-variant text-sm">{{ $route->distance_km }} km</span>
                  </div>
                </div>
              </a>
              @endforeach
            </div>
          </div>
        </section>
        <!-- Featured Operators -->
        <section class="py-24 px-6 max-w-7xl mx-auto text-center">
          <h2 class="font-headline text-3xl font-bold mb-12 text-outline">
            Trusted by Leading Operators
          </h2>
          <div
            class="flex flex-wrap justify-center items-center gap-12 md:gap-24 opacity-60 grayscale hover:grayscale-0 transition-all">
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-4xl text-primary">directions_bus</span>
              <span class="font-headline font-black text-xl">EURO-TRANS</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-4xl text-primary">commute</span>
              <span class="font-headline font-black text-xl">POWER-TOOLS</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-4xl text-primary">airport_shuttle</span>
              <span class="font-headline font-black text-xl">MAZHANDU</span>
            </div>
            <div class="flex items-center gap-2">
              <span class="material-symbols-outlined text-4xl text-primary">electric_bolt</span>
              <span class="font-headline font-black text-xl">FM-TRAVELLER</span>
            </div>
          </div>
        </section>
        <!-- Footer -->
        <footer class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
          <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="flex flex-col gap-4">
              <div class="font-manrope font-bold text-zinc-900 dark:text-zinc-100 text-lg">
                BookMyBus Zambia
              </div>
              <p class="font-inter text-xs text-zinc-500 dark:text-zinc-400 max-w-xs">
                © 2024 BookMyBus Zambia. Premium Travel Excellence. Your reliable
                partner for trans-Zambian journeys.
              </p>
            </div>
            <div class="flex flex-wrap md:justify-end gap-6 md:gap-12">
              <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                href="#">Privacy Policy</a>
              <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                href="#">Terms of Service</a>
              <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                href="#">Carrier Partners</a>
              <a class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
                href="#">Contact Us</a>
            </div>
          </div>
        </footer>
  </main>
  <!-- FAB (Suppressed on Landing per rules, but shown for context of the main action if required by specific logic - here hidden as per "Transactional/Focused" suppression guidelines for sub-pages, but landing is usually the base destination) -->
  <button
    class="fixed bottom-8 right-8 w-16 h-16 rounded-full hero-gradient text-white shadow-2xl flex items-center justify-center active:scale-90 transition-transform md:hidden">
    <span class="material-symbols-outlined text-3xl">search</span>
  </button>
</body>

</html>