# Models Summary

This document provides a comprehensive summary of all models in the BookMyBus Zambia application.

## Overview

The application uses 15 Eloquent models for authentication, operator management, fleet and trip operations, bookings, payments, ticketing, pricing, notifications, and audit history:

- **User**: Travelers and admin users
- **Operator**: Bus companies and operator accounts
- **Bus**: Operator fleet vehicles
- **Driver**: Drivers assigned to trips
- **Route**: Scheduled trips between locations
- **RouteTemplate**: Reusable route definitions for creating trips
- **Booking**: Seat reservations and passenger details
- **Payment**: Payment transactions linked to bookings
- **Ticket**: Digital tickets and QR-code validation
- **PromoCode**: Operator discount codes
- **ServiceFee**: Operator booking fees
- **CancellationRule**: Operator refund policies
- **NotificationLog**: SMS and WhatsApp delivery records
- **AdminAuditLog**: Admin activity records
- **OperatorAuditLog**: Operator activity records

---

## User Model

### `app/Models/User.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Foundation\Auth\User`
- **Traits**: `HasApiTokens`, `HasFactory`, `Notifiable`, `SoftDeletes`

### Fillable Attributes

| Attribute            | Type    | Description                   |
| -------------------- | ------- | ----------------------------- |
| `full_name`          | string  | User's full name              |
| `email`              | string  | Email address                 |
| `phone_number`       | string  | Phone number                  |
| `password`           | string  | Hashed password               |
| `role`               | string  | `traveler` or `admin`         |
| `preferred_language` | string  | Preferred language            |
| `is_active`          | boolean | Whether the account is active |

### Relationships

| Method       | Type    | Related Model |
| ------------ | ------- | ------------- |
| `bookings()` | HasMany | Booking       |
| `tickets()`  | HasMany | Ticket        |

### Helper Methods

| Method         | Return Type | Description                                    |
| -------------- | ----------- | ---------------------------------------------- |
| `isAdmin()`    | bool        | Checks whether the user has the admin role.    |
| `isTraveler()` | bool        | Checks whether the user has the traveler role. |
| `isActive()`   | bool        | Returns the active-account flag.               |

---

## Operator Model

### `app/Models/Operator.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Foundation\Auth\User`
- **Traits**: `HasFactory`, `Notifiable`, `SoftDeletes`

### Key Attributes

| Attribute Group  | Attributes                                                                                                                                                          |
| ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Account          | `company_name`, `email`, `phone_number`, `password`, `tpin`, `address`                                                                                              |
| Verification     | `is_verified`, `verified_at`, `verified_by`                                                                                                                         |
| Business profile | `contact_person_name`, `contact_person_title`, `business_registration_number`, `business_registration_date`, `business_type`, `logo_path`, `description`, `website` |
| Compliance       | `insurance_certificate_path`, `insurance_expiry_date`, `business_license_path`, `business_license_verified_at`, `tax_id_path`, `tax_id_verified_at`                 |

### Relationships

| Method         | Type      | Related Model                          |
| -------------- | --------- | -------------------------------------- |
| `buses()`      | HasMany   | Bus                                    |
| `routes()`     | HasMany   | Route                                  |
| `verifiedBy()` | BelongsTo | User (admin who verified the operator) |

### Helper Methods

| Method         | Return Type | Description                              |
| -------------- | ----------- | ---------------------------------------- |
| `isVerified()` | bool        | Checks whether the operator is verified. |

---

## Bus Model

### `app/Models/Bus.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Fillable Attributes

| Attribute Group | Attributes                                                                  |
| --------------- | --------------------------------------------------------------------------- |
| Identity        | `operator_id`, `registration_number`, `model`, `seat_capacity`, `bus_class` |
| Configuration   | `amenities`, `is_active`                                                    |
| Maintenance     | `last_maintenance_date`, `next_maintenance_date`, `mileage_km`, `notes`     |

### Relationships

| Method       | Type      | Related Model |
| ------------ | --------- | ------------- |
| `operator()` | BelongsTo | Operator      |
| `routes()`   | HasMany   | Route         |

### Helper Methods

| Method                        | Return Type | Description                                                        |
| ----------------------------- | ----------- | ------------------------------------------------------------------ |
| `hasAmenity(string $amenity)` | bool        | Checks whether the amenities array contains the requested amenity. |

---

## Driver Model

### `app/Models/Driver.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Key Attributes

| Attribute Group      | Attributes                                                                   |
| -------------------- | ---------------------------------------------------------------------------- |
| Identity and contact | `operator_id`, `full_name`, `phone_number`, `email`, `address`, `photo_path` |
| Licensing            | `license_number`, `license_expiry_date`                                      |
| Status               | `is_active`, `notes`                                                         |

