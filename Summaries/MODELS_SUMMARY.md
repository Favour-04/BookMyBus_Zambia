# Models Summary

This document provides a comprehensive summary of all models in the BookMyBus Zambia application.

## Overview

The application uses 6 Eloquent models to represent the core domain entities:

- **User**: Travelers using the platform
- **Operator**: Bus companies/operators
- **Bus**: Bus vehicles in the fleet
- **Route**: Travel routes between locations
- **Booking**: Seat reservations
- **Payment**: Payment transactions
- **Ticket**: Digital tickets issued after successful payments

---

## User Model

### `app/Models/User.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Foundation\Auth\User`
- **Traits**: `HasApiTokens`, `HasFactory`, `Notifiable`, `SoftDeletes`

### Fillable Attributes

| Attribute            | Type   | Description                 |
| -------------------- | ------ | --------------------------- |
| `full_name`          | string | User's full name            |
| `email`              | string | Unique email address        |
| `phone_number`       | string | Unique phone number         |
| `password`           | string | Hashed password             |
| `role`               | string | User role (traveler, admin) |
| `preferred_language` | string | User's preferred language   |

### Relationships

| Method       | Type    | Related Model |
| ------------ | ------- | ------------- |
| `bookings()` | HasMany | Booking       |
| `tickets()`  | HasMany | Ticket        |

### Helper Methods

| Method         | Return Type | Description                      |
| -------------- | ----------- | -------------------------------- |
| `isAdmin()`    | bool        | Checks if user has admin role    |
| `isTraveler()` | bool        | Checks if user has traveler role |

---

## Operator Model

### `app/Models/Operator.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Foundation\Auth\User`
- **Traits**: `HasFactory`, `Notifiable`, `SoftDeletes`

### Fillable Attributes

| Attribute      | Type     | Description                               |
| -------------- | -------- | ----------------------------------------- |
| `company_name` | string   | Name of the bus company                   |
| `email`        | string   | Unique email address                      |
| `phone_number` | string   | Phone number                              |
| `password`     | string   | Hashed password                           |
| `tpin`         | string   | Taxpayer Identification Number (optional) |
| `is_verified`  | boolean  | Whether operator is verified by admin     |
| `verified_at`  | datetime | Timestamp of verification                 |
| `verified_by`  | int      | ID of admin who verified                  |
| `address`      | string   | Company address (optional)                |

### Relationships

| Method         | Type      | Related Model             |
| -------------- | --------- | ------------------------- |
| `buses()`      | HasMany   | Bus                       |
| `routes()`     | HasMany   | Route                     |
| `verifiedBy()` | BelongsTo | User (admin who verified) |

### Helper Methods

| Method         | Return Type | Description                    |
| -------------- | ----------- | ------------------------------ |
| `isVerified()` | bool        | Checks if operator is verified |

---

## Bus Model

### `app/Models/Bus.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Fillable Attributes

| Attribute             | Type    | Description                      |
| --------------------- | ------- | -------------------------------- |
| `operator_id`         | int     | Foreign key to Operator          |
| `registration_number` | string  | Unique bus registration number   |
| `model`               | string  | Bus model name (optional)        |
| `seat_capacity`       | int     | Number of seats (1-100)          |
| `bus_class`           | string  | Class: economy, business, luxury |
| `amenities`           | array   | List of amenities (optional)     |
| `is_active`           | boolean | Whether bus is active            |

### Relationships

| Method       | Type      | Related Model |
| ------------ | --------- | ------------- |
| `operator()` | BelongsTo | Operator      |
| `routes()`   | HasMany   | Route         |

### Helper Methods

| Method                        | Return Type | Description                          |
| ----------------------------- | ----------- | ------------------------------------ |
| `hasAmenity(string $amenity)` | bool        | Checks if bus has a specific amenity |

---

## Route Model

### `app/Models/Route.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Fillable Attributes

