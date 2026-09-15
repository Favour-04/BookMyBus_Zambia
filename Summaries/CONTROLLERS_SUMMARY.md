# Controllers Summary

This document provides a comprehensive summary of all controllers in the BookMyBus Zambia application.

## Overview

The application follows a standard Laravel MVC structure with controllers organized into the following categories:

- **Base Controller**: Abstract base class for all controllers
- **Web Controllers**: Handle traditional web page requests (traveler-facing)
- **Admin Controllers**: Handle platform-wide management for the admin web portal
- **API Controllers**: Handle RESTful API requests for mobile/web clients
- **Operator Controllers**: Handle operator-specific functionality
- **Auth Controllers**: Handle authentication for web-based login/logout

---

## Base Controller

### `app/Http/Controllers/Controller.php`

- **Type**: Abstract Base Class
- **Description**: Base controller that all other controllers extend. Currently empty but serves as the foundation for the controller hierarchy.
- **Methods**: None (empty class)

---

## Web Controllers

### `app/Http/Controllers/BookingController.php`

- **Namespace**: `App\Http\Controllers`
- **Purpose**: Handles the web-based booking flow for travelers (seat selection, mobile-money payment, tickets, cancellation and booking lookup)

| Method                                              | Description                                                                                                                                                                                               |
| --------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `showSeats($id)`                                    | Displays the seat selection page for a route. Loads route with bus/operator details and already-booked seats, clamps the requested passenger count to the remaining capacity, and aborts 404 when the route is inactive. |
| `store(Request, FareCalculationService)`            | Creates one pending `Booking` per passenger in the submitted `passengers[]` array (multi-seat group booking). Validates each seat is within capacity, unique, and not already booked; calculates the fare once per route/promo; assigns a shared `group_reference` when more than one passenger is submitted; redirects to the payment ticket page for the first booking. |
| `paymentTicket($bookingId)`                         | Displays the payment ticket page for a booking. When the booking shares a `group_reference`, pulls in every sibling booking in the party and aggregates the fare breakdown (base fare, service fees, discount, total) across the whole group.       |
| `processPayment(Request, Booking, PaymentService)`  | Processes mobile money payment (MTN/Airtel) via `PaymentService`. For a single booking, calls `processMobileMoney()`; for a multi-seat group (shared `group_reference`), calls `processMobileMoneyForGroup()` so every seat in the party is confirmed, ticketed, and notified. Validates provider/phone, checks confirmation/expiry, then redirects to the success page or back with errors. |
| `validatePromoCode(Request, FareCalculationService)`| AJAX endpoint validating a promo code for a route; returns the discount result as JSON.                                                                                                                  |
| `success(Booking)`                                  | Displays the success page (digital ticket / booking history) after payment. Loads sibling group bookings when the purchase covered multiple seats, so every ticket in the party is shown, not just the one paid through. |
| `showTicket(string $qrCode)`                        | Displays a traveler's digital ticket by QR code (lets a traveler re-open their ticket after booking).                                                                                                     |
| `releaseHold(Booking $booking)`                     | Releases a pending seat hold when a traveler backs out of the payment page to reselect seat(s). Only ever touches a still-`pending` booking (skips refund/notification logic), releases every sibling booking sharing the same `group_reference`, and redirects back to seat selection. |
| `cancel(Booking, RefundCalculationService)`         | Cancels the traveler's own booking. Pending bookings are released; confirmed bookings are cancelled with a refund per the operator's rules. Blocks past and already-cancelled trips.                       |
| `customerLookupView()`                              | "My Bookings" page. Signed-in travelers immediately see their own paginated, trip-grouped booking history (via `paginatedTripsForCurrentUser()`); guests see only the reference-ID lookup form.           |
| `customerLookup(Request)`                           | Looks up a single booking by its exact `reference_id` (not scoped to the current user and no phone-number search — the reference ID itself is the credential, similar to a paper ticket/PNR). Also returns the signed-in user's dashboard trips alongside the lookup result. Rate-limited at the route level (`throttle:20,1`) as a brute-force guard. |

**Private Methods**: `paginatedTripsForCurrentUser(): LengthAwarePaginator` - Builds the paginated (10/page), trip-grouped booking history for the signed-in user via `Booking::groupIntoTrips()`. Shared by `customerLookupView()` and `customerLookup()`.