### Relationships

| Method       | Type      | Related Model |
| ------------ | --------- | ------------- |
| `operator()` | BelongsTo | Operator      |
| `routes()`   | HasMany   | Route         |

### Query Scopes

| Method                | Description                        |
| --------------------- | ---------------------------------- |
| `scopeActive($query)` | Filters drivers to active records. |

---

## Route Model

### `app/Models/Route.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Key Attributes

| Attribute Group | Attributes                                                                              |
| --------------- | --------------------------------------------------------------------------------------- |
| Assignment      | `operator_id`, `bus_id`, `driver_id`, `route_template_id`                               |
| Schedule        | `origin`, `destination`, `departure_time`, `arrival_time`, `travel_date`                |
| Pricing         | `distance_km`, `fare`                                                                   |
| Status          | `is_active`, `delayed_at`, `delay_minutes`, `delay_reason`, `departed_at`, `arrived_at` |

### Query Scopes

| Method                                                     | Description                                                              |
| ---------------------------------------------------------- | ------------------------------------------------------------------------ |
| `scopeSearch($query, $origin, $destination, $travel_date)` | Filters by partial origin and destination matches and exact travel date. |

### Relationships

| Method            | Type      | Related Model |
| ----------------- | --------- | ------------- |
| `operator()`      | BelongsTo | Operator      |
| `bus()`           | BelongsTo | Bus           |
| `driver()`        | BelongsTo | Driver        |
| `routeTemplate()` | BelongsTo | RouteTemplate |
| `bookings()`      | HasMany   | Booking       |

### Helper Methods

| Method                  | Return Type | Description                                         |
| ----------------------- | ----------- | --------------------------------------------------- |
| `bookedSeats()`         | array       | Returns seats in pending or confirmed bookings.     |
| `availableSeats()`      | array       | Returns currently available seat numbers.           |
| `availableSeatsCount()` | int         | Counts available seats.                             |
| `isDelayed()`           | bool        | Checks whether `delayed_at` is set.                 |
| `hasDeparted()`         | bool        | Checks whether `departed_at` is set.                |
| `hasArrived()`          | bool        | Checks whether `arrived_at` is set.                 |
| `confirmedPassengers()` | Collection  | Returns users with confirmed bookings on the route. |

---

## RouteTemplate Model

### `app/Models/RouteTemplate.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Fillable Attributes

| Attribute     | Type    | Description                    |
| ------------- | ------- | ------------------------------ |
| `operator_id` | int     | Owning operator                |
| `name`        | string  | Template name                  |
| `origin`      | string  | Starting location              |
| `destination` | string  | Destination location           |
| `distance_km` | decimal | Route distance                 |
| `base_fare`   | decimal | Template base fare             |
| `is_active`   | boolean | Whether the template is active |

### Relationships

| Method       | Type      | Related Model                           |
| ------------ | --------- | --------------------------------------- |
| `operator()` | BelongsTo | Operator                                |
| `trips()`    | HasMany   | Route records created from the template |

### Query Scopes

| Method                | Description                          |
| --------------------- | ------------------------------------ |
| `scopeActive($query)` | Filters templates to active records. |

---

## Booking Model

### `app/Models/Booking.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`, `SoftDeletes`

### Key Attributes

| Attribute Group | Attributes                                                                                             |
| --------------- | ------------------------------------------------------------------------------------------------------ |
| References      | `user_id`, `route_id`, `reference_id`                                                                  |
| Passenger       | `seat_number`, `passenger_name`, `passenger_id_number`, `passenger_phone`, `id_number`, `phone_number` |
| Fare            | `amount`, `base_fare`, `service_fee_total`, `discount_amount`, `promo_code_id`                         |
| Cancellation    | `cancellation_rule_id`, `refund_amount`, `cancelled_at`                                                |
| Operations      | `status`, `held_until`, `boarded_at`, `boarded_by`, `notes`                                            |

### Auto-Generated Attributes

- `reference_id`: Generated as `BMZ-` plus six random uppercase characters during creation.
- `held_until`: Set to 10 minutes after creation.

### Relationships

| Method               | Type      | Related Model    |
| -------------------- | --------- | ---------------- |
| `user()`             | BelongsTo | User             |
| `route()`            | BelongsTo | Route            |
| `payment()`          | HasOne    | Payment          |
| `ticket()`           | HasOne    | Ticket           |
| `promoCode()`        | BelongsTo | PromoCode        |
| `cancellationRule()` | BelongsTo | CancellationRule |

