# BookMyBus Zambia — System Architecture

## Overview

BookMyBus Zambia is a multi-tenant bus ticketing system built on **Laravel 12**. It supports two primary user roles:

- **Travelers** – Browse routes, book seats, pay via mobile money
- **Operators** – Manage buses, routes/trips, and monitor bookings

A future **Admin** role will oversee platform-wide verification and management of operators.

---

## 1. Authentication Architecture (Multi-Guard)

Laravel's authentication system is extended with multiple guards, each backed by a separate Eloquent model and session driver.

```
┌─────────────────────────────────────────────────────────┐
│                    WEB (Traveler)                        │
│  Guard: web                                              │
│  Provider: users table → App\Models\User                 │
│  Login: /login                                           │
│  Session: web                                            │
│  Middleware: auth                                        │
├─────────────────────────────────────────────────────────┤
│                  OPERATOR                                │
│  Guard: operator                                         │
│  Provider: operators table → App\Models\Operator         │
│  Login: /operator/login                                  │
│  Session: operator                                       │
│  Middleware: auth:operator                               │
├─────────────────────────────────────────────────────────┤
│                  ADMIN (Planned)                          │
│  Guard: admin                                            │
│  Provider: users table → App\Models\User (role='admin')  │
│  Login: /admin/login                                     │
│  Session: admin                                          │
│  Middleware: auth:admin                                  │
└─────────────────────────────────────────────────────────┘
```

### Auth Controllers

| Namespace | Files |
|-----------|-------|
| `App\Http\Controllers\Auth\` | `LoginController`, `RegisterController` (disabled), `PasswordResetController` (disabled) |
| `App\Http\Controllers\Auth\Operator\` | `LoginController` |
| `App\Http\Controllers\Auth\Admin\` | `LoginController` (disabled) |

All operator auth routes are wrapped in `guest:operator` middleware to prevent authenticated operators from seeing the login form.

---

## 2. Controller Layer

### Traveler-Facing Controllers

| Controller | Role | Key Methods |
|------------|------|-------------|
| `LandingController` | Public | `index()` – homepage, `search()` – trip search |
| `BookingController` | Auth | `showSeats()`, `store()`, `paymentTicket()`, `processPayment()`, `success()`, `customerLookupView()`, `customerLookup()` |
| `ProfileController` | Auth | `index()`, `update()`, `updatePassword()` |

### Operator Portal Controllers (all scoped to the authenticated operator)

| Controller | Key Methods |
|------------|-------------|
| `DashboardController` | `index()` – aggregate stats (total trips, bookings, revenue) |
| `TripManagementController` | `index()`, `store()`, `show()`, `update()`, `cancel()`, `updateStatus()`, `export()`, `stats()`, `upcoming()`, `seatMap()`, `occupancy()` |
| `BookingManagementController` | `index()`, `show()`, `export()`, `tripBookings()` |
| `ProfileController` | `index()`, `update()`, `updatePassword()` |

### Auth Scoping Pattern

Operator controllers scope queries to the authenticated operator. Example pattern:

```php
public function index()
{
    $operator = Auth::guard('operator')->user();
    $trips = Route::where('operator_id', $operator->id)->get();
    // ...
}
```

This ensures operators only see their own data (buses, routes, bookings via their buses).

---

## 3. Model Layer & Relationships

```
┌──────────┐    1:N    ┌────────┐    1:N    ┌───────┐
│ Operator │──────────▶│  Bus   │──────────▶│ Route │
└──────────┘           └────────┘           └───┬───┘
                                                │ 1:N
                                                ▼
┌──────────┐    1:N    ┌─────────┐    1:N    ┌─────────┐
│   User   │──────────▶│ Booking │──────────▶│ Payment │
└──────────┘           └────┬────┘           └─────────┘
                            │ 1:1
                            ▼
                       ┌─────────┐
                       │  Ticket │
                       └─────────┘
