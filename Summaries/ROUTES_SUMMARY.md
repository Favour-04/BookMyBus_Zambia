# Routes Directory Summary

This summary covers the three route files in the `routes/` directory: `api.php`, `web.php`, and `console.php`.

## api.php

### Purpose

Defines the API endpoints for BookMyBus Zambia, including public search endpoints, traveler authentication/bookings/payments/tickets, operator routes, and admin functionality.

### Authentication Guards

| Guard                         | Model    | Description                                   |
| ----------------------------- | -------- | --------------------------------------------- |
| `auth:sanctum`                | User     | Traveler authentication using Laravel Sanctum |
| `auth:operator`               | Operator | Session guard for operator authentication (see `config/auth.php`) |
| `auth:sanctum` + `role:admin` | User     | Admin users with admin role                   |

### Controllers Used

- `App\Http\Controllers\Api\AuthController`
- `App\Http\Controllers\Api\BookingController`
- `App\Http\Controllers\Api\BusController`
- `App\Http\Controllers\Api\PaymentController`
- `App\Http\Controllers\Api\RouteController`
- `App\Http\Controllers\Api\TicketController`
- `App\Http\Controllers\Api\AdminController`

### Public Endpoints (No Authentication)

| Method | Endpoint                      | Controller Method                 | Description                           |
| ------ | ----------------------------- | --------------------------------- | ------------------------------------- |
| POST   | `/api/auth/register`          | `AuthController@register`         | Register new traveler                 |
| POST   | `/api/auth/login`             | `AuthController@login`            | Traveler login                        |
| POST   | `/api/auth/operator/register` | `AuthController@registerOperator` | Register new operator                 |
| POST   | `/api/auth/operator/login`    | `AuthController@loginOperator`    | Operator login                        |
| GET    | `/api/routes/search`          | `RouteController@search`          | Search available routes               |
| GET    | `/api/routes/{id}`            | `RouteController@show`            | Get specific route details            |
| POST   | `/api/payments/callback`      | `PaymentController@callback`      | Payment gateway callback (MTN/Airtel) |

### Traveler Authenticated Endpoints

| Method | Endpoint                           | Controller Method            | Description                 |
| ------ | ---------------------------------- | ---------------------------- | --------------------------- |
| POST   | `/api/auth/logout`                 | `AuthController@logout`      | Logout traveler             |
| GET    | `/api/auth/me`                     | `AuthController@me`          | Get authenticated user info |
| GET    | `/api/bookings`                    | `BookingController@index`    | List user's bookings        |
| POST   | `/api/bookings/hold`               | `BookingController@hold`     | Hold seats temporarily      |
| GET    | `/api/bookings/{ref}`              | `BookingController@show`     | Get booking by reference    |
| PATCH  | `/api/bookings/{id}/cancel`        | `BookingController@cancel`   | Cancel a booking            |
| POST   | `/api/payments/initiate`           | `PaymentController@initiate` | Initiate payment            |
| GET    | `/api/payments/status/{bookingId}` | `PaymentController@status`   | Check payment status        |
| GET    | `/api/tickets`                     | `TicketController@index`     | List user's tickets         |
| GET    | `/api/tickets/{qrCode}`            | `TicketController@show`      | Get ticket by QR code       |

### Operator Endpoints

| Method | Endpoint                                  | Controller Method                 | Description              |
| ------ | ----------------------------------------- | --------------------------------- | ------------------------ |
| GET    | `/api/operator/buses`                     | `BusController@index`             | List operator's buses    |
| POST   | `/api/operator/buses`                     | `BusController@store`             | Add new bus to fleet     |
| PATCH  | `/api/operator/buses/{id}`                | `BusController@update`            | Update bus details       |
| DELETE | `/api/operator/buses/{id}`                | `BusController@destroy`           | Remove bus from fleet    |
| GET    | `/api/operator/routes`                    | `RouteController@operatorRoutes`  | List operator's routes   |
| POST   | `/api/operator/routes`                    | `RouteController@store`           | Create new route         |
| PATCH  | `/api/operator/routes/{id}`               | `RouteController@update`          | Update route             |
| DELETE | `/api/operator/routes/{id}`               | `RouteController@destroy`         | Delete route             |
| GET    | `/api/operator/routes/{routeId}/manifest` | `BookingController@routeManifest` | Get passenger manifest   |
| POST   | `/api/operator/tickets/verify`            | `TicketController@verify`         | Verify ticket at station |

### Admin Endpoints

| Method | Endpoint                            | Controller Method                 | Description              |
| ------ | ----------------------------------- | --------------------------------- | ------------------------ |
| GET    | `/api/admin/dashboard`              | `AdminController@dashboard`       | Admin dashboard stats    |
| GET    | `/api/admin/users`                  | `AdminController@users`           | List all users           |
| GET    | `/api/admin/operators`              | `AdminController@operators`       | List all operators       |
| POST   | `/api/admin/operators/{id}/verify`  | `AdminController@verifyOperator`  | Verify operator account  |
| POST   | `/api/admin/operators/{id}/suspend` | `AdminController@suspendOperator` | Suspend operator account |
| GET    | `/api/admin/bookings`               | `AdminController@bookings`        | List all bookings        |

## web.php

### Purpose

Defines the public web-facing routes for the front-end booking flow.

### Controllers Used

- `App\Http\Controllers\LandingController`
- `App\Http\Controllers\BookingController`
- `App\Http\Controllers\ProfileController`
- `App\Http\Controllers\Operator\DashboardController`
- `App\Http\Controllers\Operator\TripManagementController`
- `App\Http\Controllers\Operator\ProfileController`
- `App\Http\Controllers\Auth\LoginController`
- `App\Http\Controllers\Auth\Operator\LoginController`