**Models Used**: `Booking`, `Route`, `Operator`, `PromoCode`, `Ticket`
**Dependencies**: `App\Services\FareCalculationService`, `App\Services\PaymentService`, `App\Services\RefundCalculationService`, `App\Notifications\BookingCancelled`, `Carbon\Carbon`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\DB`, `Illuminate\Support\Str`, `Illuminate\Pagination\LengthAwarePaginator`

---

### `app/Http/Controllers/LandingController.php`

- **Namespace**: `App\Http\Controllers`
- **Purpose**: Handles landing page and trip search functionality

| Method                     | Description                                                                                                                                                        |
| -------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `index()`                  | Displays the landing page with popular routes. Uses IP-based location detection to show routes from the user's city (verified against active routes in the DB before use), falling back to random active routes if there's no location match. Also passes the flat list of Zambian cities (for From/To autocomplete) to the view. |
| `search(Request $request)` | Searches for trips based on origin, destination, and travel date, plus price range (`min_price`/`max_price`), time-of-day (`dawn`/`morning`/`afternoon`/`night`), and operator filters. Origin/destination are validated case-insensitively against the Zambian city list. Also returns the full set of operators matching the base route/date search (unaffected by the other filters) so the operator filter checklist doesn't shrink as filters are applied. |

**Private/Protected Methods**: `allCities()` - Returns a flat, deduped, sorted list of every city in `config/zambia_cities.php`; shared by `index()` (autocomplete data) and `search()` (origin/destination validation).

**Models Used**: `Route`
**Dependencies**: `Stevebauman\Location\Facades\Location`, `config/zambia_cities.php`

---

### `app/Http/Controllers/ProfileController.php`

- **Namespace**: `App\Http\Controllers`
- **Purpose**: Traveler profile management and booking history

| Method                        | Description                                                                                              |
| ----------------------------- | -------------------------------------------------------------------------------------------------------- |
| `index()`                     | Displays the traveler's profile (uses the `web` guard) with their booking history grouped into one entry per purchase via `Booking::groupIntoTrips()` and paginated 10 trips per page. |
| `update(Request $request)`    | Updates the traveler's basic account details (full_name, email, phone_number, preferred_language).       |
| `updatePassword(Request $request)` | Updates the traveler's password after verifying the current password.                               |

**Models Used**: `User`, `Booking`
**Dependencies**: `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\Hash`, `Illuminate\Validation\Rule`, `Illuminate\Pagination\LengthAwarePaginator`

---

## Auth Controllers

### `app/Http/Controllers/Auth/LoginController.php`

- **Namespace**: `App\Http\Controllers\Auth`
- **Purpose**: Handles traveler authentication for web-based requests

| Method                     | Description                                                                                                                            |
| -------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| `showLoginForm()`          | Displays the traveler login form.                                                                                                      |
| `login(Request $request)`  | Logs in a traveler via the web guard. Validates credentials, blocks suspended accounts, regenerates the session and redirects to the intended/home route. |
| `logout(Request $request)` | Logs out the traveler from the web and operator guards, clears and invalidates the session.                                            |

**Dependencies**: `Illuminate\Support\Facades\Auth`

---

### `app/Http/Controllers/Auth/RegisterController.php`

- **Namespace**: `App\Http\Controllers\Auth`
- **Purpose**: Handles traveler self-registration on the web (routes: `GET/POST /register`)

| Method                          | Description                                                                                |
| ------------------------------- | ------------------------------------------------------------------------------------------ |
| `showRegistrationForm()`        | Displays the traveler registration form.                                                   |
| `register(Request $request)`    | Validates and creates a traveler account, logs the user in (web guard) and redirects home. |

**Models Used**: `User`
**Dependencies**: `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\Hash`, `Illuminate\Validation\Rule`

---

### `app/Http/Controllers/Auth/PasswordResetController.php`

- **Namespace**: `App\Http\Controllers\Auth`
- **Purpose**: Handles the "forgot password" reset flow for travelers (routes: `/forgot-password`, `/reset-password`)

| Method                                    | Description                                                                                                   |
| ----------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| `showLinkRequestForm()`                   | Displays the password reset link request form.                                                               |
| `sendResetLinkEmail(Request $request)`    | Validates the email and sends a password reset link via the password broker.                                  |
| `showResetForm(Request, $token = null)`   | Displays the password reset form with the token and email.                                                    |
| `reset(Request $request)`                 | Resets the password via the broker, hashes the new password and fires a `PasswordReset` event.               |

**Dependencies**: `Illuminate\Support\Facades\Password`, `Illuminate\Support\Facades\Hash`, `Illuminate\Support\Str`, `Illuminate\Auth\Events\PasswordReset`

---

### `app/Http/Controllers/Auth/Admin/LoginController.php`

- **Namespace**: `App\Http\Controllers\Auth\Admin`
- **Purpose**: Handles admin authentication for the admin web portal (routes: `GET/POST /admin/login`)

| Method                     | Description                                                                                                          |
| -------------------------- | -------------------------------------------------------------------------------------------------------------------- |
| `showLoginForm()`          | Displays the admin login form.                                                                                       |
| `login(Request $request)`  | Logs in via the `admin` guard; only users with the admin role are permitted. Redirects to the admin dashboard.        |
| `logout(Request $request)` | Logs out the admin, clears and invalidates the session, redirects to the admin login.                               |

**Dependencies**: `Illuminate\Support\Facades\Auth`

---

### `app/Http/Controllers/Auth/Operator/LoginController.php`

- **Namespace**: `App\Http\Controllers\Auth\Operator`
- **Purpose**: Handles operator authentication for web-based requests

| Method                     | Description                                                                                                                                 |
| -------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------- |
| `showLoginForm()`          | Displays the operator login form.                                                                                                           |
| `login(Request $request)`  | Logs in an operator via the operator guard. Validates credentials, blocks unverified operators, logs the event with `OperatorAuditService` and redirects to the operator dashboard. |
| `logout(Request $request)` | Logs out from the web and operator guards, clears/invalidates the session and logs the event.                                               |

**Dependencies**: `Illuminate\Support\Facades\Auth`, `App\Services\OperatorAuditService`, `App\Models\OperatorAuditLog`

---

## Admin Controllers

Controllers in `App\Http\Controllers\Admin` handle platform-wide management for the admin web portal. They use session-based authentication (admin guard) and log administrative actions via `App\Services\AdminAuditService`.

### `app/Http/Controllers/Admin/DashboardController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: Admin dashboard with system-wide statistics and trends