### Helper Methods

| Method                                        | Return Type | Description                                            |
| --------------------------------------------- | ----------- | ------------------------------------------------------ |
| `isExpired()`                                 | bool        | Checks whether a pending hold has expired.             |
| `isConfirmed()`                               | bool        | Checks whether status is `confirmed`.                  |
| `isBoarded()`                                 | bool        | Checks whether `boarded_at` is set.                    |
| `cancel()`                                    | void        | Changes status to `cancelled`.                         |
| `markBoarded(string $boardedBy = 'operator')` | void        | Marks the booking confirmed and records boarding data. |
| `undoBoarded()`                               | void        | Clears boarding timestamp and actor.                   |

---

## Payment Model

### `app/Models/Payment.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute               | Type     | Description                          |
| ----------------------- | -------- | ------------------------------------ |
| `booking_id`            | int      | Related booking                      |
| `amount`                | decimal  | Payment amount                       |
| `currency`              | string   | Currency, normally `ZMW`             |
| `payment_method`        | string   | Provider or payment method label     |
| `status`                | string   | `pending`, `successful`, or `failed` |
| `transaction_reference` | string   | Gateway transaction reference        |
| `gateway_response`      | array    | Gateway response data                |
| `paid_at`               | datetime | Successful payment timestamp         |

### Relationships

| Method      | Type      | Related Model |
| ----------- | --------- | ------------- |
| `booking()` | BelongsTo | Booking       |

### Helper Methods

| Method                                                                | Return Type | Description                                               |
| --------------------------------------------------------------------- | ----------- | --------------------------------------------------------- |
| `isSuccessful()`                                                      | bool        | Checks whether status is `successful`.                    |
| `markSuccessful(string $transactionRef, array $gatewayResponse = [])` | void        | Marks payment successful and confirms the linked booking. |
| `markFailed(array $gatewayResponse = [])`                             | void        | Marks payment failed and stores gateway data.             |

---

## Ticket Model

### `app/Models/Ticket.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute    | Type     | Description               |
| ------------ | -------- | ------------------------- |
| `booking_id` | int      | Related booking           |
| `user_id`    | int      | Ticket owner              |
| `qr_code`    | string   | Unique boarding QR code   |
| `status`     | string   | `issued` or `used`        |
| `issued_at`  | datetime | Ticket issuance timestamp |
| `used_at`    | datetime | Ticket usage timestamp    |

### Auto-Generated Attributes

- `qr_code`: Generated as `BMZ-QR-` plus an uppercase UUID during creation.
- `issued_at`: Set to the current time during creation.

### Relationships

| Method      | Type      | Related Model |
| ----------- | --------- | ------------- |
| `booking()` | BelongsTo | Booking       |
| `user()`    | BelongsTo | User          |

### Helper Methods

| Method         | Return Type | Description                               |
| -------------- | ----------- | ----------------------------------------- |
| `markAsUsed()` | void        | Marks the ticket used and sets `used_at`. |
| `isValid()`    | bool        | Checks whether status is `issued`.        |

---

## PromoCode Model

### `app/Models/PromoCode.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute Group | Attributes                                                                             |
| --------------- | -------------------------------------------------------------------------------------- |
| Ownership       | `operator_id`                                                                          |
| Discount        | `code`, `discount_type`, `discount_value`, `min_booking_amount`, `max_discount_amount` |
| Usage           | `usage_limit`, `used_count`                                                            |
| Availability    | `valid_from`, `valid_until`, `is_active`                                               |

### Relationships

| Method       | Type      | Related Model |
| ------------ | --------- | ------------- |
| `operator()` | BelongsTo | Operator      |

### Helper Methods

| Method                               | Return Type | Description                                                           |
| ------------------------------------ | ----------- | --------------------------------------------------------------------- |
| `isValid()`                          | bool        | Checks active status, validity dates, and usage limit.                |
| `calculateDiscount(float $subtotal)` | float       | Calculates fixed or percentage discount with an optional maximum cap. |
| `markUsed()`                         | void        | Increments the usage counter.                                         |

---

## ServiceFee Model

### `app/Models/ServiceFee.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute     | Type    | Description                  |
| ------------- | ------- | ---------------------------- |
| `operator_id` | int     | Owning operator              |
| `name`        | string  | Fee name                     |
| `fee_type`    | string  | Fixed or percentage fee type |
| `fee_value`   | decimal | Fee amount or percentage     |
| `is_active`   | boolean | Whether the fee is active    |

### Relationships

| Method       | Type      | Related Model |
| ------------ | --------- | ------------- |
| `operator()` | BelongsTo | Operator      |