```

### Key Model Details

**Operator** (`App\Models\Operator`)
- Extends `Authenticatable` (can log in via `auth:operator` guard)
- Has `buses()`, `routes()`, `verifiedBy()` relationships
- `isVerified()` helper checks operator approval status

**User** (`App\Models\User`)
- Extends `Authenticatable` (traveler login)
- Has `bookings()`, `tickets()` relationships
- Uses `HasApiTokens` (Sanctum) for potential API access
- `isAdmin()`, `isTraveler()` role helpers

**Bus** (`App\Models\Bus`)
- `amenities` cast as JSON array
- `hasAmenity(string)` helper for amenity checks
- Has `routes()` relationship

**Route** (`App\Models\Route`)
- `scopeSearch()` – filters by origin, destination, travel_date
- `bookedSeats()` – returns array of occupied seat numbers for the route
- `availableSeats()` – computes available seats from bus capacity minus booked
- `availableSeatsCount()` – returns count of open seats

**Booking** (`App\Models\Booking`)
- Auto-generates `reference_id` on creation (format: `BMZ-XXXXXX`)
- Auto-sets `held_until` to `now() + 10 minutes`
- `isExpired()` / `isConfirmed()` / `cancel()` helpers
- Statuses: `pending`, `confirmed`, `cancelled`

**Payment** (`App\Models\Payment`)
- `markSuccessful()` – updates status, stores transaction ref, confirms linked booking
- `markFailed()` – updates status with failure details
- `isSuccessful()` helper

**Ticket** (`App\Models\Ticket`)
- Auto-generates `qr_code` on creation (UUID-based)
- Auto-sets `issued_at` timestamp
- `markAsUsed()` / `isValid()` helpers

---

## 4. Payment Service Layer

### Architecture

```
PaymentService
    │
    ├── uses ──▶ SimulatedGateway (implements GatewayInterface)
    │                │
    │                ├── supports: 'mtn', 'airtel'
    │                ├── phone validation by prefix
    │                └── ~90% simulated success rate
    │
    ├── creates ──▶ Payment record
    └── updates ──▶ Booking.status
```

### GatewayInterface

```php
interface GatewayInterface {
    public function charge(string $phoneNumber, float $amount, string $reference): array;
    public function getProviderName(): string;
    public function validatePhoneNumber(string $phoneNumber): bool;
}
```

### SimulatedGateway

- **Provider mapping:**
  - `mtn` → prefixes `096`, `076` → "MTN MoMo"
  - `airtel` → prefixes `097`, `077` → "Airtel Money"
- Phone validation: exactly 10 digits, must start with provider prefix
- Charge simulation: 1-3 second delay, ~90% success rate
- Factory: `SimulatedGateway::forProvider('mtn')`

### Payment Flow

```
Booking (pending)
    ↓ Traveler submits phone + provider
PaymentService::processMobileMoney()
    ↓ Validate phone format
    ↓ Create Payment record (status: pending)
    ↓ Gateway::charge()
    ├── Success → Payment::markSuccessful() → Booking: confirmed → Ticket issued
    └── Failure → Payment::markFailed() → Booking remains pending
```

---

## 5. Route Design & Conventions

### Route group structure (from `routes/web.php`)

```php
// Public
Route::get('/', ...)->name('home');
Route::get('/search', ...)->name('trips.search');

// Traveler Guest
Route::middleware('guest')->group(...);

// Operator Guest
Route::middleware('guest:operator')->group(...);

// Traveler Auth
Route::middleware('auth')->group(...);

// Operator Auth
Route::prefix('operator')->name('operator.')->middleware('auth:operator')->group(...);
```

### Important ordering constraint

Static routes like `/trips/export`, `/trips/stats`, `/trips/upcoming` must be defined **before** the wildcard `/trips/{trip}` route to avoid route model binding conflicts.

```php
// Static routes (BEFORE wildcard)
Route::get('/trips/export', ...);
Route::get('/trips/stats', ...);
Route::get('/trips/upcoming', ...);