| Method    | Description                                                                                                                                                                   |
| --------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index()` | Displays system stats (users, operators, pending verifications, bookings, revenue, buses, routes), month-over-month trends, booking status breakdown, payment channel split, last-30-day charts, top operators, and recent activity lists. |

**Private Methods**: `percentageChange($previous, $current)` - Computes the percentage change between two baselines.

**Models Used**: `User`, `Operator`, `Booking`, `Bus`, `Route`, `Payment`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\DB`

---

### `app/Http/Controllers/Admin/BookingController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: System-wide booking listing and detail views

| Method                    | Description                                                                                                                                                        |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `index(Request $request)` | Lists bookings across all operators with filters (status, operator, search by reference/passenger/phone/seat, travel date) and sorting. Provides summary stats.   |
| `show($id)`               | Shows a single booking's full detail (route, bus, driver, user, payment, ticket, promo code, cancellation rule) and logs a `booking.viewed` audit event.           |

**Private Methods**: `applyDateFilter($query, $date)` - Filters bookings by travel date (today/week/month).

**Models Used**: `Booking`, `Operator`
**Dependencies**: `Carbon\Carbon`, `App\Services\AdminAuditService`

---

### `app/Http/Controllers/Admin/OperatorController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: Operator management (list, create, verify, suspend, delete)

| Method                    | Description                                                                                                                                                                          |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `index(Request $request)` | Lists operators with verification status filter and search (company, email, TPIN, phone). Includes bus/route counts and summary counts.                                              |
| `create()`                | Shows the form to create a new operator.                                                                                                                                             |
| `store(Request $request)` | Validates and creates an operator (optionally marking them verified immediately). Logs an `operator.created` audit event.                                                            |
| `show($id)`               | Shows an operator's profile with bus/route counts, booking stats and recent bookings.                                                                                                |
| `verify(Request, $id)`    | Approves/verifies an operator (sets is_verified, verified_at, verified_by). Logs the audit event.                                                                                    |
| `suspend(Request, $id)`   | Suspends/unverifies an operator (clears verification fields). Logs the audit event.                                                                                                  |
| `destroy(Request, $id)`   | Soft-deletes an operator after logging an `operator.deleted` audit event.                                                                                                             |

**Private Methods**: `operatorStats($operatorId)` - Aggregates booking/usage stats for an operator.

**Models Used**: `Operator`, `Booking`
**Dependencies**: `Illuminate\Support\Facades\Auth`, `App\Services\AdminAuditService`

---

### `app/Http/Controllers/Admin/UserController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: Traveler management (list, create, suspend, activate, delete)

| Method                    | Description                                                                                                                                                                         |
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)` | Lists travelers with search, active/suspended status filter and pagination, plus summary counts.                                                                                    |
| `show($id)`               | Shows a traveler's profile with booking stats and recent bookings; logs a `user.viewed` audit event.                                                                                |
| `create()`                | Shows the form to create a new user account.                                                                                                                                        |
| `store(Request $request)` | Validates and creates a traveler or admin user; logs a `user.created` audit event.                                                                                                  |
| `suspend(Request, $id)`   | Deactivates a traveler account (own admin account cannot be suspended).                                                                                                             |
| `activate(Request, $id)`  | Reactivates a traveler account.                                                                                                                                                      |
| `destroy(Request, $id)`   | Soft-deletes a traveler account (own admin account cannot be deleted) and logs the audit event.                                                                                     |

**Models Used**: `User`, `Booking`
**Dependencies**: `Illuminate\Support\Facades\Auth`, `App\Services\AdminAuditService`

---

### `app/Http/Controllers/Admin/PaymentController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: System-wide payment transaction listing

| Method                    | Description                                                                                                                                            |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `index(Request $request)` | Lists all payments with filters (status, method, operator, date range, search by transaction/booking reference) and summary stats (successful/failed/pending, revenue). |

**Models Used**: `Payment`, `Operator`

---

### `app/Http/Controllers/Admin/TripController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: System-wide trip (route) listing

| Method                    | Description                                                                                                                                             |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)` | Lists all trips across operators with filters (operator, date, status: delayed/departed/arrived/scheduled/inactive, search) and trip stats.             |

**Models Used**: `Route`, `Operator`

---

### `app/Http/Controllers/Admin/ReportController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: Analytics / reports (revenue and bookings trends)

| Method                    | Description                                                                                                                                             |
| ------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index()`                 | Renders the reports dashboard with all datasets (trailing 12-month trends, revenue by operator/route, payment methods, status breakdown, totals).       |
| `data(Request $request)`  | JSON endpoint returning the same datasets scoped to an optional date range / operator for live filtering.                                                |
| `export()`                | Exports a revenue-by-operator CSV file.                                                                                                                   |

**Private Methods**: `buildReportData($from, $to, $operatorId)`, `scopedPaymentsQuery($from, $to, $operatorId)`, `scopedBookingsQuery($from, $to, $operatorId)`, `normaliseRange($from, $to)`.

**Models Used**: `Booking`, `Payment`, `Operator`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\DB`

---

### `app/Http/Controllers/Admin/AuditLogController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: Platform-wide admin audit log

| Method                    | Description                                                                                                  |
| ------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `index(Request $request)` | Lists audit log entries (paginated, 25/page) with filters for event, date range and search. Includes stats.  |
| `show($id)`               | Shows a single audit log entry with recorded state changes.                                                   |

**Models Used**: `AdminAuditLog`

---

### `app/Http/Controllers/Admin/ProfileController.php`

