# Views Summary - BookMyBus Zambia

## Overview

This document summarises the Blade view templates in the `resources/views` directory of the BookMyBus Zambia application. The directory currently contains **73 template files** covering traveler-facing pages, authentication screens, layout shells and partials, the operator portal, the admin portal, and 8 orphaned/unwired scaffold views under `admin/` (see the note at the end of Section E) — 65 of the 73 are part of the live application.

The views are built with Tailwind CSS (loaded from CDN) and follow a consistent Material Design 3-inspired design system with custom color variables (`primary` green `#00601f`, `secondary-container` orange `#ff8921`, `surface` `#f9f9fc`, etc.). Nearly all traveler-facing pages now extend `layouts.app` (`landing_search`, `search_results`, `seat_selection`, `payment_ticket`, `history_page`, `booking_lookup`, `ticket`, `profile`, `support_page`, `contact_us`, `carrier_partners`, `privacy_policy`, `terms_of_service`) — only `welcome.blade.php` (the unused Laravel starter page) and the legacy `_search_results.blade.php` remain self-contained documents. `layouts.app` itself now includes full dark-mode support (a `localStorage`-backed theme toggle and a light/dark CSS custom-property token set) in addition to the shared nav/footer shell. Every admin view extends `layouts.admin`, and the operator portal pages are self-contained documents with an inline sidebar (a `layouts.operator` shell exists but is not yet used by the operator views).

## View Files Summary

### A. Public (Traveler) Views

#### 1. `welcome.blade.php`

**Purpose:** Default Laravel welcome page (starter template)

**Key Features:**

- Standard Laravel welcome page with documentation links
- Uses Tailwind CSS v4.0.7 with inline styles
- Contains navigation for login/register routes
- Displays Laravel logo SVG graphics
- Responsive layout with flex/grid utilities

**Notes:** This is a default Laravel template and not part of the custom application UI.

---

#### 2. `landing_search.blade.php`

**Purpose:** Main landing page with trip search functionality

**Key Features:**

- **Layout:** Extends `layouts.app` (shared nav/footer/dark-mode shell) and uses `@section('content')`
- **Hero Section:** Full-width hero with gradient overlay background image, headline "Travel Zambia with Confidence"
- **Search Form:** Origin/Destination fields with live city autocomplete (JS-driven suggestion list filtered from `$cities`, keyboard navigable), date picker (today to +30 days), passenger counter (1-5) with increment/decrement buttons, and a submit button to find trips
- **Why Choose Us Section:** Bento grid - Secure Transactions, Lightning Fast booking, Mobile Money Ready, 24/7 Premium Support
- **Popular Routes:** Dynamic route cards showing origin to destination with fare and distance
- **Trusted Operators:** Logos for EURO-TRANS, POWER-TOOLS, MAZHANDU, FM-TRAVELLER
- **JavaScript:** passenger increment/decrement, and `initCityAutocomplete()` wiring the From/To inputs to the `$cities` list

**Data Variables:**

- `$routes` - Collection of route objects
- `$detectedCity` - Optional detected city for personalised route suggestions
- `$cities` - Flat, deduped, sorted list of Zambian cities (from `config/zambia_cities.php`) powering the From/To autocomplete

---

#### 3. `search_results.blade.php`

**Purpose:** Display search results for available bus trips

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`; filters are a real GET form (`#filterForm`) posting back to `trips.search`, auto-submitting on change (time-of-day toggle, operator checkboxes, price-slider release)
- **Filters Sidebar:** Time of Day filter (Dawn, Morning, Afternoon, Night) bound to `time_of_day[]` and `LandingController::search()`'s `departureTimeOfDay` scope; dual-handle price range slider (ZMW 150-800) bound to `min_price`/`max_price` and the `priceBetween` scope; preferred-operator checkboxes driven by `$allOperators` (every operator matching the base route/date search, so the list doesn't shrink as other filters are toggled); "Clear" link when any filter is active; map view ad for route tracking
- **Results Header:** Route summary, passenger count and date, Modify Search button
- **Trip Cards:** Operator name and rating, departure time and origin terminal, visual route indicator with distance, arrival time and destination station, fare and available seats (with sold-out detection), "View Seats" CTA (disabled when sold out)
- **Empty State:** Message when no buses found
- **Info Grid:** Verified Operators, Instant Ticket, 24/7 Support
- **JavaScript:** time-of-day/operator checkbox auto-submit, dual-handle price slider with drag-then-release submit

**Data Variables:**

- `$request` - Search request object (origin, destination, passengers, travel_date, min_price, max_price, time_of_day, operators)
- `$trips` - Collection of available trip objects (filtered by price range, time of day, and operator)
- `$allOperators` - Collection of operators matching the base origin/destination/date search, used to render the operator filter checklist

---

#### 4. `_search_results.blade.php`

**Purpose:** Legacy/standby search results view (development artifact kept for reference)

**Notes:** The file still exists in the repository. It mirrors `search_results.blade.php` but renders static operator data.

**Key Features:**

- Same filter sidebar and trip card layout as `search_results.blade.php`
- Static operator names (Power Tools, Euro Africa, Likili Motorways, Mazhandu)
- Placeholder images for operator logos

**Data Variables:**

- `$request` - Search request object
- `$trips` - Collection of trip objects (shadowed by static demo data)

---

#### 5. `seat_selection.blade.php`

**Purpose:** Interactive seat selection interface for booking

**Key Features:**

- **Multi-Seat Group Booking:** Selects exactly `$passengers` seats (clamped to remaining capacity), one passenger form per seat; seats are assigned to passengers in tap order, with the first passenger labelled "Pays for the group" when `$passengers > 1`; Confirm is disabled until every required seat is selected
- **Seat Legend:** Available, Selected, and Occupied indicators
- **Bus Interior Visualisation:** cockpit/driver area indicator, 5-column seat grid, dynamic seats rendered from `$route->bus->seat_capacity`, colour-coded states, JavaScript selection with visual feedback and a live "X of N selected" progress label
- **Trip Summary Card:** route, bus class badge, departure/arrival times, selected-seat chips, live total fare (`fare × passengers`)
- **Booking Form:** one block per passenger — full name, NRC/ID number, phone number — submitted as `passengers[i][...]`; restores prior seat selections after a failed validation round-trip via `old('seat_numbers')`
- **Security Assurance:** Encryption notice
- **JavaScript:** multi-seat selection/deselection with a required-seat cap, live passenger-to-seat sync, price/progress display updates, submit-time validation

