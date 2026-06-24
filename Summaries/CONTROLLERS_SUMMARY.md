# Controllers Summary

This document provides a comprehensive summary of all controllers in the BookMyBus Zambia application.

## Overview

The application follows a standard Laravel MVC structure with controllers organized into three main categories:

- **Base Controller**: Abstract base class for all controllers
- **Web Controllers**: Handle traditional web page requests
- **API Controllers**: Handle RESTful API requests for mobile/web clients
- **Operator Controllers**: Handle operator-specific functionality

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
- **Purpose**: Handles web-based booking flow for travelers

| Method                             | Description                                                                                                                      |
| ---------------------------------- | -------------------------------------------------------------------------------------------------------------------------------- |
| `showSeats($id)`                   | Displays seat selection page for a specific route. Retrieves route with bus and operator details, and gets already booked seats. |
| `store(Request $request)`          | Creates a new booking with validated route_id and seat_number. Sets amount from route fare and status to 'pending'.              |
| `paymentTicket($bookingId)`        | Displays payment ticket page with booking details including origin, destination, departure time/date, and fare.                  |
| `processPayment(Booking $booking)` | Updates booking status from 'pending' to 'confirmed' and redirects to success page.                                              |
| `success(Booking $booking)`        | Displays the success page (digital ticket) with route, bus, and operator details.                                                |

**Models Used**: `Route`, `Booking`

---

### `app/Http/Controllers/LandingController.php`

- **Namespace**: `App\Http\Controllers`
- **Purpose**: Handles landing page and trip search functionality

| Method                     | Description                                                                                                                                                        |
| -------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `index()`                  | Displays landing page with popular routes. Uses IP-based location detection to show routes from the user's city. Falls back to random routes if no location match. |
| `search(Request $request)` | Searches for trips based on origin, destination, and travel date. Returns search results view.                                                                     |

**Models Used**: `Route`
**Dependencies**: `Stevebauman\Location\Facades\Location`

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

| Method          | Description                                                                                                      |
| --------------- | ---------------------------------------------------------------------------------------------------------------- |
| `index()`       | Displays operator dashboard with total bookings, revenue, active trips, fleet status, and upcoming trips.        |
| `getOperator()` | Private helper method to get authenticated operator. Supports API guard, session, and fallback to operator ID 1. |

**Models Used**: `Route`, `Booking`, `Operator`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\Auth`

---

### `app/Http/Controllers/Operator/TripManagementController.php`

- **Namespace**: `App\Http\Controllers\Operator`
- **Purpose**: Comprehensive trip management for bus operators

| Method                                    | Description                                                                                           |
| ----------------------------------------- | ----------------------------------------------------------------------------------------------------- |
| `index(Request $request)`                 | Display the trip management dashboard with filters for status, date, and search.                      |
| `seatMap($tripId)`                        | Show seat map for a specific trip with passenger details.                                             |
| `store(Request $request)`                 | Store a new trip. Validates origin, destination, date, time, bus, and fare. Checks for bus conflicts. |
| `update(Request $request, $tripId)`       | Update an existing trip. Validates and checks for conflicts. Logs fare changes for audit.             |
| `cancel($tripId)`                         | Cancel a trip. Checks for confirmed bookings and cancels pending bookings.                            |
| `occupancy($tripId)`                      | Get seat occupancy for a specific trip (API). Returns detailed seat map.                              |
| `export(Request $request)`                | Export trips data as CSV file.                                                                        |
| `upcoming(Request $request)`              | Get upcoming trips (API endpoint) with configurable limit and days.                                   |
| `show($tripId)`                           | Get trip details (API endpoint) with booking information and summary.                                 |
| `stats()`                                 | Get trip statistics (API endpoint) with weekly trend data.                                            |
| `updateStatus(Request $request, $tripId)` | Update trip status (API endpoint). Maps status to route updates.                                      |

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

**Models Used**: `Route`, `Booking`, `Bus`, `Operator`, `Ticket`
**Dependencies**: `Carbon\Carbon`, `Illuminate\Support\Facades\Auth`, `Illuminate\Support\Facades\DB`, `Illuminate\Support\Facades\Log`

---

## Summary Table

| Controller                     | Type     | Methods Count | Primary Models                        |
| ------------------------------ | -------- | ------------- | ------------------------------------- |
| `Controller.php`               | Base     | 0             | -                                     |
| `BookingController.php`        | Web      | 5             | Route, Booking                        |
| `LandingController.php`        | Web      | 2             | Route                                 |
| `AdminController.php`          | API      | 5             | User, Operator, Booking, Payment      |
| `AuthController.php`           | API      | 6             | User, Operator                        |
| `BookingController.php`        | API      | 5             | Booking, Route                        |
| `BusController.php`            | API      | 4             | Bus                                   |
| `RouteController.php`          | API      | 6             | Route                                 |
| `PaymentController.php`        | API      | 3             | Booking, Payment, Ticket              |
| `TicketController.php`         | API      | 3             | Ticket                                |
| `DashboardController.php`      | Operator | 2             | Route, Booking, Operator              |
| `TripManagementController.php` | Operator | 10+           | Route, Booking, Bus, Operator, Ticket |

---

## Authentication & Authorization

- **Travelers**: Use `AuthController` for registration, login, and token management
- **Operators**: Use `AuthController` for registration and login; must be verified before login
- **Admin**: Uses `AdminController` endpoints (assumed to be protected by admin middleware)
- **API Authentication**: All API controllers use token-based authentication via `createToken()`
- **Operator Web**: Uses session-based authentication with fallback to operator ID 1 for development

---

## Notes

- The `TripManagementController` has extensive private helper methods for trip status determination and data formatting
- The `DashboardController` and `TripManagementController` share similar `getOperator()` fallback logic for development purposes
- Payment processing is designed to integrate with MTN/Airtel Money APIs in production
- All controllers follow RESTful conventions where applicable
- The application uses Carbon for date/time handling throughout