// Wildcard routes (AFTER static)
Route::get('/trips/{trip}', ...);
Route::put('/trips/{trip}', ...);
```

---

## 6. Database Schema (Migrations)

### Migration Files
Located in `database/migrations/`. Key tables and their purposes:

| # | Table | Purpose |
|---|-------|---------|
| 1 | `users` | Traveler accounts |
| 2 | `operators` | Bus operator company accounts |
| 3 | `personal_access_tokens` | Sanctum API token storage |
| 4 | `buses` | Operator's bus fleet |
| 5 | `routes` | Trip definitions (origin, destination, time, fare, bus assignment) |
| 6 | `bookings` | Booking records with seat, status, and reference |
| 7 | `payments` | Payment transactions linked to bookings |
| 8 | `tickets` | Digital tickets issued per confirmed booking |

### Soft Deletes
The following models use `SoftDeletes`: `User`, `Operator`, `Bus`, `Route`, `Booking`

---

## 7. Frontend Structure

### Blade View Layout

```
resources/views/
├── layouts/
│   └── app.blade.php          # Main layout with nav, footer, yield('content')
├── operator/
│   ├── dashboard.blade.php
│   ├── all_bookings.blade.php
│   ├── booking_detail.blade.php
│   ├── trip_bookings.blade.php
│   ├── manage_trips.blade.php
│   ├── profile.blade.php
│   └── ...
├── auth/
│   ├── login.blade.php
│   └── operator-login.blade.php
├── booking_lookup.blade.php
├── payment_ticket.blade.php
├── manage_trips.blade.php
└── ...
```

### UI Mockups (separate, non-Laravel HTML)

Located under `BookMyBus_Zambia_UI/` — these are static HTML/CSS prototypes for reference:
- `landing_search/` – Homepage search UI
- `search_results/` – Trip results listing
- `seat_selection/` – Interactive seat map
- `payment_ticket/` – Payment and ticket view
- `operator_dashboard/` – Operator stats overview
- `operator_profile/` – Operator profile editor
- `trip_management/` – Trip CRUD interface
- `history_page/` – Booking history lookup

---

## 8. Key Development Decisions

1. **Separate Operator model vs role-based User** – Operators have different fields (company_name, tpin, verification status) and authentication guards, justifying a separate model.

2. **Simulated payment gateway** – Used during development to avoid real mobile money transactions. Swap with a real SDK (e.g., MTN MoMo API, Airtel Money API) by implementing `GatewayInterface`.

3. **10-minute booking hold** – `held_until` timestamp prevents seats from being held indefinitely by abandoned bookings.

4. **Static routes before wildcard** – Laravel matches the first matching route; static paths like `/trips/export` must precede `{trip}` to prevent "export" from being treated as a trip ID.

5. **Soft deletes for audit trail** – Deleted records are preserved in the database and can be restored if needed.

---

## 9. Security

- **Auth scoping** – All operator controllers filter by `Auth::guard('operator')->user()->id`
- **Route model binding** – Where possible, explicit scoping prevents operators from accessing other operators' data
- **Password hashing** – Both `User` and `Operator` models hash passwords via `'password' => 'hashed'` cast
- **CSRF protection** – Enabled on all POST/PUT/DELETE routes (Laravel default)
- **Registration disabled** – Traveler registration routes are commented out; accounts created manually or via future admin panel

---

## 10. Future Enhancements

- Admin panel for operator verification and platform oversight
- Real mobile money SDK integration (MTN MoMo API, Airtel Money API)
- Email/SMS notifications for booking confirmation
- QR code scanning for ticket validation at bus terminals
- API endpoints for mobile app consumption (Sanctum tokens already configured)
- Multi-language support (preferred_language field exists on User model)
- Payment refund processing
- Advanced reporting and analytics dashboard