**Data Variables:**

- `$route` - Route object with trip details (includes `bus->seat_capacity`)
- `$bookedSeats` - Array of already booked seat numbers
- `$searchBackUrl` - URL back to search results
- `$passengers` - Number of seats/passengers required for this booking (from `showSeats()`, clamped to remaining capacity)
- `$errors` - Validation error messages

---

#### 6. `payment_ticket.blade.php`

**Purpose:** Secure payment processing page

**Key Features:**

- **Mobile Money Options:** Airtel Money (red theme), MTN MoMo (yellow theme, marked as recommended)
- **Payment Form:** Zambian phone number validation, pay button with total fare, provider selection with dynamic form updates
- **Back to Seat Selection:** Confirm-then-POST to `bookings.release-hold`, warning that going back releases the held seat(s) back to other travelers
- **Multi-Seat Group Display:** When `$group_bookings` has more than one booking, shows "+N more" beside the passenger name, lists every seat number, and renders a "Your Party" card with each passenger/seat pair; fare summary totals are aggregated across the whole group
- **Reservation Countdown Timer** showing seat-hold time remaining and an **Expired Booking Banner**
- **Digital Ticket Display:** scenic header image, status badge, passenger name and seat(s), route visualisation (origin → destination), departure date/time, booking ID and class, QR code
- **Fare Summary:** base fare, service fees, promo discount (when applied), total
- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **JavaScript:** provider selection, countdown timer, loading state handling, release-hold confirmation prompt

**Data Variables:**

- `$booking` - Booking object
- `$group_bookings` - Collection of sibling bookings sharing the same `group_reference` (or just `[$booking]` for a single-seat purchase)
- `$total_fare`, `$base_fare`, `$service_fee_total`, `$discount_amount`, `$applied_promo` - Fare breakdown aggregated across the group
- `$passenger_name`, `$seat_number`, `$seat_numbers`
- `$origin`, `$destination`, `$origin_code`, `$destination_code`
- `$departure_date`, `$departure_time`, `$class_type`, `$booking_id`
- `$held_until` - Reservation expiry timestamp
- `$expired` - Boolean for expired reservation

---

#### 7. `history_page.blade.php`