### Helper Methods and Scopes

| Method                          | Description                                                     |
| ------------------------------- | --------------------------------------------------------------- |
| `calculateFee(float $subtotal)` | Returns a fixed fee or calculates a percentage of the subtotal. |
| `scopeActive($query)`           | Filters fees to active records.                                 |

---

## CancellationRule Model

### `app/Models/CancellationRule.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute                | Type    | Description                     |
| ------------------------ | ------- | ------------------------------- |
| `operator_id`            | int     | Owning operator                 |
| `name`                   | string  | Rule name                       |
| `hours_before_departure` | int     | Required hours before departure |
| `refund_percentage`      | decimal | Percentage refunded             |
| `is_active`              | boolean | Whether the rule is active      |

### Relationships

| Method       | Type      | Related Model |
| ------------ | --------- | ------------- |
| `operator()` | BelongsTo | Operator      |

### Query Scopes

| Method                | Description                                                                |
| --------------------- | -------------------------------------------------------------------------- |
| `scopeActive($query)` | Filters active rules and orders them by hours before departure descending. |

---

## NotificationLog Model

### `app/Models/NotificationLog.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`
- **Traits**: `HasFactory`

### Fillable Attributes

| Attribute                          | Description                                  |
| ---------------------------------- | -------------------------------------------- |
| `notifiable_type`, `notifiable_id` | Polymorphic recipient identity               |
| `channel`                          | Delivery channel such as `sms` or `whatsapp` |
| `notification_type`                | Fully qualified notification class           |
| `subject`, `body`                  | Message preview and full body                |
| `status`                           | Delivery status                              |
| `gateway_message_id`               | Provider or simulated gateway ID             |
| `sent_at`                          | Delivery timestamp                           |

### Relationships

| Method         | Type    | Related Model          |
| -------------- | ------- | ---------------------- |
| `notifiable()` | MorphTo | Notification recipient |

---

## AdminAuditLog Model

### `app/Models/AdminAuditLog.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`

### Fillable Attributes

| Attribute Group  | Attributes                         |
| ---------------- | ---------------------------------- |
| Actor and event  | `admin_id`, `event`, `description` |
| Auditable target | `auditable_type`, `auditable_id`   |
| Changes          | `old_values`, `new_values`         |
| Request context  | `ip_address`, `user_agent`         |

### Relationships

| Method        | Type      | Related Model |
| ------------- | --------- | ------------- |
| `admin()`     | BelongsTo | User          |
| `auditable()` | MorphTo   | Audited model |

### Helper Methods

| Method         | Return Type | Description                                                                               |
| -------------- | ----------- | ----------------------------------------------------------------------------------------- |
| `eventLabel()` | string      | Converts known event keys to readable labels and formats unknown keys as fallback labels. |

---

## OperatorAuditLog Model

### `app/Models/OperatorAuditLog.php`

- **Namespace**: `App\Models`
- **Extends**: `Illuminate\Database\Eloquent\Model`

### Fillable Attributes

| Attribute Group  | Attributes                            |
| ---------------- | ------------------------------------- |
| Actor and event  | `operator_id`, `event`, `description` |
| Auditable target | `auditable_type`, `auditable_id`      |
| Changes          | `old_values`, `new_values`            |
| Request context  | `ip_address`, `user_agent`            |

### Relationships

| Method        | Type      | Related Model |
| ------------- | --------- | ------------- |
| `operator()`  | BelongsTo | Operator      |
| `auditable()` | MorphTo   | Audited model |

### Helper Methods

| Method         | Return Type | Description                                                                                        |
| -------------- | ----------- | -------------------------------------------------------------------------------------------------- |
| `eventLabel()` | string      | Converts known operator event keys to readable labels and formats unknown keys as fallback labels. |

---

## Model Summary Table