- **Namespace**: `App\Http\Controllers\Admin`
- **Purpose**: Admin profile / settings

| Method                        | Description                                                                                                                       |
| ----------------------------- | --------------------------------------------------------------------------------------------------------------------------------- |
| `index()`                     | Displays the admin's profile and their recent audit activity.                                                                     |
| `update(Request $request)`    | Updates the admin's account details and logs a `profile.updated` audit event.                                                     |
| `updatePassword(Request $request)` | Updates the admin's password after verifying the current one; logs a `profile.password_changed` audit event.                 |

**Private Methods**: `getAdmin()` - Resolves the authenticated admin via the `admin` guard (403 if the user is not an admin).

**Models Used**: `User`, `AdminAuditLog`
**Dependencies**: `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\Hash`, `App\Services\AdminAuditService`

---

## API Controllers

### `app/Http/Controllers/Api/AdminController.php`

- **Namespace**: `App\Http\Controllers\Api`
- **Purpose**: Admin dashboard and management endpoints

| Method                                      | Description                                                                                                          |
| ------------------------------------------- | -------------------------------------------------------------------------------------------------------------------- |
| `dashboard()`                               | Returns system overview statistics including total users, operators, pending approvals, bookings, and total revenue. |
| `operators(Request $request)`               | Lists all operators with optional filtering by status (pending/verified/all). Includes bus and route counts.         |
| `verifyOperator(Request $request, int $id)` | Verifies an operator account. Sets is_verified to true, records verification timestamp and admin ID.                 |
| `suspendOperator(int $id)`                  | Suspends/unverifies an operator. Sets is_verified to false and clears verification fields.                           |
| `users()`                                   | Lists all travelers with their booking counts, ordered by creation date.                                             |
| `bookings(Request $request)`                | Lists all bookings system-wide with optional status filtering. Paginates results (50 per page).                      |

**Models Used**: `Booking`, `Operator`, `Payment`, `User`

---

### `app/Http/Controllers/Api/AuthController.php`

- **Namespace**: `App\Http\Controllers\Api`
- **Purpose**: Authentication endpoints for travelers and operators

| Method                               | Description                                                                                                                       |
| ------------------------------------ | --------------------------------------------------------------------------------------------------------------------------------- |
| `register(Request $request)`         | Registers a new traveler. Validates full_name, email, phone_number, and password (min 8 chars). Returns user data and auth token. |
| `login(Request $request)`            | Authenticates a traveler. Validates email and password. Returns user data and auth token.                                         |
| `registerOperator(Request $request)` | Registers a new bus operator. Validates company_name, email, phone_number, password, and optional TPIN/address.                   |
| `loginOperator(Request $request)`    | Authenticates an operator. Validates email and password. Checks if operator is verified before allowing login.                    |
| `logout(Request $request)`           | Logs out the authenticated user by deleting their current access token.                                                           |
| `me(Request $request)`               | Returns the authenticated user's details.                                                                                         |

**Models Used**: `User`, `Operator`
**Dependencies**: `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\Hash`

---

### `app/Http/Controllers/Api/BookingController.php`

- **Namespace**: `App\Http\Controllers\Api`
- **Purpose**: API endpoints for booking management

| Method                                          | Description                                                                                                         |
| ----------------------------------------------- | ------------------------------------------------------------------------------------------------------------------- |
| `hold(Request $request)`                        | Holds a seat for 10 minutes pending payment. Validates seat is within bus capacity and not already taken.           |
| `index(Request $request)`                       | Lists all bookings for the authenticated traveler with route, operator, bus, payment, and ticket details.           |
| `show(Request $request, string $referenceId)`   | Gets a single booking by reference ID for the authenticated traveler.                                               |
| `cancel(Request $request, int $id)`             | Cancels a pending booking. Confirmed bookings cannot be self-cancelled.                                             |
| `routeManifest(Request $request, int $routeId)` | Lists all confirmed bookings for a specific route (operator dashboard). Returns passenger details and seat numbers. |

**Models Used**: `Booking`, `Route`

---

### `app/Http/Controllers/Api/BusController.php`

- **Namespace**: `App\Http\Controllers\Api`
- **Purpose**: Bus fleet management for operators

