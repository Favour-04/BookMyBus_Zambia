# Routes Directory Summary

This summary covers the three route files in the `routes/` directory: `api.php`, `web.php`, and `console.php`.

## api.php

### Purpose

Defines the API endpoints for BookMyBus Zambia, including public search endpoints, traveler authentication/bookings/payments/tickets, operator routes, and admin functionality.

### Authentication Guards

| Guard                         | Model    | Description                                                       |
| ----------------------------- | -------- | ----------------------------------------------------------------- |
| `auth:sanctum`                | User     | Traveler authentication using Laravel Sanctum                     |
| `auth:operator`               | Operator | Session guard for operator authentication (see `config/auth.php`) |
| `auth:sanctum` + `role:admin` | User     | Admin users with admin role                                       |

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
- `App\Http\Controllers\Operator\BookingManagementController`
- `App\Http\Controllers\Operator\RevenueController`
- `App\Http\Controllers\Operator\CustomerController`
- `App\Http\Controllers\Operator\ProfileController`
- `App\Http\Controllers\Operator\FareRuleController`
- `App\Http\Controllers\Operator\PromoCodeController`
- `App\Http\Controllers\Operator\RouteTemplateController`
- `App\Http\Controllers\Auth\LoginController`
- `App\Http\Controllers\Auth\RegisterController`
- `App\Http\Controllers\Auth\PasswordResetController`
- `App\Http\Controllers\Auth\Operator\LoginController`
- `App\Http\Controllers\Auth\Admin\LoginController`
- Additional fully-qualified operator and admin controllers are listed in the route tables below.

### Authentication Routes

| Method | URI                       | Named Route        | Controller Method                             | Description                         |
| ------ | ------------------------- | ------------------ | --------------------------------------------- | ----------------------------------- |
| GET    | `/login`                  | `login`            | `LoginController@showLoginForm`               | Display traveler login form         |
| POST   | `/login`                  | -                  | `LoginController@login`                       | Process traveler login              |
| GET    | `/register`               | `register`         | `RegisterController@showRegistrationForm`     | Display traveler registration form  |
| POST   | `/register`               | -                  | `RegisterController@register`                 | Register traveler                   |
| GET    | `/forgot-password`        | `password.request` | `PasswordResetController@showLinkRequestForm` | Display password reset request form |
| POST   | `/forgot-password`        | `password.email`   | `PasswordResetController@sendResetLinkEmail`  | Send password reset link            |
| GET    | `/reset-password/{token}` | `password.reset`   | `PasswordResetController@showResetForm`       | Display password reset form         |
| POST   | `/reset-password`         | `password.update`  | `PasswordResetController@reset`               | Reset traveler password             |
| GET    | `/operator/login`         | `operator.login`   | `OperatorLoginController@showLoginForm`       | Display operator login form         |
| POST   | `/operator/login`         | -                  | `OperatorLoginController@login`               | Process operator login              |
| GET    | `/admin/login`            | `admin.login`      | `Admin\LoginController@showLoginForm`         | Display admin login form            |
| POST   | `/admin/login`            | -                  | `Admin\LoginController@login`                 | Process admin login                 |

Guest middleware: traveler authentication routes use `guest`, operator routes use `guest:operator`, and admin routes use `guest:admin`.

### Traveler Authenticated Routes

| Method | URI                          | Named Route              | Controller Method                      | Description              |
| ------ | ---------------------------- | ------------------------ | -------------------------------------- | ------------------------ |
| POST   | `/logout`                    | `logout`                 | `LoginController@logout`               | Logout traveler          |
| GET    | `/booking/{route}/seats`     | `booking.seats`          | `BookingController@showSeats`          | Seat selection page      |
| POST   | `/booking/store`             | `bookings.store`         | `BookingController@store`              | Create booking           |
| GET    | `/payment/ticket/{booking}`  | `payment.ticket`         | `BookingController@paymentTicket`      | Payment ticket page      |
| POST   | `/payment/process/{booking}` | `payment.process`        | `BookingController@processPayment`     | Process payment          |
| GET    | `/booking/success/{booking}` | `booking.success`        | `BookingController@success`            | Booking success page     |
| GET    | `/tickets/{qrCode}`          | `tickets.show`           | `BookingController@showTicket`         | Re-open a digital ticket |
| POST   | `/bookings/{booking}/cancel` | `bookings.cancel`        | `BookingController@cancel`             | Cancel a booking         |
| GET    | `/my-booking`                | `booking.lookup`         | `BookingController@customerLookupView` | Display booking lookup   |
| POST   | `/my-booking/lookup`         | `booking.lookup.search`  | `BookingController@customerLookup`     | Look up a booking        |
| POST   | `/booking/validate-promo`    | `booking.validate-promo` | `BookingController@validatePromoCode`  | Validate a promo code    |
| GET    | `/profile`                   | `profile`                | `ProfileController@index`              | Display traveler profile |
| PUT    | `/profile`                   | `profile.update`         | `ProfileController@update`             | Update traveler profile  |
| PUT    | `/profile/password`          | `profile.password`       | `ProfileController@updatePassword`     | Update traveler password |