**Purpose:** Digital ticket view for confirmed bookings

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **Payment Success Banner:** Green banner with check icon when a booking is confirmed, plus a seat count line for multi-seat purchases
- **Digital Ticket(s):** One ticket card per booking in the party (`$groupBookings`, labelled "Passenger X of N" when there's more than one) — scenic header image, status badge (Confirmed/Pending), passenger name and seat, route information, departure date/time, booking ID and bus class, QR code from the ticket's `qr_code` (or the reference ID as a fallback)
- **Fare Summary:** Total fare aggregated across the party, "View Ticket" link (when a ticket has been issued), Print, and "Book Another Trip" buttons

**Data Variables:**

- `$booking` - Primary booking object with passenger details, route, and amount
- `$groupBookings` - Collection of sibling bookings sharing the same `group_reference` (or just `[$booking]` for a single-seat purchase)
#### 8. `booking_lookup.blade.php`

**Purpose:** "My Bookings" page — signed-in dashboard plus guest booking-reference lookup ("Find My Booking")

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **Signed-In Dashboard** (`@auth`): Immediately lists the traveler's own paginated, trip-grouped booking history (`$trips`, from `BookingController::paginatedTripsForCurrentUser()`) — one card per purchase (multi-seat purchases show seat count instead of a single seat number), status badge, total amount, and a "View" link to the success/ticket page; empty state with a "Find a Trip" CTA; links to Profile → Booking History for cancellation
- **Reference-ID Lookup** (open to guests and signed-in travelers): POSTs to `booking.lookup.search`; accepts only the exact booking reference ID (no phone-number search, by design — see `BookingController::customerLookup`); inline error banner when no match is found
- **Booking Summary Card:** Reference ID, status, route, travel date and departure time, total; for a multi-seat match, lists every seat/passenger in the party with a QR code each
- **QR Code:** Rendered via the qrserver.com API per seat, with print support

**Data Variables:**

- `$trips` - Paginated, trip-grouped booking history for the signed-in user (`null` for guests)
- `$error` - Lookup error message (nullable)
- `$foundBooking` - Matched booking object with `route` relations (nullable)
- `$groupBookings` - Sibling bookings sharing the matched booking's `group_reference` (or just `[$foundBooking]`)

---

#### 9. `ticket.blade.php`

**Purpose:** Standalone digital ticket page (accessed from booking lookup / history)

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **Ticket Card:** Header band, status badge, reference, passenger name and seat number, route visualisation, departure date/time, amount, operator, bus registration, issued timestamp
- **QR Code:** API-generated from `$ticket->qr_code` with the raw code echoed below it
- **Print Optimisation:** `@media print` rules hide navigation/actions (`.no-print`)
- **Actions:** Book Another Trip and Print Ticket (hidden when printing)

**Data Variables:**

- `$booking` - Booking object (with `route` relations)
- `$ticket` - Issued ticket object (`qr_code`, `issued_at`)

---

#### 10. `profile.blade.php`

**Purpose:** Traveler profile and account management page

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **Profile Header:** Avatar placeholder, user name and email
- **Tabbed Interface:** Account Details (active), Change Password, Booking History; auto-switches to Password on password-validation errors, or to Booking History when redirected with `session('active_tab') === 'bookings'`
- **Account Details Form:** Full name, email, phone, preferred language (English, Nyanja, Bemba), Save Changes
- **Change Password Form:** Current / New / Confirm new password, Update Password
- **Booking History Tab:** Trip-grouped, paginated (10/page) booking history (`$trips`) — one card per purchase (multi-seat purchases expand to a per-seat breakdown with an individual Cancel action for each still-cancellable seat), route, reference, date, amount, status badges (confirmed, pending, cancelled), a Cancel Booking action for cancellable single-seat bookings, empty state with "Find a Trip" CTA
- **Status Messages:** success flash and validation errors
- **JavaScript:** tab switching, auto-select tab based on validation errors or the `active_tab` session flag

**Data Variables:**

- `$user` - User object (full_name, email, phone_number, preferred_language)
- `$trips` - Paginated collection of the user's booking history, grouped into one entry per purchase via `Booking::groupIntoTrips()`
- `$errors` - Validation error messages; session status/`active_tab` messages

---

#### 11. `support_page.blade.php`

**Purpose:** Customer support page with FAQ and contact options

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **FAQ Section:** Expandable items covering booking, payment, cancellation, tickets, missed buses
- **Contact Options:** Call Us (phone numbers), WhatsApp chat link, Email Us
- **Contact Form:** Name, email, subject, message and a Send Message button
- **Footer:** Copyright and policy links

**Data Variables:**

- None (static content)

---

#### 12. `contact_us.blade.php`

**Purpose:** Public contact page with form and office information

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')` (previously a self-contained document with its own nav/footer)
- **Contact Cards:** Call Us, Email Us, Visit Us
- **Contact Form:** Name, email, subject dropdown (Booking Issue, Payment Problem, Cancellation Request, General Inquiry, Feedback, Partnership) and message textarea

**Data Variables:**

- None (static form; no backend handler wired — the form has a `@csrf` token but no `action`/`method`, so submitting it does nothing)

---

#### 13. `carrier_partners.blade.php`

**Purpose:** Bus operator partners showcase page

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **Partner Cards:** Four verified partners (EURO-TRANS, POWER-TOOLS, MAZHANDU, FM Traveller) with icon, description, rating and trips completed
- **Become a Partner CTA:** Primary-coloured call-to-action linking to `operator.login`

**Data Variables:**

- None (static content)

---

#### 14. `privacy_policy.blade.php`

**Purpose:** Public privacy policy page

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **Sections:** Information collected, how data is used, mobile money partners, data security, your rights, contact us
- **Contact Links:** mailto for support@bookmybus.zm and link to `contact-us` route
- **Footer:** Standard policy links

**Data Variables:**

- None (static content)

---

#### 15. `terms_of_service.blade.php`

**Purpose:** Public terms of service page

**Key Features:**

- **Layout:** Extends `layouts.app` and uses `@section('content')`
- **Sections:** Booking terms, payments and refunds, cancellations, passenger responsibilities, limitation of liability, contact us
- **Passenger Responsibilities:** Arrival time, valid ID/NRC matching ticket, QR presentation at boarding, safety instructions

**Data Variables:**

- None (static content)
### B. Authentication Views

#### 16. `auth/login.blade.php`

**Purpose:** Traveler sign-in page

**Key Features:**

- **Split Layout:** Left decorative panel (green gradient), right form panel
- **Left Panel:** Feature highlights (Digital tickets, Choose seat, Mobile money) and a link to the operator portal
- **Right Panel:** Email field with icon, password with show/hide toggle, remember me checkbox, Sign In button, registration link ("coming soon")
- **Form Validation:** Error display for invalid credentials

**Data Variables:**

- `$errors` - Validation error messages

---

#### 17. `auth/operator-login.blade.php`

**Purpose:** Operator portal sign-in page

**Key Features:**

- **Split Layout:** Left decorative panel with orange gradient (distinguishes from traveler login)
- **Left Panel:** Operator-specific features (Seat inventory, Revenue tracking, Route publishing), link to traveler sign-in
- **Right Panel:** Email + password with toggle, remember me checkbox, Sign In to Portal button, pending verification notice
- **Form Validation:** Error display for invalid credentials

**Data Variables:**

- `$errors` - Validation error messages
- Session status messages

---

#### 18. `auth/admin-login.blade.php`

**Purpose:** Admin portal sign-in page (separate guard family from traveler/operator)

**Key Features:**

- **Centered Card Layout:** Dark green gradient backdrop with blurred orbs, frosted-glass auth card
- **Header:** "Admin Portal" pill with badge icon, "Welcome Back" headline
- **Form Fields:** Email, password, remember me
- **Fallbacks:** Error banner from `$errors`, session status banner
- **Footer:** "Back to BookMyBus Zambia" link to `home`, copyright line

**Data Variables:**

- `$errors` - Validation error messages
- Session status messages

---

#### 19. `auth/register.blade.php`

**Purpose:** User registration page

**Key Features:**

- **Centered Card Layout:** Blur effect and gradient background
- **Form Fields:** Full Name, Email, Phone Number, Password (floating labels with show/hide toggle), password strength indicator (4-level bar), Terms of Service checkbox
- **Submit Button:** Create Account with icon
- **Login Link / Benefits Grid:** Secure Booking, Mobile Money, 24/7 Support, Digital Tickets

**Data Variables:**

- `$errors` - Validation error messages

---

#### 20. `auth/forgot-password.blade.php`

**Purpose:** Password reset request page

**Key Features:**

- **Centered Card Layout:** Clean, minimal design
- **Form Fields:** Email Address input
- **Submit Button:** Email Password Reset Link
- **Back to Login Link**

**Data Variables:**

- None (static form)

---

#### 21. `auth/reset-password.blade.php`

**Purpose:** Password reset confirmation page

**Key Features:**

- **Centered Card Layout:** Clean, minimal design
- **Form Fields:** Email Address (pre-filled, hidden), New Password, Confirm Password
- **Submit Button:** Reset Password

**Data Variables:**

- `$token` - Password reset token
- `$email` - User email address

---

### C. Layouts & Partials

#### 22. `layouts/app.blade.php`

**Purpose:** Shared layout shell for nearly all traveler-facing pages (see the Overview note — 13 of the 15 public views now extend this layout)

**Key Features:**

- **Dark Mode:** Pre-paint bootstrap script reads `localStorage['theme']` (falling back to `prefers-color-scheme`) and stamps `.dark`/`.light` on `<html>` before first render to avoid a flash of the wrong theme; a full light/dark CSS custom-property token set (`--color-primary`, `--color-surface`, etc., following Material 3 dark guidance) is redefined under `.dark`; a header toggle button flips the class, persists the choice to `localStorage`, and swaps its icon
- **Head:** Tailwind CDN, Google Fonts (Manrope/Inter), Material Symbols, shared Tailwind MD3 color config (tokens point at the CSS variables above so they can swap themes without regenerating CSS)
- **Navigation Bar:** Fixed translucent nav with brand, Find Trips, My Bookings, Operator Portal, Support links, theme toggle button, account icon (auth-aware: profile link or login link)
- **Content Slot:** `@yield('content')` inside `@yield('main-class', ...)` (pages override this to control width/padding)
- **Footer:** Copyright and policy links (Privacy Policy, Terms of Service, Carrier Partners, Contact Us)
- **Print Support:** `@media print` rules hide `.no-print` elements (nav/footer) and force a white background
- **Scripts:** `@stack('styles')` in the head, `@stack('scripts')` before `</body>`, plus the inline theme-toggle script

**Data Variables:**

- None (uses Blade sections and the optional `@yield('title', ...)`)

---

#### 23. `layouts/admin.blade.php`

**Purpose:** Admin portal layout shell extended by all `admin/*` views

**Key Features:**

- **Head:** Tailwind + Material Symbols, `@stack('head')` hook (used by Chart.js on analytics pages)
- **Sidebar:** Fixed 64-wide column, brand "BookMyBus / Admin Portal", `layouts.partials.admin-nav`, Sign Out POST to `admin.logout`
- **Header:** `@yield('page_title')`, avatar chip
- **Flash Messages:** session success/status/error/warning and `$errors` banner block before content
- **Content Slot:** `@yield('content')`; scripts via `@stack('scripts')`

**Data Variables:**

- `$errors` - Validation errors (expected on every admin page)
- Session flash messages

---

#### 24. `layouts/operator.blade.php`

**Purpose:** Operator portal layout shell (defined but not yet used by operator views)

**Key Features:**

- Mirrors `layouts.admin`: fixed sidebar with brand from `$operator->company_name`, settings + Sign Out links, header with `@yield('page_title')`, session flash messages and `$errors` block, `@yield('content')`
- **Sidebar:** Includes `layouts.partials.operator-nav`

**Data Variables:**

- `$operator` - Operator object (company_name)
- `$errors` - Validation error messages; session flash messages

---

#### 25. `layouts/partials/admin-nav.blade.php`

**Purpose:** Admin sidebar navigation partial (included by `layouts.admin`)

**Key Features:**

- Active-route highlighting via `request()->routeIs(...)` with `text-primary font-bold border-r-4` styling
- Entries: Dashboard, Operators, Verifications (pending operators), Travelers, Bookings, Trips, Payments, Reports, Audit Log, Settings

**Data Variables:**

- None (route-request helpers only)

---

#### 26. `layouts/partials/operator-nav.blade.php`

**Purpose:** Operator sidebar navigation partial (included by `layouts.operator` and custom sidebars)

**Key Features:**

- Same active-route highlighting pattern as admin nav
- Entries: Dashboard, Manage Trips, All Bookings, Revenue, Audit Log, Customers, Fleet, Drivers, Fare Rules, Promo Codes, Route Templates, Passengers

**Data Variables:**

- None (route-request helpers only)

---

#### 27. `layouts/partials/toasts.blade.php`

**Purpose:** Shared client-side toast + JSON fetch helper (included via `@once`)

**Key Features:**

- **Toast Container:** Fixed bottom-right stack; `window.BMB.toast(msg, type, timeout)` with success/error/warning/info tones and auto-dismiss
- **JSON Fetch:** `window.BMB.jsonFetch(url, options)` helper returning parsed JSON and raising errors with server `message`/`__error` payloads
- **Singleton Guard:** Loads once per page (`window.BMB.__loaded`)

**Data Variables:**

- None (client-side JavaScript utility)
### D. Operator Portal Views

#### 28. `operator/dashboard.blade.php`

**Purpose:** Operator dashboard with bento-grid layout and real-time monitoring

**Key Features:**

- **Sidebar Navigation:** Dashboard, Manage Trips, Seat Maps, Revenue, Profile, New Trip button, Settings and Support links
- **KPI Row:** Total Bookings (with trend), Revenue Generated (with daily average), Active Fleet (occupancy progress), Average Occupancy highlight
- **Bento Grid:** Live fleet status (bus cards with status badges and progress bars), Upcoming Trips table, Notifications & Alerts
- **Alert Types:** Maintenance Required, High Demand Route, Driver Rest Alert (colour-coded)
- **New Trip Drawer:** Slide-out creation form (route, date/time, bus, class, fare, notes)
- **Fallback Data:** Inline `@php` default alert dataset when `$alerts` is not supplied

**Data Variables:**

- `$operator`, `$total_bookings`, `$bookings_trend`, `$revenue`, `$revenue_trend`, `$revenue_average`, `$active_trips_count`, `$total_trips_today`, `$avg_occupancy`, `$fleet_status`, `$upcoming_trips`, `$alerts`

---

#### 29. `operator/manage_trips.blade.php`

**Purpose:** Advanced trip management with filtering, search, and drawer-based trip creation

**Key Features:**

- **Sidebar Navigation:** Dashboard, Manage Trips, Seat Maps, Revenue, Profile, New Trip button
- **Header:** Page title/subtitle, real-time search input, notification and schedule icons
- **Filter Bar:** Status pills (All, Scheduled, On route, Delayed, Completed), date filter, Export and New trip buttons
- **Trips Table:** Trip ID, Route, Date & time, Bus, Occupancy, Status, Actions; hover actions, status badges, Edit / View seat map / Cancel actions
- **New Trip Drawer:** Slide-out form with Tom Select searchable route picker, origin/destination selectors, date/time pickers, bus radio selection, class and fare inputs, notes field
- **Statistics Strip:** Today's trips, ongoing trips, completed, occupancy summary

**Data Variables:**

- `$operator`, `$trips`, `$routes`, `$buses`, `$stats` (trip counts)

---

#### 30. `operator/trip_calendar.blade.php`

**Purpose:** Month-at-a-glance calendar of scheduled trips

**Key Features:**

- **Calendar Grid:** Month view with `calendar-cell` day tiles and clickable trip chips (hover lift effect)
- **Month Navigation:** Previous/next controls driven by `$currentDate` (Carbon date)
- **Trip Chips:** Route + departure time per trip; links through to trip booking/seat map pages
- **Sidebar:** Same operator sidebar with a Trip Calendar active state

**Data Variables:**

- `$operator`, `$currentDate`, `$tripsByDate` (trips grouped per calendar day)

---

#### 31. `operator/all_bookings.blade.php`

**Purpose:** Operator-wide booking list with stats and filtering

**Key Features:**

- **Stats Cards:** Total, Today, Confirmed, Pending, Revenue Today
- **Status Filter Pills:** All, Confirmed, Pending, Cancelled, Expired
- **Search + Export:** keyword search and CSV export that preserves current filters (`operator.bookings.export`)
- **Bookings Table:** Reference, passenger, route, date/time, seat, amount, status badge, actions (View / Edit / Receipt)

**Data Variables:**

- `$operator`, `$bookings` (paginated), `$stats`

---

#### 32. `operator/booking_detail.blade.php`

**Purpose:** Single booking detail view for operators

**Key Features:**

- **Header Card:** Reference ID, status badge (confirmed/pending/cancelled/expired), seat number, amount, booked-on and held-until timestamps
- **Passenger Information Card:** Name, phone, ID/NRC
- **Trip Details Card:** Route visualisation, travel date, departure, bus/operator
- **Actions:** Back to All Bookings, Edit Booking, Print Receipt, status change forms

**Data Variables:**

- `$operator`, `$booking` (with `route`, `payment`, `ticket` relations)

---

#### 33. `operator/booking_edit.blade.php`

**Purpose:** Edit booking details (passenger info and status)

**Key Features:**

- **Form Card:** Passenger name, phone, ID number, status select, seat field; Update Booking button
- **Session Feedback:** Success banner and `$errors` list
- **Navigation:** Sidebar + header with "Edit Booking" title

**Data Variables:**

- `$operator`, `$booking`, `$errors`
- Session success messages

---

#### 34. `operator/booking_receipt.blade.php`

**Purpose:** Print-oriented payment receipt for a booking

**Key Features:**

- **Print-Friendly CSS:** No Tailwind dependency; plain CSS with `.status-*` badge colours and `@media print` rules
- **Content:** Header (operator company name), reference/status/booking date rows, trip details (route display, travel date, departure), fare summary, footer
- **Print Button:** `window.print()` (hidden when printing)

**Data Variables:**

- `$operator`, `$booking`

---

#### 35. `operator/seat_map.blade.php`

**Purpose:** Visual seat availability map for a specific route

**Key Features:**

- **Legend + Grid:** Seat rows rendered dynamically; available/occupied/selected states
- **Route Header:** origin → destination with date and time
- **Sidebar:** Operator portal navigation, Manage Trips active
- **Seat Selection JS:** Click-to-select with seat number capture

**Data Variables:**

- `$route` (derives `$operator = $route->operator`), `$bookedSeats`

---

#### 36. `operator/trip_bookings.blade.php`

**Purpose:** List bookings belonging to a single trip

**Key Features:**

- **Trip Header:** Trip ID, route, date/time, occupancy progress
- **Bookings Table:** Reference, passenger, seat, status badges (`.badge-confirmed/.badge-pending/.badge-cancelled`), amount, actions
- **Stats Footer:** Total passengers, confirmed, pending, revenue for the trip

**Data Variables:**

- `$operator`, `$tripData` (id, route, date, occupancy), `$bookings`

---

#### 37. `operator/buses.blade.php`

**Purpose:** Fleet (buses) management list

**Key Features:**

- **Stats Cards:** Total Buses, Active, Total Seats, Maintenance Due (7 days) with warning colour
- **Filters:** Status pills (All/Active/Inactive), class dropdown (Economy/Business/Luxury), keyword search
- **Buses Table/Cards:** Registration number, model, class badge, capacity, status toggle, Edit
- **Add Bus Drawer:** Slide-out form for registration, model, class, capacity, active flag

**Data Variables:**

- `$operator`, `$buses`, `$totalBuses`, `$activeBuses`, `$totalCapacity`, `$maintenanceDue`, `$errors`

---

#### 38. `operator/bus_detail.blade.php`

**Purpose:** Detailed view of a single bus

**Key Features:**

- **Header Card:** Registration number, active/inactive badge, model and class, Edit + Activate/Deactivate actions
- **Stats Grid:** Seat capacity, total trips, upcoming trips, utilisation
- **Tabbed Panels:** Trip history / maintenance / assignments via `.tab-panel` JS
- **Edit Drawer:** Update bus attributes

**Data Variables:**

- `$operator`, `$bus`, `$stats`, `$trips`

---

#### 39. `operator/drivers.blade.php`

**Purpose:** Driver management

**Key Features:**

- **Header:** "Driver Management" + Add Driver button
- **Stats/Table:** Driver name, licence number, phone, status
- **Add Driver Drawer:** Slide-out form (`#driver-drawer`) for name, licence, phone, active flag
- **Session Feedback:** Status banner and `$errors` block

**Data Variables:**

- `$operator`, `$drivers`, `$errors`
- Session status messages
#### 40. `operator/customers.blade.php`

**Purpose:** Operator customer directory

**Key Features:**

- **Stats Cards:** Total Customers, Total Bookings, New This Month, Repeat Customers
- **Search + Table:** keyword search; customer name/email/phone, bookings count, total spend, last trip, actions (View)

**Data Variables:**

- `$operator`, `$customers`, `$totalCustomers`, `$totalCustomerBookings`, `$newThisMonth`, `$repeatCustomers`

---

#### 41. `operator/customer_detail.blade.php`

**Purpose:** Single customer profile for an operator

**Key Features:**

- **Profile Card:** Avatar, full name, email, phone, member-since date
- **Summary Stats:** Total trips, total spend, last booking
- **Booking History Table:** Reference, route, date, amount, status
- **Back Link:** Returns to `operator.customers.index`

**Data Variables:**

- `$operator`, `$customer`, `$bookings`

---

#### 42. `operator/passenger_list.blade.php`

**Purpose:** Passenger manifest list page (searchable)

**Key Features:**

- **Stats:** Total passengers, today's passengers, upcoming trips
- **Filters:** Keyword search and route/date filters
- **Table:** Passenger name, seat, route, date, booking reference, status
- **Sidebar:** Operator navigation with Passenger List active

**Data Variables:**

- `$operator`, `$passengers`, `$stats`

---

#### 43. `operator/passenger_manifest.blade.php`

**Purpose:** Printable passenger manifest for a single trip

**Key Features:**

- **Print-Optimised:** `@media print` rules (15mm margins, compact type, safe table breaks); no Tailwind CDN
- **Trip Info Band:** Route, date, departure, bus, driver
- **Manifest Table:** Passenger, seat, gender/phone, paid status; signatures column
- **Print Button:** `window.print()` hidden when printing

**Data Variables:**

- `$route`, `$bookings` (or `$manifest` rows)

---

#### 44. `operator/printable_schedule.blade.php`

**Purpose:** Printable operator trip schedule (landscape report)

**Key Features:**

- **Print-Optimised:** Landscape page setup (`@page size: landscape`), compact table, print button
- **Summary Cards:** Total trips, Booked/Capacity, Available seats, Est. Revenue
- **Schedule Table:** Date, departure, route, bus, driver, booked, capacity, occupancy, fare, status
- **Header:** Operator company name and date range (`$dateFrom` - `$dateTo`)

**Data Variables:**

- `$operator`, `$dateFrom`, `$dateTo`, `$trips`, `$stats`

---

#### 45. `operator/fare_rules.blade.php`

**Purpose:** Fare rule configuration with tabbed interface

**Key Features:**

- **Tabs:** Active / Scheduled / Expired rule lists (`.tab-btn` / `.tab-content`)
- **Rule Cards:** Route pair, base fare, fee type (fixed/percentage), dates, status
- **Rule Drawer:** Slide-out form to create/edit rules with Tom Select route picker
- **Session Feedback:** Status and error banners

**Data Variables:**

- `$operator`, `$rules`, `$routes`, `$errors`

---

#### 46. `operator/promo_codes.blade.php`

**Purpose:** Promo/discount code management

**Key Features:**

- **Stats Strip:** Active codes, redemptions, revenue attributed
- **Promo Table:** Code, discount type/amount, usage limit, used count, validity window, active toggle
- **Promo Drawer:** Slide-out create/edit form (`#promo-drawer`) with code, type, value, limits, dates

**Data Variables:**

- `$operator`, `$promoCodes`, `$stats`, `$errors`

---

#### 47. `operator/route_templates.blade.php`

**Purpose:** Reusable route template management

**Key Features:**

- **Template Cards/Table:** Route pair, distance, duration, base fare, status, trips count (usage)
- **Template Drawer:** Create/edit form; includes a second **Create Trip drawer** (`#create-trip-drawer`) to schedule a trip directly from a template
- **Tom Select:** Searchable origin/destination pickers

**Data Variables:**

- `$operator`, `$templates`, `$routes`, `$buses`, `$errors`

---

#### 48. `operator/revenue.blade.php`

**Purpose:** Revenue analytics and reporting

**Key Features:**

- **KPI Row:** Total revenue, today, month, pending payouts
- **Charts:** CSS bar chart (`.chart-bar` transitions) for revenue trend; breakdown cards by route and payment method
- **Period Filter:** Date range selector (reloads page)
- **Export:** CSV export link preserving filters

**Data Variables:**

- `$operator`, `$summary`, `$dailyRevenue`, `$byRoute`, `$byMethod`

---

#### 49. `operator/audit_log.blade.php`

**Purpose:** Operator activity audit log

**Key Features:**

- **Filters:** Event type, date range, search box
- **Log Table:** Event badge, description, target resource, when (formatted + human-relative)
- **Pagination:** `$logs->links()` when paginated
- **Sidebar:** Operator navigation with Audit Log active

**Data Variables:**

- `$operator`, `$logs`, `$eventTypes`

---

#### 50. `operator/audit_log_detail.blade.php`

**Purpose:** Single audit log entry with before/after state

**Key Features:**

- **Entry Header:** Event badge + raw event code, description, timestamp
- **Metadata Card:** Operator, target (auditable type + id), IP address, user agent
- **State Changes:** Pretty-printed JSON of `old_values` and `new_values` in monospace blocks
- **Back Link:** Returns to `operator.audit-log.index`

**Data Variables:**

- `$operator`, `$log`

---

#### 51. `operator/profile.blade.php`

**Purpose:** Operator account and business profile management (tabbed)

**Key Features:**

- **Profile Header Card:** Business avatar, name and verification badge, email
- **Tabbed Interface:** Account Details (active) - business info form, Change Password - password update form; tab panels via `.tab-panel` JS
- **Account Details Form:** Company name, email, phone, TPIN (optional), business address (optional), Save Changes
- **Change Password Form:** Current / New / Confirm, Update Password
- **Status Messages:** Success flash and validation errors

**Data Variables:**

- `$operator` (company_name, email, phone_number, tpin, address, is_verified), `$errors`
- Session status messages
### E. Admin Portal Views

All admin views extend `layouts.admin`. They share a consistent pattern: a summary KPI strip, filter controls (status pills / GET forms), a data table with status badges and drill-down links, and pagination via `$collection->links()`.

#### 52. `admin/dashboard.blade.php`

**Purpose:** Admin overview dashboard with KPIs and charts

**Key Features:**

- **KPI Grid (6 cards):** Travelers (drill-down link), Operators, Revenue (mo/mo trend), Bookings (trend), Avg. Booking Value, Confirmed
- **Charts (Chart.js via CDN):** Revenue bar chart, Bookings line chart, Booking Status doughnut (12-month `$chartLabels`, `$revenueChartData`, `$bookingsChartData`, `$status_breakdown`)
- **Recent Bookings Table:** Reference, passenger, route, amount, status badge; links to `admin.bookings.show`
- **Quick Links:** Cards drill down to users/bookings listing pages

**Data Variables:**

- `$stats` (total_users, total_operators, total_revenue, revenue_trend, total_bookings, bookings_trend, avg_booking_value, confirmed_bookings), `$recent_bookings`
- `$chartLabels`, `$revenueChartData`, `$bookingsChartData`, `$status_breakdown`

---

#### 53. `admin/operators/index.blade.php`

**Purpose:** Operator management list (verify / search / filter)

**Key Features:**

- **Summary Strip:** Total, Verified, Pending, Fleet (buses)
- **Filters:** Status pills (All/Verified/Pending) + company/email/TPIN search
- **Operators Table:** Company (avatar + TPIN), email/phone, buses, routes (active), verification badge, joined date, actions (View, Verify when pending)

**Data Variables:**

- `$operators` (paginated), `$counts`, `$fleetTotal`, `$status`, `$search`

---

#### 54. `admin/operators/create.blade.php`

**Purpose:** Onboard a new bus operator

**Key Features:**

- **Two-Column Form:** Company name, email, phone, password + confirmation, TPIN, business type, registration number/date
- **Optional Fields:** Contact person name/title, address, description
- **Verification Checkbox:** "Mark as verified immediately" (skips pending queue)
- **Submit:** POST to `admin.operators.store`

**Data Variables:**

- `$errors`; old input values via `old()`

---

#### 55. `admin/operators/show.blade.php`

**Purpose:** Single operator profile for admins

**Key Features:**

- **Header Card:** Logo/avatar, company name, verified/pending badge, contact info, actions (Verify & Approve, Suspend, Delete with confirm)
- **Stats Cards:** Bookings, revenue, buses, routes (active)
- **Buses / Routes Tables:** Registration, class, capacity, status; origin → destination, fare, status
- **Recent Bookings Table:** Reference, passenger, route, amount, status; links to user/bookings detail pages

**Data Variables:**

- `$operator`, `$stats`, `$buses`, `$routes`, `$recentBookings`

---

#### 56. `admin/users/index.blade.php`

**Purpose:** Traveler (user) management

**Key Features:**

- **Summary Strip:** Total, Active, Suspended, New This Month
- **Filters:** Status pills (All/Active/Suspended) + search (name/email/phone)
- **Users Table:** Avatar + name, email/phone, bookings count, active/suspended badge, joined date, actions (View, Suspend/Activate)

**Data Variables:**

- `$users` (paginated), `$counts`, `$status`, `$search`

---

#### 57. `admin/users/show.blade.php`

**Purpose:** Single traveler profile for admins

**Key Features:**

- **Header Card:** Avatar, full name, active/suspended badge, email/phone, actions (Suspend / Activate, Remove with confirm)
- **Stats Cards:** Total bookings, confirmed, pending, cancelled, total spend
- **Account Details:** Full name, email, phone, preferred language, created date, account status
- **Booking History Table:** Reference, route, amount, status, booked date; links to `admin.bookings.show`

**Data Variables:**

- `$user`, `$stats`, `$bookings`

---

#### 58. `admin/bookings/index.blade.php`

**Purpose:** System-wide bookings list across all operators

**Key Features:**

- **Summary Strip:** Total, Confirmed, Pending, Cancelled, Revenue
- **Filters:** Status pills (All/Confirmed/Pending/Cancelled), operator dropdown, date range, search
- **Bookings Table:** Reference + seat, passenger, route, operator, amount, status badge, booked date

**Data Variables:**

- `$bookings` (paginated), `$stats`, `$operators`

---

#### 59. `admin/bookings/show.blade.php`

**Purpose:** Single booking detail (system-wide)

**Key Features:**

- **Header Card:** Reference ID, status badge, route + travel date, amount paid, seat
- **Passenger Card:** Name, phone, ID/NRC, seat
- **Trip Card:** Route, travel date, departure time, operator (link), bus, fare
- **Payment & Ticket Card:** Payment status/method/amount/reference/paid-at, issued ticket number

**Data Variables:**

- `$booking` (with `route`, `payment`, `ticket`, `user` relations)

---

#### 60. `admin/trips/index.blade.php`

**Purpose:** System-wide trip listing

**Key Features:**

- **Summary Strip:** Total, Today, Delayed, Active
- **Filters:** Operator dropdown, date, status, origin/destination search
- **Trips Table:** Route, operator, bus, date, departure, fare, computed status badge (arrived/departed/delayed/inactive/scheduled)

**Data Variables:**

- `$trips` (paginated), `$stats`, `$operators`

---

#### 61. `admin/payments/index.blade.php`

**Purpose:** System-wide payments monitoring

**Key Features:**

- **Summary Strip:** Successful, Failed, Pending, Collected Revenue
- **Filters:** Status, method, operator, date range, txn ref/booking search
- **Payments Table:** Transaction reference, booking (link), operator, method, amount, status badge, paid-at

**Data Variables:**

- `$payments` (paginated), `$stats`, `$operators`, `$methods`

---

#### 62. `admin/reports/index.blade.php`

**Purpose:** Reports and analytics with export

**Key Features:**

- **KPI Row:** Total Collected Revenue, Total Bookings, Registered Operators
- **Charts (Chart.js):** Revenue bar (12 months), Bookings line, Payment Methods doughnut, Booking Status doughnut
- **Data Tables:** Revenue by Operator, Revenue by Route
- **Export:** CSV export button (`admin.reports.export`)

**Data Variables:**

- `$totals`, `$labels`, `$revenueData`, `$bookingsData`, `$paymentMethods`, `$statusBreakdown`, `$revenueByOperator`, `$revenueByRoute`

---

#### 63. `admin/audit_log.blade.php`

**Purpose:** System-wide admin activity audit log

**Key Features:**

- **Summary Strip:** Total Actions, Actions Today, Unique Events, Last Activity
- **Filters:** Event type, from/to dates, description search
- **Log Table:** Event badge (link to detail), description, target (`auditable_type #id`), admin, when (formatted + relative)
- **Pagination:** `$logs->links()`

**Data Variables:**

- `$logs` (paginated), `$totalActions`, `$actionsToday`, `$uniqueEvents`, `$lastActivity`, `$eventTypes`

---

#### 64. `admin/audit_log_detail.blade.php`

**Purpose:** Single admin audit log entry

**Key Features:**

- **Entry Header:** Event badge + raw event code, description, timestamp
- **Metadata Card:** Admin, target, IP address, user agent
- **Recorded State:** Pretty-printed JSON of `old_values` / `new_values`
- **Back Link:** Returns to `admin.audit-log.index`

**Data Variables:**

- `$log`

---

#### 65. `admin/profile.blade.php`

**Purpose:** Admin account settings

**Key Features:**

- **Account Header:** Initial avatar, full name, email, phone
- **Account Details Form:** Full name, email, phone, preferred language (en/ny/bem/to/loz), Save Changes (PUT to `admin.profile.update`)
- **Change Password Form:** Current / New / Confirm, Update Password
- **Recent Activity Feed:** Iconed, human-relative list of `$activities` (login/logout/profile/operator/user/booking events) with empty state

**Data Variables:**

- `$admin`, `$activities`, `$errors`
- Session flash messages

---

### Orphaned Scaffold Views (not wired to any route or controller)

Eight additional files live under `resources/views/admin/` that are **not** part of the admin portal above and are not rendered by any controller `view()` call or reachable via any registered route:

- `admin/index.blade.php`, `admin/login.blade.php`, `admin/register.blade.php`, `admin/search-results.blade.php`, `admin/booking.blade.php`, `admin/payment.blade.php`, `admin/support.blade.php`, `admin/user-profile.blade.php`

They form a separate, self-contained prototype of the traveler-facing search → seat-select → checkout flow (dashboard, login, register, search results, seat selection, payment, support, profile), built on `layouts.admin` and referencing route names that don't exist anywhere in `routes/web.php` (`admin.search.results`, `admin.dashboard` as a search-results back-link, `admin.booking`, `admin.booking.hold`, `login.store`, `register.store`, `admin.profile.update`). Likely leftovers from the "resolved conflicts with admin-panel-views" merge — dead code that would 404/`RouteNotFoundException` if ever linked to. Not documented as live views in the sections above; flagged here so the file count (73 total, 65 live) reconciles.

---

## Design System

### Color Palette

The application uses a custom Material Design 3-inspired color system (each view defines its Tailwind color tokens; the layout shells add the full MD3 token set):

- **Primary:** `#00601f` / `#004614` (Green) - Main brand color
- **Primary Container:** `#197b30`
- **Secondary Container:** `#ff8921` (Orange) - Accent color
- **Tertiary Container:** `#d1200f` (Red) - Error/warning
- **Surface:** `#f9f9fc` - Background
- **On Surface:** `#1a1c1e` - Text
- **Error:** `#ba1a1a`

### Typography

- **Headline Font:** Manrope (headings and titles)
- **Body Font:** Inter (body text and labels)

### UI Components

- Rounded corners (`rounded-xl` / `rounded-2xl`), card-based layout with shadows, gradient buttons for primary actions, Material Symbols icons throughout
- **Operator portal:** Fixed left sidebar, sticky header, KPI stat cards, status badge pills, slide-out drawer forms (trips, buses, drivers, fares, promos, templates)
- **Admin portal:** Same sidebar/header shell via `layouts.admin`, KPI summary strips, Chart.js analytics (dashboard + reports), CSV export

---

## User Flow

**Traveler:**

```
landing_search → search_results → seat_selection → payment_ticket → history_page / ticket
booking_lookup → (signed-in: paginated dashboard) · (guest/anyone: reference-ID lookup) → ticket (QR boarding pass)
profile (Booking History tab) → cancel a booking
```

**Operator:**

```
auth/operator-login → operator/dashboard → manage_trips (→ trip_calendar, seat_map, trip_bookings)
all_bookings (→ booking_detail → booking_edit / booking_receipt)
buses (→ bus_detail) · drivers · customers (→ customer_detail) · passengers (→ passenger_manifest)
fare_rules · promo_codes · route_templates · revenue · audit_log → profile
```

**Admin:**

```
auth/admin-login → admin/dashboard → operators (index/create/show) · users (index/show)
bookings (index/show) · trips · payments · reports · audit_log (→ audit_log_detail) · profile
```

---

## TODO Items

1. ~~All views need to extend a `layouts.app` when available~~ - **Mostly complete**: 13 of 15 public traveler views now extend `layouts.app` (only `welcome.blade.php` and the legacy `_search_results.blade.php` remain self-contained)
2. ~~`seat_selection` needs dynamic seat map rendering from the Buses table~~ - **Complete**: uses `$route->bus->seat_capacity`; also now supports multi-seat group selection
3. ~~`payment_ticket` needs MTN/Airtel Money integration~~ - **Complete**: full provider selection UI with phone validation
4. ~~Operator dashboard and profile need dynamic data from the database~~ - **Complete**
5. ~~`manage_trips` needs proper form submission handling and validation~~ - **Complete**: submits to the operator trip store route
6. `auth/forgot-password` and `auth/reset-password` need email-sending functionality - Still pending
7. `_search_results.blade.php` - Legacy static-data backup; document or remove (file exists at `resources/views/_search_results.blade.php`)
8. `layouts/operator` shell is defined but the operator views duplicate the sidebar inline instead of extending it - candidate refactor
9. `contact_us.blade.php` contact form has no backend handler wired (form has no `action`/`method`)
10. Eight orphaned scaffold views under `resources/views/admin/` (see "Orphaned Scaffold Views" note in Section E) reference route names that don't exist in `routes/web.php` - candidate for removal or completion
11. The inventory of views in this document should be kept in sync as new views are added (currently 73 template files, 65 of them live/reachable)

---

*Last updated: September 2026*