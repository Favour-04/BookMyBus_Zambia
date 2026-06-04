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
      rel="stylesheet"
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
      rel="stylesheet"
    />
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
    </style>
  </head>
  <body class="bg-surface font-body text-on-surface">
    <!-- TopNavBar -->
    <nav
      class="fixed top-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md shadow-sm dark:shadow-none"
    >
      <div
        class="flex justify-between items-center px-6 py-4 max-w-7xl mx-auto w-full"
      >
        <div
          class="text-xl font-extrabold text-green-900 dark:text-green-100 tracking-tighter font-headline"
        >
          BookMyBus Zambia
        </div>
        <div class="hidden md:flex items-center gap-8">
          <a
            class="font-headline tracking-tight font-bold text-sm text-green-900 dark:text-green-100 border-b-2 border-orange-600 pb-1"
            href="#"
            >Find Trips</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="#"
            >My Bookings</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="#"
            >Operator Portal</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="#"
            >Support</a
          >
        </div>
        <div class="flex items-center gap-4">
          <button
            class="text-green-800 dark:text-green-400 font-headline font-bold text-sm px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors rounded-lg"
          >
            Sign In
          </button>
          <span
            class="material-symbols-outlined text-zinc-600 dark:text-zinc-400 cursor-pointer"
            >account_circle</span
          >
        </div>
      </div>
    </nav>
    <main class="pt-20">
      <!-- Hero Section -->
      <section
        class="relative min-h-[870px] flex items-center justify-center px-6 overflow-hidden"
      >
        <div class="absolute inset-0 z-0">
          <img
            class="w-full h-full object-cover brightness-[0.6]"
            data-alt="luxury modern motorcoach bus traveling on a wide open scenic Zambian highway during a bright clear morning"
            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDk0Dlz3kz8govksGNNICUM9-j8vL7Yon5vj-d_62Af9GeHSg1Xug_R0TlK9veZpFbgC_oejPsCpAQr0l8wvuUN_iOXbwgj4WRvh1fVY3FX3BZoWNw2sz9d4mW33rERvAfkJ_63pxYzhVQB46HITvif6J4bFr2yh4PYyGlUt3Kw4J9BJ7JhORkiTuUg1gplwcGy3laWI4Uvvnd6t1tnX1V9GktQ9QqNQVifCYfXCi60Wi4uTGLJ96VPI73U0OijKpre_xvwA1JOg7PX"
          />
        </div>
        <div class="relative z-10 max-w-7xl mx-auto w-full">
          <div class="max-w-3xl mb-12">
            <h1
              class="font-headline text-5xl md:text-7xl font-extrabold text-white tracking-tighter leading-none mb-6"
            >
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
          <form action="{{ route('trips.search') }}" method="GET" class="bg-surface-container-lowest p-2 rounded-xl editorial-shadow w-full max-w-5xl">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2">
              <div
                class="md:col-span-3 p-4 hover:bg-surface-container-low transition-colors rounded-lg cursor-pointer group"
              >
                <span
                  class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1"
                  >From</span
                >
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-primary text-xl"
                    >location_on</span
                  >
                  <input
                    class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                    placeholder="Lusaka"
                    type="text"
                    name="from"
                    value="{{ request('from') }}"
                  />
                </div>
              </div>
              <div
                class="md:col-span-3 p-4 hover:bg-surface-container-low transition-colors rounded-lg cursor-pointer group"
              >
                <span
                  class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1"
                  >To</span
                >
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-secondary text-xl"
                    >map</span
                  >
                  <input
                    class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                    placeholder="Livingstone"
                    type="text"
                    name="to"
                    value="{{ request('to') }}"
                  />
                </div>
              </div>
              <div
                class="md:col-span-2 p-4 hover:bg-surface-container-low transition-colors rounded-lg cursor-pointer group"
              >
                <span
                  class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1"
                  >Date</span
                >
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-outline text-xl"
                    >calendar_today</span
                  >
                  <input
                    class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                    placeholder="24 Oct 2024"
                    type="text"
                  />
                </div>
              </div>
              <div
                class="md:col-span-2 p-4 hover:bg-surface-container-low transition-colors rounded-lg cursor-pointer group"
              >
                <span
                  class="text-[10px] uppercase font-bold text-outline tracking-widest block mb-1"
                  >Passengers</span
                >
                <div class="flex items-center gap-2">
                  <span class="material-symbols-outlined text-outline text-xl"
                    >person</span
                  >
                  <input
                    class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0 w-full placeholder:text-surface-dim"
                    placeholder="1 Adult"
                    type="text"
                  />
                </div>
              </div>
              <div class="md:col-span-2 p-2">
                <button
                  class="w-full h-full hero-gradient text-white font-headline font-extrabold rounded-lg hover:brightness-110 active:scale-95 transition-all flex items-center justify-center gap-2 py-4 md:py-0"
                >
                  <span>Find Trips</span>
                  <span class="material-symbols-outlined">trending_flat</span>
                </button>
              </div>
            </div>
          </form>
        </div>
      </section>
      <!-- Why Choose Us - Bento Grid Pattern -->
      <section class="py-24 px-6 max-w-7xl mx-auto">
        <div
          class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6"
        >
          <div class="max-w-2xl">
            <span
              class="text-secondary font-bold tracking-widest text-xs uppercase"
              >The Difference</span
            >
            <h2
              class="font-headline text-4xl md:text-5xl font-extrabold tracking-tighter mt-2"
            >
              Redefining the Journey
            </h2>
          </div>
          <p
            class="text-on-surface-variant max-w-xs font-body italic border-l-2 border-primary-container pl-4"
          >
            "Setting the benchmark for transit technology in Central Africa."
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div
            class="md:col-span-2 bg-surface-container-low rounded-2xl p-10 flex flex-col justify-between min-h-[320px]"
          >
            <div
              class="w-14 h-14 rounded-full bg-primary-container flex items-center justify-center mb-6"
            >
              <span
                class="material-symbols-outlined text-on-primary-container text-3xl"
                >shield</span
              >
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
            class="bg-secondary-container rounded-2xl p-10 text-on-secondary-container flex flex-col justify-between"
          >
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
          <div
            class="bg-primary text-white rounded-2xl p-10 flex flex-col justify-between"
          >
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
            class="md:col-span-2 bg-surface-container-high rounded-2xl p-10 flex flex-col md:flex-row gap-8 items-center overflow-hidden"
          >
            <div class="flex-1">
              <h3 class="font-headline text-2xl font-bold mb-3">
                24/7 Premium Support
              </h3>
              <p class="text-on-surface-variant mb-6">
                Our dedicated team is always on standby to assist with
                rebookings or travel queries.
              </p>
              <button
                class="bg-surface-container-lowest px-6 py-2 rounded-full font-bold text-sm shadow-sm hover:translate-y-[-2px] transition-transform"
              >
                Speak to us
              </button>
            </div>
            <div class="flex-1 -mb-20 -mr-10">
              <img
                class="rounded-xl w-full h-48 object-cover grayscale brightness-110"
                data-alt="friendly customer support professional smiling with a headset in a modern bright office environment"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD9dzPzJm2Ho4o7j_qsVncE-5BSIQyLPaHIeIyb5JRDGzb7QJ7PVIDRT6rIGuyYuXzoF7TlO9Ar5PJLJFV4erlBpM_lxrJe82vTm76iUEDCkmSTp0pZlfjUQOObNoZ3gf53ym5F5NJysCeZiG7-RazyOLqKuDbMYKehvNxJu9uGcluSNHndqg4MMGwHbdj5LWhFn7zDZVpTA4a0T3KvxlpCv4msUJIvyyY7K3ltQG5T-CjaZzlejB3dCGe77-rH4xzxheP82wziqdfi"
              />
            </div>
          </div>
        </div>
      </section>
      <!-- Popular Routes - Asymmetric Cards -->
      <section class="py-24 bg-surface-container-low">
        <div class="max-w-7xl mx-auto px-6">
          <div class="flex justify-between items-center mb-16">
            <h2 class="font-headline text-4xl font-extrabold tracking-tighter">
              Popular Routes
            </h2>
            <button
              class="text-primary font-bold flex items-center gap-2 hover:gap-3 transition-all"
            >
              <span>View all destinations</span>
              <span class="material-symbols-outlined">east</span>
            </button>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            @foreach($routes as $route)
            <div class="group cursor-pointer">
              <div class="relative h-[400px] rounded-2xl overflow-hidden mb-6">
                <img
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                  data-alt="Scenic route from {{ $route->origin }} to {{ $route->destination }}"
                  src="https://placehold.co/400x400?text={{ $route->origin }}+to+{{ $route->destination }}"
                />
                <div
                  class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"
                ></div>
                <div class="absolute bottom-6 left-6">
                  <span
                    class="bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full text-[10px] font-bold uppercase mb-2 inline-block"
                    >Daily Trips</span
                  >
                  <h4 class="text-white font-headline text-2xl font-bold">
                    {{ $route->origin }} to {{ $route->destination }}
                  </h4>
                </div>
              </div>
              <div class="flex justify-between items-center px-2">
                <span class="text-on-surface-variant font-medium"
                  >From ZMW {{ $route->fare }}</span
                >
                <span class="text-on-surface-variant text-sm"
                  >{{ $route->distance_km }} km</span
                >
              </div>
            </div>
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
          class="flex flex-wrap justify-center items-center gap-12 md:gap-24 opacity-60 grayscale hover:grayscale-0 transition-all"
        >
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl text-primary"
              >directions_bus</span
            >
            <span class="font-headline font-black text-xl">EURO-TRANS</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl text-primary"
              >commute</span
            >
            <span class="font-headline font-black text-xl">POWER-TOOLS</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl text-primary"
              >airport_shuttle</span
            >
            <span class="font-headline font-black text-xl">MAZHANDU</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-4xl text-primary"
              >electric_bolt</span
            >
            <span class="font-headline font-black text-xl">FM-TRAVELLER</span>
          </div>
        </div>
      </section>
      <!-- Footer -->
      <footer
        class="w-full py-12 mt-auto bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800"
      >
        <div
          class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-8 items-center"
        >
          <div class="flex flex-col gap-4">
            <div
              class="font-manrope font-bold text-zinc-900 dark:text-zinc-100 text-lg"
            >
              BookMyBus Zambia
            </div>
            <p
              class="font-inter text-xs text-zinc-500 dark:text-zinc-400 max-w-xs"
            >
              © 2024 BookMyBus Zambia. Premium Travel Excellence. Your reliable
              partner for trans-Zambian journeys.
            </p>
          </div>
          <div class="flex flex-wrap md:justify-end gap-6 md:gap-12">
            <a
              class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
              href="#"
              >Privacy Policy</a
            >
            <a
              class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
              href="#"
              >Terms of Service</a
            >
            <a
              class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
              href="#"
              >Carrier Partners</a
            >
            <a
              class="font-inter text-xs text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200 underline underline-offset-4 transition-opacity hover:opacity-80"
              href="#"
              >Contact Us</a
            >
          </div>
        </div>
      </footer>
    </main>
    <!-- FAB (Suppressed on Landing per rules, but shown for context of the main action if required by specific logic - here hidden as per "Transactional/Focused" suppression guidelines for sub-pages, but landing is usually the base destination) -->
    <button
      class="fixed bottom-8 right-8 w-16 h-16 rounded-full hero-gradient text-white shadow-2xl flex items-center justify-center active:scale-90 transition-transform md:hidden"
    >
      <span class="material-symbols-outlined text-3xl">search</span>
    </button>
  </body>
</html>