| Model            | File Path                         | Relationships                                                            | Key Features                              |
| ---------------- | --------------------------------- | ------------------------------------------------------------------------ | ----------------------------------------- |
| User             | `app/Models/User.php`             | HasMany(Booking), HasMany(Ticket)                                        | Authentication, roles, active status      |
| Operator         | `app/Models/Operator.php`         | HasMany(Bus), HasMany(Route), BelongsTo(User)                            | Company profile, verification, compliance |
| Bus              | `app/Models/Bus.php`              | BelongsTo(Operator), HasMany(Route)                                      | Fleet and maintenance management          |
| Driver           | `app/Models/Driver.php`           | BelongsTo(Operator), HasMany(Route)                                      | Driver records and licensing              |
| Route            | `app/Models/Route.php`            | BelongsTo(Operator/Bus/Driver/RouteTemplate), HasMany(Booking)           | Search, seats, trip status                |
| RouteTemplate    | `app/Models/RouteTemplate.php`    | BelongsTo(Operator), HasMany(Route)                                      | Reusable trip definitions                 |
| Booking          | `app/Models/Booking.php`          | BelongsTo(User/Route/PromoCode/CancellationRule), HasOne(Payment/Ticket) | Holds, fare breakdown, boarding           |
| Payment          | `app/Models/Payment.php`          | BelongsTo(Booking)                                                       | Payment lifecycle and confirmation        |
| Ticket           | `app/Models/Ticket.php`           | BelongsTo(Booking), BelongsTo(User)                                      | QR code and validation                    |
| PromoCode        | `app/Models/PromoCode.php`        | BelongsTo(Operator)                                                      | Discounts, validity, usage limits         |
| ServiceFee       | `app/Models/ServiceFee.php`       | BelongsTo(Operator)                                                      | Fixed or percentage booking fees          |
| CancellationRule | `app/Models/CancellationRule.php` | BelongsTo(Operator)                                                      | Time-based refund policies                |
| NotificationLog  | `app/Models/NotificationLog.php`  | MorphTo(notifiable)                                                      | SMS and WhatsApp delivery history         |
| AdminAuditLog    | `app/Models/AdminAuditLog.php`    | BelongsTo(User), MorphTo(auditable)                                      | Admin activity history                    |
| OperatorAuditLog | `app/Models/OperatorAuditLog.php` | BelongsTo(Operator), MorphTo(auditable)                                  | Operator activity history                 |

---

## Entity Relationship Diagram (Text)

```
User
  ├── bookings (hasMany) ──> Booking
  └── tickets (hasMany) ──> Ticket

Operator
  ├── buses (hasMany) ──> Bus
  ├── routes (hasMany) ──> Route
  ├── verifiedBy (belongsTo) ──> User
  ├── route templates (hasMany) ──> RouteTemplate
  ├── promo codes (hasMany) ──> PromoCode
  ├── service fees (hasMany) ──> ServiceFee
  └── cancellation rules (hasMany) ──> CancellationRule

Bus
  ├── operator (belongsTo) ──> Operator
  └── routes (hasMany) ──> Route

Driver
  ├── operator (belongsTo) ──> Operator
  └── routes (hasMany) ──> Route

RouteTemplate
  ├── operator (belongsTo) ──> Operator
  └── trips (hasMany) ──> Route

Route
  ├── operator (belongsTo) ──> Operator
  ├── bus (belongsTo) ──> Bus
  ├── driver (belongsTo) ──> Driver
  ├── routeTemplate (belongsTo) ──> RouteTemplate
  └── bookings (hasMany) ──> Booking

Booking
  ├── user (belongsTo) ──> User
  ├── route (belongsTo) ──> Route
  ├── payment (hasOne) ──> Payment
  ├── ticket (hasOne) ──> Ticket
  ├── promoCode (belongsTo) ──> PromoCode
  └── cancellationRule (belongsTo) ──> CancellationRule

NotificationLog
  └── notifiable (morphTo) ──> Notification recipient

AdminAuditLog
  ├── admin (belongsTo) ──> User
  └── auditable (morphTo) ──> Audited model

OperatorAuditLog
  ├── operator (belongsTo) ──> Operator
  └── auditable (morphTo) ──> Audited model
```

---

## Key Features by Model

### User Management

- **User**: Traveler/admin authentication with Sanctum API tokens, role checks, and active status
- **Operator**: Bus company authentication, profile, verification, and compliance workflow

### Fleet and Booking System

- **Bus**: Fleet management with amenities and maintenance tracking
- **Driver**: Operator-owned driver records with license and active-status tracking
- **Route**: Location-based search, seat availability, driver assignment, and operational status
- **RouteTemplate**: Reusable route definitions for trip generation
- **Booking**: Seat reservation with 10-minute hold, generated references, fare breakdown, cancellation, and boarding state

### Pricing, Payment, and Ticketing

- **PromoCode**: Fixed or percentage discounts with date and usage validation
- **ServiceFee**: Operator-specific fixed or percentage fees
- **CancellationRule**: Time-based refund policy configuration
- **Payment**: Mobile-money payment lifecycle, gateway responses, and booking confirmation
- **Ticket**: QR code generation, ticket validation, and usage tracking

### Notifications and Auditing

- **NotificationLog**: Persistent SMS and WhatsApp delivery results
- **AdminAuditLog**: Admin actions with target, request, and before/after values
- **OperatorAuditLog**: Operator actions with target, request, and before/after values