### Front-end Routes

| Method | URI                 | Named Route        | Controller Method          | View/Description           |
| ------ | ------------------- | ------------------ | -------------------------- | -------------------------- |
| GET    | `/`                 | `home`             | `LandingController@index`  | `landing_search.blade.php` |
| GET    | `/search`           | `trips.search`     | `LandingController@search` | `search_results.blade.php` |
| GET    | `/support`          | `support.page`     | Closure                    | `support_page`             |
| GET    | `/privacy-policy`   | `privacy-policy`   | View route                 | `privacy_policy`           |
| GET    | `/terms-of-service` | `terms-of-service` | View route                 | `terms_of_service`         |
| GET    | `/carrier-partners` | `carrier-partners` | View route                 | `carrier_partners`         |
| GET    | `/contact-us`       | `contact-us`       | View route                 | `contact_us`               |

### Operator Web Routes

All routes in this section are prefixed with `/operator`, use the `operator.` name prefix, and require `auth:operator`.

| Route Group           | Endpoints                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               | Controllers / Purpose                                            |
| --------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------- |
| Dashboard and reports | `GET /operator`, `GET /operator/revenue`                                                                                                                                                                                                                                                                                                                                                                                                                                                                | `DashboardController@index`, `RevenueController@index`           |
| Buses                 | `GET /operator/buses`, `GET /operator/buses/{bus}`, `POST /operator/buses`, `PUT /operator/buses/{bus}`, `POST /operator/buses/{bus}/toggle-status`, `DELETE /operator/buses/{bus}`                                                                                                                                                                                                                                                                                                                     | `Operator\BusController`; fleet management                       |
| Audit log             | `GET /operator/audit-log`, `GET /operator/audit-log/{id}`                                                                                                                                                                                                                                                                                                                                                                                                                                               | `Operator\AuditLogController`; operator audit records            |
| Customers             | `GET /operator/customers`, `GET /operator/customers/{customer}`                                                                                                                                                                                                                                                                                                                                                                                                                                         | `CustomerController`; customer management                        |
| Trips                 | `GET /operator/trips`, `GET /operator/trips/calendar`, `GET /operator/trips/export`, `GET /operator/trips/stats`, `GET /operator/trips/upcoming`, `POST /operator/trips`                                                                                                                                                                                                                                                                                                                                | `TripManagementController`; trip management and reporting        |
| Trip details          | `GET /operator/trips/{trip}/seat-map`, `GET /operator/trips/{trip}/occupancy`, `GET /operator/trips/{trip}/bookings`, `GET /operator/trips/{trip}`, `PUT /operator/trips/{trip}`, `DELETE /operator/trips/{trip}`, `PATCH /operator/trips/{trip}/status`                                                                                                                                                                                                                                                | `TripManagementController` and `BookingManagementController`     |
| Trip actions          | `POST /operator/trips/{trip}/delay`, `POST /operator/trips/{trip}/depart`, `POST /operator/trips/{trip}/arrive`, `POST /operator/trips/{trip}/assign-driver`, `GET /operator/trips/{trip}/json`, `POST /operator/trips/{trip}/cancel-notify`                                                                                                                                                                                                                                                            | `TripManagementController`; operational status and notifications |
| Bookings              | `GET /operator/bookings`, `GET /operator/bookings/export`, `POST /operator/bookings/bulk-action`, `PATCH /operator/bookings/{booking}/cancel`, `POST /operator/bookings/{booking}/process-refund`, `PATCH /operator/bookings/{booking}/board`, `PATCH /operator/bookings/{booking}/undo-board`, `PATCH /operator/bookings/{booking}/notes`, `GET /operator/bookings/{booking}/edit`, `PUT /operator/bookings/{booking}`, `GET /operator/bookings/{booking}/receipt`, `GET /operator/bookings/{booking}` | `BookingManagementController`; booking operations                |
| Fare rules and fees   | `GET /operator/fare-rules`, cancellation-rule CRUD under `/operator/fare-rules/cancellation`, service-fee CRUD under `/operator/fare-rules/service-fees`                                                                                                                                                                                                                                                                                                                                                | `FareRuleController`                                             |
| Promo codes           | `GET`, `POST /operator/promo-codes`, `PUT /operator/promo-codes/{id}`, `DELETE /operator/promo-codes/{id}`                                                                                                                                                                                                                                                                                                                                                                                              | `PromoCodeController`                                            |
| Route templates       | `GET`, `POST /operator/route-templates`, `PUT /operator/route-templates/{id}`, `DELETE /operator/route-templates/{id}`, `POST /operator/route-templates/{id}/create-trip`, `POST /operator/route-templates/{id}/create-bulk-trips`, `GET /operator/route-templates/json`                                                                                                                                                                                                                                | `RouteTemplateController`                                        |
| Profile and schedule  | `GET/PUT /operator/profile`, `PUT /operator/profile/password`, `GET /operator/schedule/printable`                                                                                                                                                                                                                                                                                                                                                                                                       | `OperatorProfileController`, `TripManagementController`          |
| Passengers            | `GET /operator/passengers`, `GET /operator/passengers/manifest/{routeId}`, `GET /operator/passengers/export/{routeId}`, `POST /operator/passengers/bulk-checkin`                                                                                                                                                                                                                                                                                                                                        | `Operator\PassengerListController`                               |
| Drivers               | `GET /operator/drivers`, `POST /operator/drivers`, `PUT /operator/drivers/{id}`, `GET /operator/drivers/{id}/json`, `DELETE /operator/drivers/{id}`                                                                                                                                                                                                                                                                                                                                                     | `Operator\DriverController`                                      |
| Logout                | `POST /operator/logout`                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 | `OperatorLoginController@logout`                                 |