| Method                               | Description                                                                                                                                  |
| ------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)`            | Lists all buses for the authenticated operator with route counts.                                                                            |
| `store(Request $request)`            | Adds a new bus to the operator's fleet. Validates registration_number, seat_capacity (1-100), and bus_class (economy/business/luxury).       |
| `update(Request $request, int $id)`  | Updates bus details including model, seat_capacity, bus_class, amenities, and is_active status. Only allows updates to operator's own buses. |
| `destroy(Request $request, int $id)` | Removes a bus from the fleet. Only allowed if no active routes exist for the bus.                                                            |

**Models Used**: `Bus`

---

### `app/Http/Controllers/Api/RouteController.php`

- **Namespace**: `App\Http\Controllers\Api`
- **Purpose**: Route/trip management for travelers and operators

| Method                               | Description                                                                                                                           |
| ------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------- |
| `search(Request $request)`           | Search routes by origin, destination, and date. Accessible by guests and travelers. Returns route details with available seats count. |
| `show(int $id)`                      | Get full route details including seat map. Used when a traveler clicks on a route to select a seat.                                   |
| `operatorRoutes(Request $request)`   | List all routes for a specific operator (operator dashboard).                                                                         |
| `store(Request $request)`            | Create a new route (operator only). Validates bus_id, origin, destination, times, fare, and travel date.                              |
| `update(Request $request, int $id)`  | Update fare or times for a route (operator only). Only allows updates to operator's own routes.                                       |
| `destroy(Request $request, int $id)` | Delete a route (operator only, if no confirmed bookings).                                                                             |

**Models Used**: `Route`

---

### `app/Http/Controllers/Api/PaymentController.php`

- **Namespace**: `App\Http\Controllers\Api`
- **Purpose**: Payment processing for bookings

| Method                                     | Description                                                                                                                               |
| ------------------------------------------ | ----------------------------------------------------------------------------------------------------------------------------------------- |
| `initiate(Request $request)`               | Initiate a payment for a pending booking. In actual production this would call the MTN/Airtel API. Validates booking is in payable state. |
| `callback(Request $request)`               | Callback endpoint hit by the payment gateway after transaction. Confirms or fails the payment and issues a ticket on success.             |
| `status(Request $request, int $bookingId)` | Get payment status for a booking. Returns booking status and payment details.                                                             |

**Models Used**: `Booking`, `Payment`, `Ticket`

---

### `app/Http/Controllers/Api/TicketController.php`

- **Namespace**: `App\Http\Controllers\Api`
- **Purpose**: Digital ticket management for travelers and operators

| Method                                   | Description                                                                                                   |
| ---------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)`                | Get all tickets for the authenticated traveler. Returns formatted ticket data with route and booking details. |
| `show(Request $request, string $qrCode)` | Get a single ticket by QR code string. Used by traveler to view their ticket.                                 |
| `verify(Request $request)`               | Verify a ticket at the bus station (operator only). Marks the ticket as used if valid.                        |

**Models Used**: `Ticket`

---

## Operator Controllers

### `app/Http/Controllers/Operator/DashboardController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Operator dashboard with statistics and overview

| Method    | Description                                                                                                                                                          |
| --------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index()` | Displays operator dashboard with booking/revenue KPIs and month-over-month trends, today's bookings, pending/cancelled counts, trip occupancy, fleet status, upcoming trips, recent bookings and top routes. |

**Private Methods**:

- `getOperator()` - Private helper method to get authenticated operator. Supports API guard, session, and fallback to operator ID 1.
- `getOperatorRoutes($operator)` - Get operator's routes for dropdown
- `getOperatorBuses($operator)` - Get operator's buses with detailed info
- `getOperatorDrivers($operator)` - Get operator's active drivers

**Models Used**: `Route`, `Booking`, `Operator`, `Bus`, `Driver`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\Auth`

---

### `app/Http/Controllers/Operator/TripManagementController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Comprehensive trip management for bus operators (19 public methods)

| Method                                    | Description                                                                                                        |
| ----------------------------------------- | ------------------------------------------------------------------------------------------------------------------ |
| `calendar(Request $request)`              | Displays a month calendar view of trips with status and booked counts.                                             |
| `index(Request $request)`                 | Displays the trip management dashboard with filters for status, date, and search.                                  |
| `seatMap($tripId)`                        | Shows the seat map for a specific trip with passenger details.                                                     |
| `store(Request $request)`                 | Stores a new trip. Validates origin, destination, date, time, bus, and fare. Checks for bus conflicts.             |
| `update(Request $request, $tripId)`       | Updates an existing trip. Validates and checks for conflicts. Logs fare changes for audit.                         |
| `cancel($tripId)`                         | Cancels a trip. Checks for confirmed bookings and cancels pending bookings.                                        |
| `occupancy($tripId)`                      | Gets seat occupancy for a trip (API). Returns a detailed seat map.                                                 |
| `export(Request $request)`                | Exports trip data as a CSV file.                                                                                   |
| `upcoming(Request $request)`              | Gets upcoming trips (API endpoint) with configurable limit and days.                                               |
| `show($tripId)`                           | Gets trip details (API endpoint) with bookings summary.                                                            |
| `stats()`                                 | Gets trip statistics (API endpoint) with weekly trend data.                                                        |
| `updateStatus(Request $request, $tripId)` | Updates trip status (API endpoint). Maps status to route updates.                                                  |
| `markDelayed(Request $request, $tripId)`  | Marks a trip as delayed (delay minutes/reason), logs the audit event and notifies passengers.                      |
| `markDeparted($tripId)`                   | Marks a trip as departed and notifies passengers.                                                                  |
| `markArrived($tripId)`                    | Marks a trip as arrived and notifies passengers.                                                                   |
| `assignDriver(Request, $tripId)`          | Assigns a driver from the operator's fleet to a trip.                                                               |
| `tripJson($tripId)`                       | Returns trip data as JSON for the edit drawer.                                                                     |
| `cancelWithNotification($tripId)`         | Cancels a trip and notifies passengers (bypasses the confirmed-booking check).                                     |
| `printableSchedule(Request $request)`     | Renders a printable schedule for a date range.                                                                     |

**Private Methods**:

- `getOperator()` - Get authenticated operator (works for both API and web)
- `getOperatorTrips($operator, $request)` - Get all trips for an operator with filters
- `getOperatorBuses($operator)` - Get operator's buses with detailed info
- `getOperatorRoutes($operator)` - Get operator's routes for dropdown
- `getStatusStyles()` - Get status styles for badges
- `getTripStats($operator)` - Get trip statistics for dashboard
- `formatTripData($route)` - Format route data into trip format
- `determineTripStatus($route)` - Determine trip status with comprehensive logic
- `applyTripStatusFilter($query, $status)` - Apply trip status filter to query
- `applyDateFilter($query, $filter)` - Apply date filter to query
- `generateSeatMap($capacity, $bookedSeats, $pendingSeats, $passengerMap)` - Generate seat map array
- `generateDetailedSeatMap($capacity, $confirmedSeats, $pendingSeats)` - Generate detailed seat map for API