### Authentication Routes

| Method | URI               | Named Route      | Controller Method                       | Description                 |
| ------ | ----------------- | ---------------- | --------------------------------------- | --------------------------- |
| GET    | `/login`          | `login`          | `LoginController@showLoginForm`         | Display traveler login form |
| POST   | `/login`          | -                | `LoginController@login`                 | Process traveler login      |
| GET    | `/operator/login` | `operator.login` | `OperatorLoginController@showLoginForm` | Display operator login form |
| POST   | `/operator/login` | -                | `OperatorLoginController@login`         | Process operator login      |

### Traveler Authenticated Routes

| Method | URI                          | Named Route        | Controller Method                  | Description                 |
| ------ | ---------------------------- | ------------------ | ---------------------------------- | --------------------------- |
| POST   | `/logout`                    | `logout`           | `LoginController@logout`           | Logout traveler             |
| GET    | `/profile`                   | `profile`          | `ProfileController@index`          | Display traveler profile    |
| PUT    | `/profile`                   | `profile.update`   | `ProfileController@update`         | Update traveler profile     |
| PUT    | `/profile/password`          | `profile.password` | `ProfileController@updatePassword` | Update traveler password    |
| GET    | `/booking/{route}/seats`     | `booking.seats`    | `BookingController@showSeats`      | Seat selection page         |
| POST   | `/booking/store`             | `bookings.store`   | `BookingController@store`          | Create booking (form POST)  |
| GET    | `/payment/ticket/{booking}`  | `payment.ticket`   | `BookingController@paymentTicket`  | Payment ticket page         |
| POST   | `/payment/process/{booking}` | `payment.process`  | `BookingController@processPayment` | Process payment (form POST) |
| GET    | `/booking/success/{booking}` | `booking.success`  | `BookingController@success`        | Booking success page        |

### Front-end Routes

| Method | URI       | Named Route    | Controller Method          | View/Description           |
| ------ | --------- | -------------- | -------------------------- | -------------------------- |
| GET    | `/`       | `home`         | `LandingController@index`  | `landing_search.blade.php` |
| GET    | `/search` | `trips.search` | `LandingController@search` | `search_results.blade.php` |

### Operator Web Routes

| Method | URI                                | Named Route                    | Controller Method                          | Description               |
| ------ | ---------------------------------- | ------------------------------ | ------------------------------------------ | ------------------------- |
| GET    | `/operator`                        | `operator.dashboard`           | `DashboardController@index`                | Operator dashboard view   |
| POST   | `/operator/logout`                 | `operator.logout`              | `OperatorLoginController@logout`           | Logout operator           |
| GET    | `/operator/trips`                  | `operator.trips.index`         | `TripManagementController@index`           | Trip management dashboard |
| GET    | `/operator/trips/export`           | `operator.trips.export`        | `TripManagementController@export`          | Export trips as CSV       |
| POST   | `/operator/trips`                  | `operator.trips.store`         | `TripManagementController@store`           | Create new trip           |
| GET    | `/operator/trips/{trip}/seat-map`  | `operator.trips.seat-map`      | `TripManagementController@seatMap`         | View seat map for a trip  |
| GET    | `/operator/trips/{trip}/occupancy` | `operator.trips.occupancy`     | `TripManagementController@occupancy`       | Get seat occupancy (API)  |
| GET    | `/operator/trips/{trip}`           | `operator.trips.show`          | `TripManagementController@show`            | Get trip details (API)    |
| PUT    | `/operator/trips/{trip}`           | `operator.trips.update`        | `TripManagementController@update`          | Update trip               |
| DELETE | `/operator/trips/{trip}`           | `operator.trips.cancel`        | `TripManagementController@cancel`          | Cancel trip               |
| PATCH  | `/operator/trips/{trip}/status`    | `operator.trips.update-status` | `TripManagementController@updateStatus`    | Update trip status (API)  |
| GET    | `/operator/trips/stats`            | `operator.trips.stats`         | `TripManagementController@stats`           | Get trip statistics (API) |
| GET    | `/operator/trips/upcoming`         | `operator.trips.upcoming`      | `TripManagementController@upcoming`        | Get upcoming trips (API)  |
| GET    | `/operator/profile`                | `operator.profile`             | `OperatorProfileController@index`          | Operator profile page     |
| PUT    | `/operator/profile`                | `operator.profile.update`      | `OperatorProfileController@update`         | Update operator profile   |
| PUT    | `/operator/profile/password`       | `operator.profile.password`    | `OperatorProfileController@updatePassword` | Update operator password  |

### User Flow Mapping

```
/ (landing_search)
  → /search (search_results)
    → /booking/{route}/seats (seat_selection)
      → /booking/store (form POST)
        → /payment/ticket/{booking} (payment_ticket)
          → /payment/process/{booking} (form POST)
            → /booking/success/{booking} (history_page)
```

## console.php

### Purpose

Registers console commands for the application.

### Registered Command

| Command   | Method                  | Description                |
| --------- | ----------------------- | -------------------------- |
| `inspire` | `Artisan::command(...)` | Display an inspiring quote |

### Notes

- The command is registered with `Artisan::command(...)` and has purpose text `Display an inspiring quote`.
- This is a default Laravel command and can be run via `php artisan inspire`.

## Security Considerations

- All API routes use appropriate middleware guards
- Payment callback endpoint is public (called by payment gateways)
- Operator routes use a custom guard for separate authentication
- Admin routes require both authentication and role verification