| Attribute        | Type    | Description                         |
| ---------------- | ------- | ----------------------------------- |
| `operator_id`    | int     | Foreign key to Operator             |
| `bus_id`         | int     | Foreign key to Bus                  |
| `origin`         | string  | Origin location                     |
| `destination`    | string  | Destination location                |
| `distance_km`    | decimal | Distance in kilometers              |
| `departure_time` | string  | Departure time (H:i format)         |
| `arrival_time`   | string  | Arrival time (H:i format, optional) |
| `fare`           | decimal | Ticket fare                         |
| `travel_date`    | date    | Date of travel                      |
| `is_active`      | boolean | Whether route is active             |

### Query Scopes

| Method                                                     | Description                                            |
| ---------------------------------------------------------- | ------------------------------------------------------ |
| `scopeSearch($query, $origin, $destination, $travel_date)` | Filters routes by origin, destination, and travel date |

### Relationships

| Method       | Type      | Related Model |
| ------------ | --------- | ------------- |
| `operator()` | BelongsTo | Operator      |
| `bus()`      | BelongsTo | Bus           |
| `bookings()` | HasMany   | Booking       |

### Helper Methods

| Method                  | Return Type | Description                             |
| ----------------------- | ----------- | --------------------------------------- |
| `bookedSeats()`         | array       | Returns array of booked seat numbers    |
| `availableSeats()`      | array       | Returns array of available seat numbers |
| `availableSeatsCount()` | int         | Returns count of available seats        |

---

## Booking Model

### `app/Models/Booking.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Fillable Attributes

| Attribute      | Type     | Description                               |
| -------------- | -------- | ----------------------------------------- |
| `user_id`      | int      | Foreign key to User                       |
| `route_id`     | int      | Foreign key to Route                      |
| `seat_number`  | int      | Seat number being booked                  |
| `amount`       | decimal  | Booking amount                            |
| `status`       | string   | Status: pending, confirmed, cancelled     |
| `held_until`   | datetime | Seat hold expiration time                 |
| `reference_id` | string   | Unique booking reference (auto-generated) |

### Auto-Generated Attributes

- `reference_id`: Generated as 'BMZ-' + 6 random characters on creation
- `held_until`: Set to 10 minutes from creation time

### Relationships

| Method      | Type      | Related Model |
| ----------- | --------- | ------------- |
| `user()`    | BelongsTo | User          |
| `route()`   | BelongsTo | Route         |
| `payment()` | HasOne    | Payment       |
| `ticket()`  | HasOne    | Ticket        |

### Helper Methods

| Method          | Return Type | Description                                |
| --------------- | ----------- | ------------------------------------------ |
| `isExpired()`   | bool        | Checks if pending booking hold has expired |
| `isConfirmed()` | bool        | Checks if booking is confirmed             |
| `cancel()`      | void        | Updates status to 'cancelled'              |

---

## Payment Model

### `app/Models/Payment.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute               | Type     | Description                                   |
| ----------------------- | -------- | --------------------------------------------- |
| `booking_id`            | int      | Foreign key to Booking                        |
| `amount`                | decimal  | Payment amount                                |
| `currency`              | string   | Currency (ZMW)                                |
| `payment_method`        | string   | Method: mtn_money, airtel_money, zanaco, card |
| `status`                | string   | Status: pending, successful, failed           |
| `transaction_reference` | string   | Gateway transaction reference                 |
| `gateway_response`      | array    | Response from payment gateway                 |
| `paid_at`               | datetime | Payment completion timestamp                  |

### Relationships

| Method      | Type      | Related Model |
| ----------- | --------- | ------------- |
| `booking()` | BelongsTo | Booking       |

### Helper Methods

| Method                                                           | Return Type | Description                                      |
| ---------------------------------------------------------------- | ----------- | ------------------------------------------------ |
| `isSuccessful()`                                                 | bool        | Checks if payment was successful                 |
| `markSuccessful(string $transactionRef, array $gatewayResponse)` | void        | Marks payment as successful and confirms booking |
| `markFailed(array $gatewayResponse)`                             | void        | Marks payment as failed                          |