**Models Used**: `Route`, `Booking`, `Bus`, `Driver`, `Operator`, `Ticket`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\DB`, `Illuminate\Support\Facades\Log`, `Illuminate\Support\Facades\Notification`, `App\Services\OperatorAuditService`, `App\Notifications\TripStatusChanged`

---

### `app/Http/Controllers/Operator/BookingManagementController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Comprehensive booking management for the operator's trips

| Method                                | Description                                                                                                                                                                        |
| ------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)`             | Lists all bookings for the operator with status/payment/date filters, search and sorting, plus summary stats.                                                                       |
| `tripBookings($tripId)`               | Shows bookings for a specific trip.                                                                                                                                                |
| `show($bookingId)`                    | Shows a booking's full detail and logs a `booking.viewed` audit event.                                                                                                              |
| `edit($bookingId)`                    | Shows the edit form with available seats for a seat change.                                                                                                                        |
| `update(Request, $bookingId)`         | Updates booking passenger details/seat with conflict and capacity checks.                                                                                                           |
| `markBoarded($bookingId)`             | Marks a confirmed booking as boarded.                                                                                                                                              |
| `undoBoarded($bookingId)`             | Removes the boarded status.                                                                                                                                                        |
| `updateNotes(Request, $bookingId)`    | Adds/updates internal notes on a booking.                                                                                                                                          |
| `bulkAction(Request)`                 | Bulk cancels pending bookings or bulk marks confirmed bookings as boarded.                                                                                                         |
| `printReceipt($bookingId)`            | Shows a printable receipt for a booking.                                                                                                                                           |
| `customerLookup(Request)`             | Looks up a booking by reference ID or phone number.                                                                                                                                |
| `cancelBooking($bookingId)`           | Cancels a booking (pending or confirmed) with appropriate handling.                                                                                                                |
| `processRefund($bookingId)`           | Processes a refund for a cancelled booking.                                                                                                                                        |
| `export(Request $request)`            | Exports the operator's bookings as a CSV file.                                                                                                                                     |

**Private Methods**: `getOperator()`, `applyDateFilter($query, $filter)`, `getBookingStats($operator)`, `getStatusStyles()`, `formatTripData($route)`.

**Models Used**: `Booking`, `Route`, `Operator`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\DB`, `App\Services\OperatorAuditService`

---

### `app/Http/Controllers/Operator/BusController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Fleet (bus) management

| Method                        | Description                                                                                                                                                      |
| ----------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)`     | Lists the operator's buses with search/status/class filters and fleet summary stats (total, active, capacity, maintenance due).                                   |
| `store(Request $request)`     | Validates and creates a bus (registration number unique per operator, capacity 1-100, class economy/business/luxury, amenities, maintenance fields).             |
| `show($busId)`                | Shows a single bus detail.                                                                                                                                       |
| `update(Request, $busId)`     | Updates a bus with the same validation rules.                                                                                                                    |
| `toggleStatus($busId)`        | Activates/deactivates a bus; blocks deactivation when the bus has upcoming scheduled trips. Logs an audit event.                                                |
| `destroy($busId)`             | Soft-deletes a bus; blocked when upcoming scheduled trips exist. Logs an audit event.                                                                            |

**Private Methods**: `getOperator()`.

**Models Used**: `Bus`, `Route`, `Operator`
**Dependencies**: `Carbon\Carbon`, `App\Services\OperatorAuditService`, `Illuminate\Validation\Rule`

---

### `app/Http/Controllers/Operator/PassengerListController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Passenger lists (manifest) and check-in

| Method                      | Description                                                                                                                                   |
| --------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)`   | Lists all passengers across the operator's trips with filters (trip, status, boarded, date range, search) and summary stats.                 |
| `manifest($routeId)`        | Shows the passenger manifest for a specific trip.                                                                                             |
| `export($routeId)`          | Exports the passenger manifest for a trip as a CSV file.                                                                                       |
| `bulkCheckin(Request)`      | Bulk-marks selected confirmed bookings as boarded.                                                                                             |

**Private Methods**: `getPassengerStats($operator)`, `getOperator()`.

**Models Used**: `Booking`, `Route`, `Operator`
**Dependencies**: `Carbon\Carbon`

---

### `app/Http/Controllers/Operator/CustomerController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Customer (traveler) management for the operator

| Method                    | Description                                                                                                                                              |
| ------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)` | Lists customers who have booked with this operator (search, sort) with stats (total, confirmed bookings, new this month, repeat customers).              |
| `show($customerId)`       | Shows a customer's details, booking history and stats; logs an audit event.                                                                              |

**Private Methods**: `getOperator()`.

**Models Used**: `User`, `Booking`, `Operator`
**Dependencies**: `Carbon\Carbon`, `App\Services\OperatorAuditService`

---

### `app/Http/Controllers/Operator/RevenueController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Revenue analytics

| Method                    | Description                                                                                                                                                                 |
| ------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index(Request $request)` | Revenue dashboard with date range / period filters; KPIs (total/period/today revenue, pending payments, confirmed/cancelled counts), revenue by route and payment method, daily chart data and recent transactions. |

**Private Methods**: `getOperator()`.

**Models Used**: `Booking`, `Payment`, `Route`, `Operator`
**Dependencies**: `Carbon\Carbon`

---

### `app/Http/Controllers/Operator/AuditLogController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Operator audit log

