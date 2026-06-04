<!-- TODO: This view should extend layouts.app when a layout is available -->
<!-- TODO: Loop through 'bookings' from database to display travel history -->

<!doctype html>

<html class="light" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Travel History | BookMyBus Zambia</title>
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
    <nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md shadow-sm">
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
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="#"
            >Find Trips</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-green-900 border-b-2 border-orange-600 pb-1"
            href="#history"
            >Travel History</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="#past-bookings"
            >My Bookings</a
          >
          <a
            class="font-headline tracking-tight font-bold text-sm text-zinc-600 dark:text-zinc-400 hover:text-green-900 dark:hover:text-green-100 transition-colors"
            href="#support"
            >Support</a
          >
        </div>
        <div class="flex items-center gap-4">
          <button
            class="text-green-800 font-headline font-bold text-sm px-4 py-2 hover:bg-zinc-50 rounded-lg transition-colors"
          >
            Sign In
          </button>
          <span class="material-symbols-outlined text-zinc-600 cursor-pointer"
            >account_circle</span
          >
        </div>
      </div>
    </nav>

    <main class="pt-24 pb-16">
      <section class="relative overflow-hidden px-6">
        <div
          class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(0,96,31,0.18),_transparent_30%),radial-gradient(circle_at_bottom_left,_rgba(255,141,33,0.16),_transparent_30%)]"
        ></div>
        <div
          class="relative max-w-7xl mx-auto grid gap-10 lg:grid-cols-[1.3fr,_0.9fr] items-center"
        >
          <div class="space-y-6">
            <span
              class="inline-flex items-center gap-2 text-xs uppercase tracking-[0.3em] font-bold text-secondary"
            >
              <span class="material-symbols-outlined">history</span>
              Travel History
            </span>
            <h1
              class="text-5xl md:text-6xl font-extrabold tracking-tight max-w-3xl"
            >
              Your journeys, saved and ready to review.
            </h1>
            <p class="text-lg text-on-surface-variant max-w-2xl">
              See your completed trips, payment history and boarding records in
              one elegant, easy-to-scan timeline.
            </p>
            <div class="grid sm:grid-cols-3 gap-4">
              <div
                class="bg-surface-container-lowest rounded-3xl p-6 editorial-shadow border border-surface-container-highest"
              >
                <p
                  class="text-xs uppercase tracking-[0.3em] font-bold text-zinc-500"
                >
                  Trips Completed
                </p>
                <p class="text-4xl font-extrabold text-on-surface mt-3">18</p>
              </div>
              <div
                class="bg-surface-container-lowest rounded-3xl p-6 editorial-shadow border border-surface-container-highest"
              >
                <p
                  class="text-xs uppercase tracking-[0.3em] font-bold text-zinc-500"
                >
                  Saved Receipts
                </p>
                <p class="text-4xl font-extrabold text-on-surface mt-3">12</p>
              </div>
              <div
                class="bg-surface-container-lowest rounded-3xl p-6 editorial-shadow border border-surface-container-highest"
              >
                <p
                  class="text-xs uppercase tracking-[0.3em] font-bold text-zinc-500"
                >
                  Last Trip
                </p>
                <p class="text-4xl font-extrabold text-on-surface mt-3">
                  2 days ago
                </p>
              </div>
            </div>
          </div>
          <div
            class="rounded-[2rem] overflow-hidden shadow-xl shadow-primary/10 bg-white border border-surface-container-highest"
          >
            <img
              class="w-full h-full object-cover min-h-[420px]"
              data-alt="Luxury coach bus traveling on a scenic Zambian highway"
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuDk0Dlz3kz8govksGNNICUM9-j8vL7Yon5vj-d_62Af9GeHSg1Xug_R0TlK9veZpFbgC_oejPsCpAQr0l8wvuUN_iOXbwgj4WRvh1fVY3FX3BZoWNw2sz9d4mW33rERvAfkJ_63pxYzhVQB46HITvif6J4bFr2yh4PYyGlUt3Kw4J9BJ7JhORkiTuUg1gplwcGy3laWI4Uvvnd6t1tnX1V9GktQ9QqNQVifCYfXCi60Wi4uTGLJ96VPI73U0OijKpre_xvwA1JOg7PX"
            />
          </div>
        </div>
      </section>

      <section id="history" class="max-w-7xl mx-auto px-6 mt-16">
        <div
          class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8"
        >
          <div>
            <p
              class="text-sm uppercase tracking-[0.35em] text-secondary font-bold"
            >
              Trip archive
            </p>
            <h2 class="text-3xl font-extrabold text-on-surface mt-3">
              Recent travel history
            </h2>
          </div>
          <button
            class="inline-flex items-center gap-2 rounded-full bg-primary text-white px-5 py-3 font-bold text-sm shadow-lg shadow-primary/20 hover:brightness-105 transition-all"
          >
            <span class="material-symbols-outlined">download</span>
            Export history
          </button>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
          {{-- TODO: Loop through 'bookings' from database to display travel history --}}
          @foreach($bookings ?? [] as $booking)
          <article
            class="bg-white rounded-[2rem] p-6 editorial-shadow border border-surface-container-highest"
          >
            <div class="flex items-center justify-between mb-4">
              <div>
                <p
                  class="text-xs uppercase tracking-[0.3em] text-zinc-400 font-bold"
                >
                  Completed
                </p>
                <h3 class="text-xl font-extrabold text-on-surface mt-2">
                  {{ $booking->origin ?? 'Lusaka' }} → {{ $booking->destination ?? 'Livingstone' }}
                </h3>
              </div>
              <span
                class="inline-flex items-center gap-2 rounded-full bg-secondary-container/10 text-secondary px-3 py-1 text-xs font-bold uppercase"
              >
                Completed
              </span>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm text-zinc-500 mb-6">
              <div>
                <p class="font-semibold text-on-surface">{{ $booking->date ?? '24 Oct 2024' }}</p>
                <p class="mt-1">{{ $booking->time ?? '08:30 AM' }}</p>
              </div>
              <div>
                <p class="font-semibold text-on-surface">{{ $booking->seat ?? '14A' }}</p>
                <p class="mt-1">K {{ $booking->fare ?? '420' }}</p>
              </div>
            </div>
            <p class="text-sm text-zinc-600 leading-7">
              {{ $booking->description ?? 'A smooth executive journey through Zambia\'s southern corridor with complimentary Wi-Fi and bottled water.' }}
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <span
                class="inline-flex items-center gap-2 rounded-full bg-surface-container-low px-4 py-2 text-[11px] font-bold uppercase tracking-[0.2em] text-zinc-500"
              >
                <span class="material-symbols-outlined text-[16px]"
                  >check_circle</span
                >
                On-time
              </span>
              <span
                class="inline-flex items-center gap-2 rounded-full bg-surface-container-low px-4 py-2 text-[11px] font-bold uppercase tracking-[0.2em] text-zinc-500"
              >
                <span class="material-symbols-outlined text-[16px]">wifi</span>
                Wi-Fi onboard
              </span>
            </div>
          </article>
          @endforeach
        </div>
      </section>

      <section id="past-bookings" class="max-w-7xl mx-auto px-6 mt-14">
        <div
          class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6"
        >
          <div>
            <p
              class="text-sm uppercase tracking-[0.35em] text-secondary font-bold"
            >
              Booking history
            </p>
            <h2 class="text-3xl font-extrabold text-on-surface mt-3">
              All past journeys
            </h2>
          </div>
          <div
            class="rounded-full bg-surface-container-low px-4 py-3 text-sm text-zinc-600"
          >
            Showing {{ count($bookings ?? []) }} recent trips
          </div>
        </div>

        <div
          class="overflow-hidden rounded-[2rem] border border-surface-container-highest bg-white editorial-shadow"
        >
          <table class="min-w-full text-left border-collapse">
            <thead
              class="bg-surface-container-low text-zinc-500 text-[11px] uppercase tracking-[0.25em] font-bold"
            >
              <tr>
                <th class="px-6 py-4">Route</th>
                <th class="px-6 py-4">Date</th>
                <th class="px-6 py-4">Departure</th>
                <th class="px-6 py-4">Seat</th>
                <th class="px-6 py-4">Fare</th>
                <th class="px-6 py-4">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-surface-container-highest">
              {{-- TODO: Loop through 'bookings' to display in table format --}}
              @foreach($bookings ?? [] as $booking)
              <tr class="hover:bg-surface-container-lowest transition-colors">
                <td class="px-6 py-5 font-semibold text-on-surface">
                  {{ $booking->origin ?? 'Lusaka' }} → {{ $booking->destination ?? 'Livingstone' }}
                </td>
                <td class="px-6 py-5 text-zinc-500">{{ $booking->date ?? '24 Oct 2024' }}</td>
                <td class="px-6 py-5 text-zinc-500">{{ $booking->time ?? '08:30 AM' }}</td>
                <td class="px-6 py-5 text-zinc-500">{{ $booking->seat ?? '14A' }}</td>
                <td class="px-6 py-5 font-semibold text-on-surface">K {{ $booking->fare ?? '420' }}</td>
                <td class="px-6 py-5 text-sm text-secondary font-bold">
                  Completed
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </section>

      <section id="support" class="max-w-7xl mx-auto px-6 mt-14">
        <div
          class="rounded-[2rem] bg-surface-container-lowest p-8 editorial-shadow border border-surface-container-highest"
        >
          <div
            class="flex flex-col md:flex-row md:items-center md:justify-between gap-6"
          >
            <div>
              <p
                class="text-sm uppercase tracking-[0.35em] text-secondary font-bold"
              >
                Need help?
              </p>
              <h2 class="text-3xl font-extrabold text-on-surface mt-3">
                Customer support for historical trips
              </h2>
            </div>
            <button
              class="inline-flex items-center gap-2 rounded-full bg-primary text-white px-5 py-3 font-bold text-sm shadow-lg shadow-primary/20 hover:brightness-105 transition-all"
            >
              <span class="material-symbols-outlined">support_agent</span>
              Contact support
            </button>
          </div>
          <div class="mt-8 grid gap-4 sm:grid-cols-2">
            <div
              class="rounded-3xl bg-white p-6 border border-surface-container-highest"
            >
              <p class="text-sm font-bold text-on-surface">
                Need your receipt?
              </p>
              <p class="mt-2 text-sm text-zinc-600">
                Download invoices for any completed trip in seconds.
              </p>
            </div>
            <div
              class="rounded-3xl bg-white p-6 border border-surface-container-highest"
            >
              <p class="text-sm font-bold text-on-surface">
                Report a journey issue
              </p>
              <p class="mt-2 text-sm text-zinc-600">
                Share feedback or request help if a past journey had delays or
                booking questions.
              </p>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>