---

## Ticket Model

### `app/Models/Ticket.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute    | Type     | Description                     |
| ------------ | -------- | ------------------------------- |
| `booking_id` | int      | Foreign key to Booking          |
| `user_id`    | int      | Foreign key to User             |
| `qr_code`    | string   | Unique QR code (auto-generated) |
| `status`     | string   | Status: issued, used            |
| `issued_at`  | datetime | Ticket issuance timestamp       |
| `used_at`    | datetime | Ticket usage timestamp          |

### Auto-Generated Attributes

- `qr_code`: Generated as 'BMZ-QR-' + UUID on creation
- `issued_at`: Set to current time on creation

### Relationships

| Method      | Type      | Related Model |
| ----------- | --------- | ------------- |
| `booking()` | BelongsTo | Booking       |
| `user()`    | BelongsTo | User          |

### Helper Methods

| Method         | Return Type | Description                                         |
| -------------- | ----------- | --------------------------------------------------- |
| `markAsUsed()` | void        | Updates status to 'used' and sets used_at timestamp |
| `isValid()`    | bool        | Checks if ticket status is 'issued'                 |

---

## Model Summary Table

| Model    | File Path                 | Relationships                                                      | Key Features                      |
| -------- | ------------------------- | ------------------------------------------------------------------ | --------------------------------- |
| User     | `app/Models/User.php`     | HasMany(Booking), HasMany(Ticket)                                  | Authentication, role-based access |
| Operator | `app/Models/Operator.php` | HasMany(Bus), HasMany(Route), BelongsTo(User)                      | Company management, verification  |
| Bus      | `app/Models/Bus.php`      | BelongsTo(Operator), HasMany(Route)                                | Fleet management, amenities       |
| Route    | `app/Models/Route.php`    | BelongsTo(Operator), BelongsTo(Bus), HasMany(Booking)              | Search scope, seat availability   |
| Booking  | `app/Models/Booking.php`  | BelongsTo(User), BelongsTo(Route), HasOne(Payment), HasOne(Ticket) | Auto reference ID, seat hold      |
| Payment  | `app/Models/Payment.php`  | BelongsTo(Booking)                                                 | Payment gateway integration       |
| Ticket   | `app/Models/Ticket.php`   | BelongsTo(Booking), BelongsTo(User)                                | QR code, ticket validation        |

---

## Entity Relationship Diagram (Text)

```
User
  └── bookings (hasMany) ──> Booking
  └── tickets (hasMany) ──> Ticket

Operator
  └── buses (hasMany) ──> Bus
  └── routes (hasMany) ──> Route
  └── verifiedBy (belongsTo) ──> User

Bus
  └── operator (belongsTo) ──> Operator
  └── routes (hasMany) ──> Route

Route
  └── operator (belongsTo) ──> Operator
  └── bus (belongsTo) ──> Bus
  └── bookings (hasMany) ──> Booking

Booking
  └── user (belongsTo) ──> User
  └── route (belongsTo) ──> Route
  └── payment (hasOne) ──> Payment
  └── ticket (hasOne) ──> Ticket

Payment
  └── booking (belongsTo) ──> Booking

Ticket
  └── booking (belongsTo) ──> Booking
  └── user (belongsTo) ──> User
```

---

## Key Features by Model

### User Management

- **User**: Traveler authentication with Sanctum API tokens, role-based access control
- **Operator**: Bus company authentication with admin verification workflow

### Booking System

- **Booking**: Seat reservation with 10-minute hold, auto-generated reference IDs, status management
- **Route**: Location-based search, seat availability calculations
- **Bus**: Fleet management with amenity tracking

### Payment & Ticketing

- **Payment**: Multiple payment method support (MTN Money, Airtel Money, Zanaco, card), gateway integration
- **Ticket**: QR code generation, ticket validation and usage tracking