| Method                    | Description                                                                                                  |
| ------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `index(Request $request)` | Lists the operator's audit logs (paginated, 30/page) with event/date-range/search filters and summary stats. |
| `show($id)`               | Shows a single audit log entry.                                                                              |

**Private Methods**: `getOperator()` - Includes the full fallback logic (guard, session, fallback to operator ID 1).

**Models Used**: `OperatorAuditLog`, `Operator`

---

### `app/Http/Controllers/Operator/FareRuleController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Fare rules management (cancellation rules and service fees)

| Method                                   | Description                                                                                              |
| ---------------------------------------- | -------------------------------------------------------------------------------------------------------- |
| `index(Request, FareCalculationService)` | Shows cancellation rules and service fees; supports a live fare preview with an optional promo code.     |
| `storeCancellationRule(Request)`         | Creates a cancellation rule (hours before departure, refund percentage).                                 |
| `updateCancellationRule(Request, $id)`   | Updates a cancellation rule.                                                                             |
| `destroyCancellationRule($id)`           | Deletes a cancellation rule.                                                                             |
| `storeServiceFee(Request)`               | Creates a service fee (fixed or percentage).                                                             |
| `updateServiceFee(Request, $id)`         | Updates a service fee.                                                                                   |
| `destroyServiceFee($id)`                 | Deletes a service fee.                                                                                   |

**Private Methods**: `getOperator()`.

**Models Used**: `CancellationRule`, `ServiceFee`, `PromoCode`, `Operator`
**Dependencies**: `App\Services\FareCalculationService`, `App\Services\OperatorAuditService`

---

### `app/Http/Controllers/Operator/DriverController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Driver management

| Method                    | Description                                                                             |
| ------------------------- | --------------------------------------------------------------------------------------- |
| `index()`                 | Lists the operator's drivers (paginated).                                               |
| `store(Request $request)` | Validates and creates a driver (unique license number).                                 |
| `update(Request, $id)`    | Updates a driver's details.                                                             |
| `json($id)`               | Returns a driver as JSON (for AJAX).                                                    |
| `destroy($id)`            | Deletes a driver; blocked when the driver is assigned to active routes.                 |

**Private Methods**: `getOperator()`.

**Models Used**: `Driver`, `Operator`
**Dependencies**: `Illuminate\Validation\Rule`

---

### `app/Http/Controllers/Operator/PromoCodeController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Promo code management

| Method                    | Description                                                                                              |
| ------------------------- | -------------------------------------------------------------------------------------------------------- |
| `index()`                 | Lists the operator's promo codes.                                                                        |
| `store(Request $request)` | Validates and creates a promo code (unique code, discount type/value, validity window, usage limits).    |
| `update(Request, $id)`    | Updates a promo code.                                                                                    |
| `destroy($id)`            | Deletes a promo code.                                                                                    |

**Private Methods**: `getOperator()`.

**Models Used**: `PromoCode`, `Operator`
**Dependencies**: `App\Services\OperatorAuditService`

---

### `app/Http/Controllers/Operator/RouteTemplateController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Route templates and bulk trip creation

| Method                            | Description                                                                                                                            |
| --------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| `index()`                         | Lists the operator's route templates with trip counts.                                                                                 |
| `store(Request $request)`         | Validates and creates a route template (name, origin, destination, distance, base fare).                                               |
| `update(Request, $id)`            | Updates a route template.                                                                                                              |
| `destroy($id)`                    | Deletes a route template.                                                                                                              |
| `createTrip(Request, $id)`        | Creates a single trip from a template (bus, dates, times, fare).                                                                       |
| `createBulkTrips(Request, $id)`   | Bulk-creates trips over a date range on selected weekdays, checking for bus conflicts.                                                 |
| `getTemplatesJson()`              | Returns active templates as JSON (for AJAX).                                                                                           |

**Private Methods**: `getOperator()`.

**Models Used**: `RouteTemplate`, `Route`, `Operator`
**Dependencies**: `Carbon\Carbon`, `App\Services\OperatorAuditService`

---

### `app/Http/Controllers/Operator/ProfileController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Operator profile / settings

| Method                        | Description                                                                                                                                                      |
| ----------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `index()`                     | Displays the operator's profile with operational stats (total trips, active buses, avg occupancy, on-time rate, confirmed bookings).                              |
| `update(Request $request)`    | Updates the operator's business details and handles an optional logo upload to public storage.                                                                     |
| `updatePassword(Request)`     | Updates the operator's password after verifying the current one.                                                                                                 |

**Private Methods**: `getOperator()`.

