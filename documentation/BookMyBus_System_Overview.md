# BookMyBus Zambia — System Overview

**A multi-tenant bus ticketing platform for the Zambian travel market**

---

## Table of Contents

1. [What is BookMyBus Zambia?](#1-what-is-bookmybus-zambia)
2. [Technology Stack](#2-technology-stack)
3. [System Architecture at a Glance](#3-system-architecture-at-a-glance)
4. [User Roles & Portals](#4-user-roles--portals)
5. [Feature Breakdown](#5-feature-breakdown)
6. [Database Design](#6-database-design)
7. [Service Layer](#7-service-layer)
8. [Notifications System](#8-notifications-system)
9. [Security](#9-security)
10. [Frontend & UI](#10-frontend--ui)
11. [Testing](#11-testing)
12. [Recent Improvements](#12-recent-improvements)
13. [Future Roadmap](#13-future-roadmap)

---

## 1. What is BookMyBus Zambia?

BookMyBus Zambia is a web-based bus ticketing platform that connects **travelers** with **bus operators** across Zambia. It digitizes the entire bus booking lifecycle — from searching available trips and selecting seats to paying via mobile money and receiving digital QR-coded tickets.

### The Problem It Solves

In Zambia, bus ticket booking is largely manual — travelers go to bus stations, queue at counters, and pay in cash. There is no centralized platform to:

- **Search and compare** trips across multiple operators
- **Book seats online** with real-time availability
- **Pay digitally** via mobile money (MTN, Airtel)
- **Receive digital tickets** instead of paper
- **Manage bookings** from a single dashboard

BookMyBus Zambia addresses all of these, while also giving operators and platform administrators tools to manage fleets, trips, bookings, revenue, and compliance — all from dedicated web portals.

### Key Differentiators

- **Mobile-money-first** payment integration (MTN MoMo, Airtel Money) — the dominant payment methods in Zambia
- **Multi-tenant architecture** — each operator manages only their own data
- **Three-role system** — Travelers, Operators, and Admin, each with separate authentication
- **QR-coded digital tickets** for validation at terminals
- **Local context** — Zambian cities (Lusaka, Kitwe, Ndola, Livingstone, etc.), ZMW currency, local phone formats

---

## 2. Technology Stack

| Layer | Technology |
|-------|-----------|
| **Backend Framework** | Laravel 12 (PHP 8.2+) |
| **Database** | MySQL (via XAMPP) |
| **Frontend** | Blade templates + Tailwind CSS (Play CDN) |
| **JavaScript** | Vanilla JS, Chart.js (analytics), Tom Select (search dropdowns) |
| **Icons** | Google Material Symbols Outlined |
| **Fonts** | Manrope (headlines), Inter (body) |
| **API Auth** | Laravel Sanctum (token-based for mobile app consumption) |
| **Web Auth** | Laravel session guards (3 separate guards) |
| **Date/Time** | Carbon |
| **Location** | stevebauman/location (IP-based city detection) |
| **Testing** | PHPUnit |

### Project Scale

| Metric | Count |
|--------|-------|
| Controllers | 38 |
| Models | 15 |
| Database migrations | 26 |
| Blade views | 64 |
| Registered routes | ~165 |
| Feature tests | 7 |

---

## 3. System Architecture at a Glance

```
                         ┌──────────────────────────┐
                         │     BookMyBus Zambia      │
                         └───────────┬──────────────┘
                                     │
                    ┌────────────────┼────────────────┐
                    │                │                │
              ┌─────▼─────┐  ┌──────▼──────┐  ┌──────▼──────┐
              │ Traveler  │  │  Operator   │  │   Admin     │
              │ Portal    │  │  Portal     │  │   Portal    │
              │ Guard:web │  │Guard:operator│  │ Guard:admin │
              │ /login    │  │/operator/   │  │ /admin/     │
              │ /search   │  │  login      │  │   login     │
              │ /booking  │  │/operator    │  │ /admin      │
              │ /payment  │  │/operator/.. │  │ /admin/..   │
              └─────┬─────┘  └──────┬──────┘  └──────┬──────┘
                    │               │                │
                    └───────────────┼────────────────┘
                                    │
                         ┌──────────▼──────────┐
                         │    REST API Layer    │
                         │  (Sanctum-protected) │
                         └──────────┬──────────┘
                                    │
                    ┌───────────────┼───────────────┐
                    │               │               │
              ┌─────▼─────┐  ┌──────▼──────┐  ┌─────▼─────┐
              │  Service  │  │  Eloquent   │  │    SMS    │
              │  Layer    │  │  Models     │  │ Notif.    │
              │ Payment   │  │ 15 Models   │  │ Africa's  │
              │ Fare Calc │  │ Soft Deletes│  │ Talking   │
              │ Refund    │  │             │  │ (or sim.) │
              │ Audit     │  │             │  │           │
              └─────┬─────┘  └──────┬──────┘  └───────────┘
                    └───────┬───────┘
                            │
                    ┌───────▼───────┐
                    │    MySQL      │
                    │  (26 tables)  │
                    └───────────────┘
```

### Architecture Pattern

The system follows a **layered MVC architecture**:

- **Routes** → map URLs to controllers (web.php for sessions, api.php for Sanctum tokens)
- **Controllers** → handle HTTP requests, validate input, call services, return views/responses
- **Services** → encapsulate business logic (payment processing, fare calculation, audit logging)
- **Models** → Eloquent ORM representing database entities with relationships
- **Views** → Blade templates rendered server-side with Tailwind CSS


---

## 4. User Roles & Portals

The system uses **three separate authentication guards**, each backed by a different model and session driver. This means a traveler, operator, and admin can be logged in simultaneously without session conflicts.

### Guard Configuration

| Guard | Provider Model | Session | Login URL | Middleware |
|-------|---------------|---------|-----------|------------|
| `web` | `User` (role=traveler) | `web` | `/login` | `auth` |
| `sanctum` | `User` (role=traveler/admin) | — | `/api/auth/login` | `auth:sanctum` |
| `operator` | `Operator` | `operator` | `/operator/login` | `auth:operator` |
| `admin` | `User` (role=admin) | `admin` | `/admin/login` | `auth:admin` |

### Portal Overview

| Portal | Users | Purpose |
|--------|-------|---------|
| **Traveler** | Public + registered travelers | Search trips, book seats, pay, view tickets & booking history |
| **Operator** | Bus company staff (verified) | Manage fleet, trips, bookings, drivers, revenue, fare rules, promo codes |
| **Admin** | Platform administrators | Verify/suspend operators, manage travelers, view all bookings/payments/trips, generate reports, audit log |

---

## 5. Feature Breakdown

### 5.1 Traveler Portal

| Feature | Description |
|---------|-------------|
| **Trip Search** | Search by origin, destination, travel date, and passenger count. IP-based city detection for personalized route suggestions. |
| **Search Results** | Filter by time of day, price range, and operator. Shows departure/arrival times, fare, available seats, and operator ratings. |
| **Seat Selection** | Interactive visual seat map showing available, booked, and held seats. Dynamic rendering based on bus capacity. |
| **Booking** | Creates a booking with a 10-minute hold on selected seats. Auto-generated reference IDs (e.g., `BMZ-XXXXXX`). |
| **Payment** | Mobile money payment flow (MTN Money, Airtel Money) with phone number validation. Simulated gateway for development. |
| **Digital Ticket** | QR-coded ticket issued on successful payment. Ticket contains booking details, route, seat, and QR code for validation. |
| **Booking Lookup** | Find bookings by reference ID or phone number — no login required. |
| **Booking History** | View all past bookings with status (confirmed, pending, cancelled). |
| **Profile** | Manage account details, change password, set preferred language. |
| **Cancellation** | Cancel bookings with refund eligibility based on cancellation rules. |
| **Promo Codes** | Apply promotional discount codes at checkout for reduced fares. |

### 5.2 Operator Portal

| Feature | Description |
|---------|-------------|
| **Dashboard** | Aggregate stats — total trips, bookings, revenue, occupancy rates. 30-day revenue/bookings charts. Upcoming departures. Quick actions. |
| **Trip Management** | Full CRUD for trips. Create, edit, cancel, update status (scheduled → delayed → departed → arrived). Seat maps, occupancy. CSV export. Trip calendar. |
| **Booking Management** | View all bookings. Filter by status, date, search. Edit, board, refund, add notes. Bulk actions. CSV export. Printable receipts. |
| **Fleet (Bus) Management** | Manage buses — registration, model, seat capacity, amenities. Toggle active/inactive. Maintenance tracking. |
| **Driver Management** | Add/edit/delete drivers with license details. Drawer-based quick-add interface. |
| **Customer Management** | View customer profiles with booking history, total spent, and contact details. |
| **Revenue & Reports** | Revenue by period, payment method, and route. Daily revenue heatmap. Top routes. Transaction log. |
| **Fare Rules** | Configure service fees and cancellation rules. Live fare preview calculator. |
| **Promo Codes** | Create promo codes with discount type, max discount, min booking, validity, usage limits. |
| **Route Templates** | Save reusable route templates and generate trips — including bulk generation. |
| **Passenger Manifest** | View/manage passenger lists per trip. Bulk check-in. Export. Printable list. |
| **Audit Log** | Track all operator actions with timestamps, IP addresses, and old/new values. |
| **Profile & Settings** | Manage company details, TPIN, contact info, and password. |

### 5.3 Admin Portal

| Feature | Description |
|---------|-------------|
| **Dashboard** | System-wide KPIs — travelers, operators, bookings, revenue, avg booking value. Month-over-month trends. 30-day charts. Status breakdown. Payment channel split. Top operators. Recent activity. |
| **Operator Management** | List/filter/search operators. View profiles with fleet stats and revenue. Verify, suspend, delete (soft). Create new operators. |
| **Traveler Management** | List/filter/search travelers. View profiles with booking history. Suspend/activate, delete (soft). |
| **Bookings (System-Wide)** | View all bookings across all operators. Filter by status, operator, date. Full booking details. |
| **Payments (System-Wide)** | View all payment transactions. Filter by status, method, operator, date range. |
| **Trips (System-Wide)** | View all trips across all operators. Filter by operator, date, status. |
| **Reports & Analytics** | 12-month trends. Revenue by operator/route. Payment method distribution. CSV export. Live filtering. |
| **Audit Log** | Complete audit trail of all admin actions with old/new values, IP, user agent. |
| **Profile & Settings** | Manage admin account, password, preferred language (English, Nyanja, Bemba, Tonga, Lozi). |

### 5.4 REST API (for Mobile App)

A complete REST API is available under `/api/` for future mobile app consumption, protected by Laravel Sanctum:

| Endpoint Group | Key Endpoints |
|----------------|--------------|
| **Auth** | `POST /api/auth/register`, `POST /api/auth/login`, `GET /api/auth/me`, `POST /api/auth/logout` |
| **Operator Auth** | `POST /api/auth/operator/register`, `POST /api/auth/operator/login` |
| **Routes/Search** | `GET /api/routes/search`, `GET /api/routes/{id}` |
| **Bookings** | `GET /api/bookings`, `POST /api/bookings/hold`, `GET /api/bookings/{ref}`, `PATCH /api/bookings/{id}/cancel` |
| **Payments** | `POST /api/payments/initiate`, `GET /api/payments/status/{bookingId}`, `POST /api/payments/callback` |
| **Tickets** | `GET /api/tickets`, `GET /api/tickets/{qrCode}` |
| **Operator Buses** | `GET/POST/PATCH/DELETE /api/operator/buses` |
| **Operator Routes** | `GET/POST/PATCH/DELETE /api/operator/routes` |
| **Admin** | `GET /api/admin/dashboard`, `GET /api/admin/operators`, `GET /api/admin/users`, `GET /api/admin/bookings` |

---

## 6. Database Design

### Entity Relationship Diagram

```
┌──────────┐       ┌───────────┐       ┌──────────┐
│  User    │       │ Operator  │       │   Bus    │
│(traveler)│       │ (company) │       │(vehicle) │
└────┬─────┘       └─────┬─────┘       └────┬─────┘
     │ hasMany           │ hasMany          │ hasMany
     ▼                   ▼                  │
┌──────────┐       ┌───────────┐            │
│ Booking  │──────▶│  Route    │◀───────────┘
│          │       │ (trip)    │
└────┬─────┘       └─────┬─────┘
     │ hasOne            │ hasMany
     ▼                   ▼
┌──────────┐       ┌───────────┐
│ Payment  │       │ Booking   │
│ (txn)    │       └───────────┘
└──────────┘
     │ hasOne
     ▼
┌──────────┐
│ Ticket   │
│(QR code) │
└──────────┘
```

### Core Tables (15 models, 26 migrations)

| Table | Purpose | Key Fields |
|-------|---------|------------|
| `users` | Traveler & admin accounts | full_name, email, phone_number, password, role, is_active, preferred_language |
| `operators` | Bus company accounts | company_name, email, phone_number, password, tpin, is_verified, verified_at, verified_by |
| `buses` | Operator fleet | operator_id, registration_number, model, seat_capacity, amenities, is_active |
| `routes` | Trip definitions | operator_id, bus_id, origin, destination, travel_date, departure_time, fare, is_active |
| `bookings` | Seat reservations | user_id, route_id, reference_id, seat_number, passenger_name, passenger_phone, status, amount, held_until |
| `payments` | Payment transactions | booking_id, transaction_reference, payment_method, amount, status, paid_at |
| `tickets` | Digital tickets | booking_id, user_id, qr_code, ticket_number, status, issued_at, used_at |
| `drivers` | Driver profiles | operator_id, full_name, phone_number, license_number, license_expiry_date, is_active |
| `promo_codes` | Promotional discounts | operator_id, code, discount_type, discount_value, max_discount, min_booking, usage_limit |
| `cancellation_rules` | Refund policies | operator_id, window_hours, refund_percentage |
| `service_fees` | Booking fees | operator_id, fee_type, fee_value, applies_to |
| `route_templates` | Reusable route configs | operator_id, name, origin, destination, departure_time, base_fare, bus_id |
| `admin_audit_logs` | Admin action trail | admin_id, event, description, auditable_type, auditable_id, old_values, new_values, ip_address |
| `operator_audit_logs` | Operator action trail | operator_id, event, description, auditable_type, auditable_id, old_values, new_values, ip_address |
| `notification_logs` | SMS/notification records | notifiable_type, notifiable_id, channel, message, status |

### Soft Deletes

The following models use soft deletes, preserving records for audit trails:
`User`, `Operator`, `Bus`, `Route`, `Booking`

---

## 7. Service Layer

The application encapsulates business logic in dedicated service classes, keeping controllers thin:

| Service | Responsibility |
|---------|---------------|
| **PaymentService** | Initiates and processes payments via the mobile money gateway. Handles status checks and callbacks. |
| **FareCalculationService** | Calculates total booking fares including base fare, service fees, promo discounts, and cancellation rules. |
| **RefundCalculationService** | Determines refund eligibility and calculates refund amounts based on cancellation rules and time windows. |
| **AdminAuditService** | Logs admin actions with old/new values and metadata. |
| **OperatorAuditService** | Logs operator actions with old/new values and metadata. |

### Payment Gateway Abstraction

Mobile money integration is abstracted behind a `GatewayInterface`:

```
App\Services\MobileMoney\GatewayInterface
    └── App\Services\MobileMoney\SimulatedGateway  (development)
    └── (future) MTN MoMo Gateway
    └── (future) Airtel Money Gateway
```

This allows swapping the simulated gateway for real MTN/Airtel SDKs without changing any controller or service code.

### Messaging Gateway Abstraction

SMS/WhatsApp notifications are abstracted behind a `MessagingGatewayInterface`:

```
App\Services\Messaging\MessagingGatewayInterface
    └── App\Services\Messaging\SimulatedMessagingGateway  (development)
    └── App\Services\Messaging\AfricasTalkingGateway  (production-ready)
```

---

## 8. Notifications System

The system includes a notification layer for sending booking-related alerts to travelers:

| Notification | Trigger | Channel |
|-------------|---------|---------|
| **TicketIssued** | Payment confirmed → ticket generated | SMS, WhatsApp |
| **BookingCancelled** | Booking cancelled (by traveler or operator) | SMS |
| **DepartureReminder** | Trip departure approaching | SMS |
| **TripStatusChanged** | Trip delayed, departed, or arrived | SMS |

### Notification Channels

- **SMS Channel** — Custom channel implementation using the messaging gateway
- **WhatsApp Channel** — Custom channel implementation using the messaging gateway

All sent notifications are logged in the `notification_logs` table for delivery tracking.

---

## 9. Security

| Measure | Implementation |
|---------|---------------|
| **Multi-guard authentication** | Separate sessions for travelers, operators, and admins prevent cross-portal session conflicts |
| **Auth scoping** | All operator/admin controllers filter data by the authenticated user's ID — operators cannot access other operators' data |
| **Password hashing** | Both `User` and `Operator` models use Laravel's `hashed` cast for automatic bcrypt hashing |
| **CSRF protection** | Enabled on all POST/PUT/DELETE routes (Laravel default `@csrf` in forms) |
| **Role middleware** | Custom `RoleMiddleware` verifies admin role for admin routes |
| **Soft deletes** | Deleted records are preserved (not hard-deleted) for data integrity and audit trails |
| **Route model binding** | Explicit scoping prevents ID enumeration attacks |
| **Sanctum API tokens** | API endpoints require valid bearer tokens; tokens are scoped per user |

---

## 10. Frontend & UI

### Design System

The UI uses a **Material Design-inspired** color system with Tailwind CSS:

| Color Role | Hex | Usage |
|-----------|-----|-------|
| Primary | `#00601f` (traveler) / `#004614` (operator) | Brand green, primary buttons, active states |
| Primary Container | `#197b30` | Hover states, secondary buttons |
| Secondary | `#954a00` / `#ff8921` | Orange accents (operator portal) |
| Tertiary | `#a80800` / `#7c0400` | Warnings, pending states |
| Error | `#ba1a1a` | Errors, destructive actions, cancelled status |
| Surface | `#f9f9fc` | Background |
| Outline Variant | `#bfcaba` | Borders, dividers |

### Layout Architecture

The system uses **4 Blade layouts**, each serving a distinct portal type:

| Layout | Used By | Key Features |
|--------|---------|-------------|
| `layouts.app` | 13 traveler-facing views | Top nav bar with mobile hamburger toggle, footer, skip-to-content link, focus-visible styles |
| `layouts.operator` | 21 operator views | Fixed sidebar (collapsible on mobile), header with page title + action buttons, flash messages |
| `layouts.admin` | 14 admin views | Same sidebar pattern as operator, with admin-specific navigation |
| `layouts.auth` | 6 auth/login views | Minimal centered-card layout, no sidebar/nav, gradient backgrounds |

### Accessibility Features

- **Skip-to-content** links on all layouts (keyboard/screen-reader users)
- **`aria-current="page"`** on active navigation links
- **`sr-only` labels** on all filter inputs and search boxes
- **`:focus-visible`** outline styles on all interactive controls
- **`role="status"`/`role="alert"`** on flash messages
- **Chart accessibility** — `role="img"` + `aria-label` on all Chart.js canvases with visually-hidden data tables
- **`aria-label`** on icon-only buttons (menu toggle, account icon)

### Responsive Design

- **Mobile sidebar toggle** on operator and admin layouts (hamburger button + slide-in sidebar + backdrop overlay)
- **Responsive padding** — `p-4 md:p-8` on all layouts
- **Responsive grids** — KPI cards use `grid-cols-2 sm:grid-cols-4 lg:grid-cols-5/6` patterns
- **Horizontal scroll** on tables via `overflow-x-auto` wrappers

---

## 11. Testing

| Test File | What It Tests |
|-----------|--------------|
| `AuthTest` | Traveler login/logout flow |
| `BookingLookupTest` | Booking lookup by reference ID and phone number |
| `BookingCancelTest` | Booking cancellation flow |
| `TicketTest` | Ticket generation and QR code validation |
| `NotificationTest` | SMS notification delivery |
| `AdminProfileTest` | Admin profile update and password change |
| `ExampleTest` | Default Laravel example (unit + feature) |

Tests can be run with:

```bash
php artisan test
```

---

## 12. Recent Improvements

### Admin Portal UI Polish

- **Responsiveness**: Added mobile sidebar toggle with hamburger button and slide-in sidebar to both admin and operator layouts
- **Accessibility**: Added skip-to-content links, focus-visible styles, aria-current on nav links, screen-reader labels on all filter controls, chart accessibility with aria-labels and hidden data tables
- **Consistency**: Standardized pagination footers ("Showing X–Y of Z"), table header backgrounds, currency formatting (2 decimals), and flash message roles across all admin views
- **Bug fix**: Added missing `admin.operators.create` and `admin.operators.store` routes — the "Add Operator" page existed but was unreachable

### Full Project Layout Refactoring

- **Operator views**: Refactored 21 standalone HTML views to use a shared `@extends('layouts.operator')` layout, eliminating ~400KB of duplicated boilerplate
- **Auth views**: Created `layouts.auth.blade.php` and refactored 6 auth views to use it
- **Traveler views**: Refactored 12 standalone views to use `@extends('layouts.app')`
- **Operator nav**: Added `aria-current="page"` to all 12 operator nav links, and added scroll overflow for the 14-item sidebar
- **Cleanup**: Deleted 2 unreferenced legacy files (`welcome.blade.php`, `_search_results.blade.php`)
- **Script placement**: Moved all inline scripts to `@push('scripts')` and external scripts to `@push('head')` for proper load ordering

### Impact

| Metric | Before | After |
|--------|--------|-------|
| Views using shared layouts | 1 (`payment_ticket`) | 54 (all non-print views) |
| Standalone HTML views | 53 | 3 (print-optimized only) |
| Layouts available | 2 (`app`, `operator` — operator unused) | 4 (`app`, `operator`, `admin`, `auth`) |
| Duplicated HTML boilerplate | ~700KB across 53 views | 0 — all inherited from 4 layouts |

---

## 13. Future Roadmap

| Priority | Enhancement | Description |
|----------|-------------|-------------|
| **High** | Real mobile money integration | Replace `SimulatedGateway` with actual MTN MoMo and Airtel Money SDKs |
| **High** | Email notifications | Add email channel alongside SMS for booking confirmations |
| **Medium** | Mobile app | Build a React Native / Flutter app consuming the existing REST API |
| **Medium** | QR code scanning | Terminal staff scan traveler QR codes for boarding validation |
| **Medium** | Payment refunds | Automated refund processing through the payment gateway |
| **Medium** | Multi-language support | `preferred_language` field exists on User model — add translation files for Nyanja, Bemba, Tonga, Lozi |
| **Low** | Advanced analytics | Predictive demand forecasting, route performance scoring |
| **Low** | Operator ratings | Traveler-submitted ratings and reviews for operators |
| **Low** | Loyalty program | Frequent traveler rewards and points system |

---

## Appendix: Route Summary

| Route Group | Count | Auth |
|-------------|-------|------|
| Public (landing, search, static pages) | ~12 | None |
| Traveler auth & profile | ~10 | Guest / `auth` |
| Traveler booking flow | ~8 | `auth` |
| Operator portal | ~76 | `auth:operator` |
| Admin portal | ~27 | `auth:admin` |
| REST API (`/api/`) | ~33 | `auth:sanctum` |
| **Total** | **~165** | |

---

*Document generated for BookMyBus Zambia — September 2026*