### Admin Web Routes

All routes in this section are prefixed with `/admin`, use the `admin.` name prefix, and require `auth:admin`.

| Route Group        | Endpoints                                                                                                                                                      | Controller / Purpose                              |
| ------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------- |
| Dashboard          | `GET /admin/dashboard`                                                                                                                                         | `Admin\DashboardController@index`                 |
| Operators          | `GET /admin/operators`, `GET /admin/operators/{id}`, `POST /admin/operators/{id}/verify`, `POST /admin/operators/{id}/suspend`, `DELETE /admin/operators/{id}` | `Admin\OperatorController`; operator management   |
| Audit log          | `GET /admin/audit-log`, `GET /admin/audit-log/{id}`                                                                                                            | `Admin\AuditLogController`                        |
| Profile            | `GET/PUT /admin/profile`, `PUT /admin/profile/password`                                                                                                        | `Admin\ProfileController`                         |
| Bookings           | `GET /admin/bookings`, `GET /admin/bookings/{id}`                                                                                                              | `Admin\BookingController`                         |
| Payments and trips | `GET /admin/payments`, `GET /admin/trips`                                                                                                                      | `Admin\PaymentController`, `Admin\TripController` |
| Reports            | `GET /admin/reports`, `GET /admin/reports/export`, `GET /admin/reports/data`                                                                                   | `Admin\ReportController`                          |
| Users              | `GET /admin/users`, `GET /admin/users/{id}`, `POST /admin/users/{id}/suspend`, `POST /admin/users/{id}/activate`, `DELETE /admin/users/{id}`                   | `Admin\UserController`                            |
| Logout             | `POST /admin/logout`                                                                                                                                           | `Auth\Admin\LoginController@logout`               |

### User Flow Mapping

```
/ (landing_search)
  → /search (search_results)
    → /booking/{route}/seats (seat_selection)
      → /booking/store (form POST)
        → /payment/ticket/{booking} (payment_ticket)
          → /payment/process/{booking} (form POST)
            → /booking/success/{booking} (booking success page)

          The authenticated traveler can also use `/my-booking` for booking lookup and `/tickets/{qrCode}` to reopen a digital ticket.
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
- `notifications:departure-reminders` is scheduled with `Schedule::command(...)` every 15 minutes and uses `withoutOverlapping()`.
- The scheduler command sends departure reminders to confirmed passengers on trips departing soon.

## Security Considerations

- All API routes use appropriate middleware guards
- Payment callback endpoint is public (called by payment gateways)
- Operator routes use a custom guard for separate authentication
- Admin routes require both authentication and role verification