**Models Used**: `Operator`, `Route`, `Booking`, `Bus`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\Hash`, `Illuminate\Support\Facades\Storage`, `Illuminate\Validation\Rule`

---

## Summary Table

| Controller                                | Type     | Methods Count | Primary Models                                        |
| ----------------------------------------- | -------- | ------------- | ----------------------------------------------------- |
| `Controller.php`                          | Base     | 0             | -                                                     |
| `BookingController.php` (Web)             | Web      | 11            | Booking, Route, Operator, PromoCode, Ticket           |
| `LandingController.php` (Web)             | Web      | 2             | Route                                                 |
| `ProfileController.php` (Web)             | Web      | 3             | User                                                  |
| `LoginController.php` (Auth)              | Auth     | 3             | -                                                     |
| `RegisterController.php` (Auth)           | Auth     | 2             | User                                                  |
| `PasswordResetController.php` (Auth)      | Auth     | 4             | -                                                     |
| `LoginController.php` (Auth/Admin)        | Auth     | 3             | -                                                     |
| `LoginController.php` (Auth/Operator)     | Auth     | 3             | -                                                     |
| `AdminController.php` (API)               | API      | 6             | User, Operator, Booking, Payment                      |
| `AuthController.php` (API)                | API      | 6             | User, Operator                                        |
| `BookingController.php` (API)             | API      | 5             | Booking, Route                                        |
| `BusController.php` (API)                 | API      | 4             | Bus                                                   |
| `RouteController.php` (API)               | API      | 6             | Route                                                 |
| `PaymentController.php` (API)             | API      | 3             | Booking, Payment, Ticket                              |
| `TicketController.php` (API)              | API      | 3             | Ticket                                                |
| `DashboardController.php` (Admin)         | Admin    | 1             | User, Operator, Booking, Bus, Route, Payment          |
| `BookingController.php` (Admin)           | Admin    | 2             | Booking, Operator                                     |
| `OperatorController.php` (Admin)          | Admin    | 7             | Operator, Booking                                     |
| `UserController.php` (Admin)              | Admin    | 7             | User, Booking                                         |
| `PaymentController.php` (Admin)           | Admin    | 1             | Payment, Operator                                     |
| `TripController.php` (Admin)              | Admin    | 1             | Route, Operator                                       |
| `ReportController.php` (Admin)            | Admin    | 3             | Booking, Payment, Operator                            |
| `AuditLogController.php` (Admin)          | Admin    | 2             | AdminAuditLog                                         |
| `ProfileController.php` (Admin)           | Admin    | 3             | User, AdminAuditLog                                   |
| `DashboardController.php` (Operator)      | Operator | 1             | Route, Booking, Operator, Bus, Driver                 |
| `TripManagementController.php` (Operator) | Operator | 19            | Route, Booking, Bus, Driver, Operator, Ticket         |
| `BookingManagementController.php` (Operator) | Operator | 14        | Booking, Route, Operator                              |
| `BusController.php` (Operator)            | Operator | 6             | Bus, Route, Operator                                  |
| `PassengerListController.php` (Operator)  | Operator | 4             | Booking, Route, Operator                              |
| `CustomerController.php` (Operator)       | Operator | 2             | User, Booking, Operator                               |
| `RevenueController.php` (Operator)        | Operator | 1             | Booking, Payment, Route, Operator                     |
| `AuditLogController.php` (Operator)       | Operator | 2             | OperatorAuditLog, Operator                            |
| `FareRuleController.php` (Operator)       | Operator | 7             | CancellationRule, ServiceFee, PromoCode, Operator     |
| `DriverController.php` (Operator)         | Operator | 5             | Driver, Operator                                      |
| `PromoCodeController.php` (Operator)      | Operator | 4             | PromoCode, Operator                                   |
| `RouteTemplateController.php` (Operator)  | Operator | 7             | RouteTemplate, Route, Operator                        |
| `ProfileController.php` (Operator)        | Operator | 3             | Operator, Route, Booking, Bus                         |

---

## Authentication & Authorization

- **Travelers (Web)**: Use `Auth\LoginController` for login/logout with session-based authentication; `Auth\RegisterController` for self-registration and `Auth\PasswordResetController` for password resets
- **Travelers (API)**: Use `Api\AuthController` for registration, login, and token management
- **Operators (Web)**: Use `Auth\Operator\LoginController` for login/logout with session-based authentication; actions are audited via `OperatorAuditService`
- **Operators (API)**: Use `Api\AuthController` for registration and login; must be verified before login
- **Admin (Web)**: Uses `Auth\Admin\LoginController` with the `admin` guard; only users with the `admin` role are permitted; actions are audited via `AdminAuditService`
- **Admin (API)**: Uses `Api\AdminController` endpoints for dashboard and management
- **API Authentication**: All API controllers use token-based authentication via `createToken()`
- **Operator Web fallback**: Many operator controllers fall back to operator ID 1 for development when no guard/session operator is present

---

## Notes

- `TripManagementController` is the largest operator controller (19 public methods) with an extensive set of private helpers for trip status determination and data formatting
- Most operator controllers share a `getOperator()` helper that resolves the operator from the guard, session, or a development fallback (operator ID 1)
- Admin and operator web portals log actions via `AdminAuditService` / `OperatorAuditService`
- Web fare/refund logic is centralized in `FareCalculationService`, `PaymentService`, and `RefundCalculationService`
- `BookingController` (Web) supports multi-seat "group" bookings: multiple passengers submitted in one `store()` request share a `group_reference`, and `paymentTicket()`, `processPayment()`, `success()`, and `releaseHold()` all operate on the whole group when one is present
- The traveler-facing booking lookup (`customerLookup()`) is deliberately reference-ID-only and unscoped by user — no phone-number search — and the route is throttled (`throttle:20,1`) against brute-forcing reference IDs; see the method's docblock in `BookingController.php` for the full reasoning
- Payment processing is designed to integrate with MTN/Airtel Money APIs in production
- All controllers follow RESTful conventions where applicable
- The application uses Carbon for date/time handling throughout
- Auth controllers handle web-based session authentication while the API `AuthController` handles token-based authentication
