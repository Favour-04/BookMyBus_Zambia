# Services Directory Summary

This document provides a comprehensive summary of the reusable application services and gateway abstractions in `app/Services`.

## Overview

The services layer centralizes domain calculations, payment processing, audit logging, and external communication. It contains six top-level services and two gateway namespaces:

- **Fare calculation**: Computes booking totals, service fees, and promo discounts.
- **Payment processing**: Coordinates mobile-money payments, payment records, booking confirmation, and ticket issuance.
- **Refund calculation**: Applies operator cancellation rules and records refund details.
- **Audit logging**: Records admin and operator activity with actor, target, request, and value-change information.
- **Mobile-money gateways**: Defines and simulates MTN MoMo and Airtel Money charging.
- **Messaging gateways**: Defines simulated and Africa's Talking delivery for SMS and WhatsApp notifications.

## Top-Level Services

### `app/Services/FareCalculationService.php`

- **Namespace**: `App\Services`
- **Purpose**: Calculates the complete fare for a route booking and validates operator promo codes.
- **Models Used**: `Route`, `PromoCode`, `ServiceFee`

| Method                                                              | Description                                                                                                                                                                                |
| ------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `calculate(Route $route, ?PromoCode $promoCode = null)`             | Calculates the base fare, active operator service fees, subtotal, applicable promo discount, and final rounded total. Returns the service-fee breakdown and promo-code ID with the totals. |
| `validatePromoCode(string $code, int $operatorId, float $subtotal)` | Finds and validates a promo code for an operator and subtotal. Returns validity, the promo model, discount where applicable, and a user-facing message.                                    |

**Business Rules**:

- Only active service fees belonging to the route's operator are included.
- A promo code must belong to the route's operator, be valid, and meet its minimum booking amount.
- The final total is rounded to two decimal places after the discount is applied.

**Used By**: Web booking and operator fare-rule controllers.

---

### `app/Services/PaymentService.php`

- **Namespace**: `App\Services`
- **Purpose**: Processes mobile-money payments for bookings through a provider gateway.
- **Models Used**: `Booking`, `Payment`, `Ticket`
- **Notifications**: `TicketIssued`
- **Gateway Dependency**: `MobileMoney\GatewayInterface`

| Method                                                                        | Description                                                                                                                                                                                                                                                                                                        |
| ----------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `processMobileMoney(Booking $booking, string $provider, string $phoneNumber)` | Normalizes and validates the phone number, resolves the selected provider, creates a pending ZMW payment, calls the gateway, and returns a success or failure result. On success, the payment is marked successful, the booking is confirmed through the payment model, and a digital ticket is issued atomically. |
| `resolveGateway(string $provider)`                                            | Protected gateway factory method that resolves the provider through `SimulatedGateway::forProvider()`. It can be overridden by tests to provide a deterministic gateway.                                                                                                                                           |

**Processing Behavior**:

- Supported provider keys are `mtn` and `airtel`.
- Phone numbers are normalized to digits before validation and saved to the booking.
- Payment and ticket creation are wrapped in a database transaction on successful gateway response.
- Ticket creation is guarded against duplicate tickets for the same booking.
- The traveler receives a `TicketIssued` notification after successful processing.
- Unsupported providers, invalid phone numbers, and declined transactions return structured failure results.

**Used By**: `App\Http\Controllers\BookingController` for the web payment flow.

---

### `app/Services/RefundCalculationService.php`

- **Namespace**: `App\Services`
- **Purpose**: Calculates cancellation refunds from the route's operator rules and records cancellation details.
- **Models Used**: `Booking`, `CancellationRule`
- **Date Dependency**: `Carbon\Carbon`

| Method                                  | Description                                                                                                                                                                       |
| --------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `calculateRefund(Booking $booking)`     | Calculates hours until departure, selects the first active matching cancellation rule, and returns the refund amount, percentage, rule metadata, timing, and explanatory message. |
| `processCancellation(Booking $booking)` | Calculates the refund, updates the booking to `cancelled`, stores the applied rule and refund amount, sets `cancelled_at`, and returns the refund details.                        |

**Business Rules**:

- Past departures receive no refund.
- If no active cancellation rule applies, the refund is zero.
- Refund amounts are calculated from the booking amount and the rule percentage, then rounded to two decimal places.
- The service calculates and records the refund; it does not issue an external payment refund.

**Used By**: Traveler booking cancellation and operator booking refund flows.

---

### `app/Services/AdminAuditService.php`

- **Namespace**: `App\Services`
- **Purpose**: Records actions performed by the authenticated admin in `AdminAuditLog`.
- **Model Used**: `AdminAuditLog`
- **Style**: Static service API

| Method                                                                                                                                                    | Description                                                                                                                                                                 |
| --------------------------------------------------------------------------------------------------------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `log(string $event, ?string $description = null, ?Model $auditable = null, ?array $oldValues = null, ?array $newValues = null, ?Request $request = null)` | Gets the authenticated admin from the `admin` guard and creates an audit record. It optionally records the auditable model, old and new values, IP address, and user agent. |

**Behavior**:

- Returns `null` when no admin is authenticated.
- Uses the supplied request or the current request for request metadata.
- Stores the auditable model class and key when a model is provided.

**Used By**: Admin booking, operator, profile, and user controllers.

---

### `app/Services/OperatorAuditService.php`

- **Namespace**: `App\Services`
- **Purpose**: Records operator activity in `OperatorAuditLog`.
- **Models Used**: `Operator`, `OperatorAuditLog`
- **Style**: Static service API

| Method                                                                    | Description                                                                                                             |
| ------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------- |
| `log(...)`                                                                | Creates an operator audit record with actor, event, description, auditable model, old/new values, and request metadata. |
| `viewed(Model $auditable, ?string $description = null)`                   | Records a model-view event using a generated event name and description.                                                |
| `created(Model $auditable, ?string $description = null)`                  | Records a model-created event with the model's current attributes as new values.                                        |
| `updated(Model $auditable, array $original, ?string $description = null)` | Records a model-updated event with the original values and current model changes.                                       |
| `deleted(Model $auditable, ?string $description = null)`                  | Records a model-cancelled event with the model's attributes.                                                            |
| `getOperator()`                                                           | Private helper that resolves the operator from the `operator` guard or an `operator_id` session fallback.               |

**Behavior**:

- If no operator context is available, `log()` returns an empty `OperatorAuditLog` instance rather than creating a record.
- Request metadata comes from the explicit request when supplied, otherwise from the current request.

**Used By**: Operator authentication, bus, booking, customer, fare-rule, promo-code, route-template, and trip-management controllers.

## Mobile Money Services

### `app/Services/MobileMoney/GatewayInterface.php`

- **Type**: Interface
- **Purpose**: Defines the contract for mobile-money payment providers.

| Method                                                          | Description                                                                                                   |
| --------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------- |
| `charge(string $phoneNumber, float $amount, string $reference)` | Charges a phone number and returns success status, transaction reference, message, and gateway response data. |
| `getProviderName()`                                             | Returns the provider's display name.                                                                          |
| `validatePhoneNumber(string $phoneNumber)`                      | Checks whether a phone number matches the provider's prefix pattern.                                          |

---

### `app/Services/MobileMoney/SimulatedGateway.php`

- **Implements**: `GatewayInterface`
- **Purpose**: Simulates Zambian mobile-money charging for development and testing.

**Supported Providers**:

| Provider Key | Display Name | Phone Prefixes | Payment Label  |
| ------------ | ------------ | -------------- | -------------- |
| `mtn`        | MTN MoMo     | `096`, `076`   | `mtn_money`    |
| `airtel`     | Airtel Money | `097`, `077`   | `airtel_money` |

| Method                                     | Description                                                                                                                          |
| ------------------------------------------ | ------------------------------------------------------------------------------------------------------------------------------------ |
| `charge(...)`                              | Simulates one to three seconds of network latency and returns an approximately 90% success rate with transaction or failure details. |
| `getProviderName()`                        | Returns the configured provider display name.                                                                                        |
| `getProviderLabel()`                       | Returns the provider payment label used on payment records.                                                                          |
| `validatePhoneNumber(string $phoneNumber)` | Requires exactly 10 digits and a prefix registered for the selected provider.                                                        |
| `forProvider(string $providerKey)`         | Static factory for `mtn` and `airtel`; throws `InvalidArgumentException` for unsupported providers.                                  |

**Notes**:

- Gateway responses include provider, phone, amount, currency, booking reference, status, timestamp, and simulation metadata.
- The implementation is intentionally deterministic in shape but uses random latency and success/failure outcomes for realistic development behavior.

## Messaging Services

### `app/Services/Messaging/MessagingGatewayInterface.php`

- **Type**: Interface
- **Purpose**: Defines the contract for SMS and WhatsApp message delivery.

| Method                                                       | Description                                                                                                       |
| ------------------------------------------------------------ | ----------------------------------------------------------------------------------------------------------------- |
| `send(string $to, string $message, string $channel = 'sms')` | Sends a message through the selected channel and returns success status, message ID, result message, and channel. |

---

### `app/Services/Messaging/SimulatedMessagingGateway.php`

- **Implements**: `MessagingGatewayInterface`
- **Purpose**: Provides a development-safe messaging implementation that sends no external messages.

| Method      | Description                                                                                                                            |
| ----------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| `send(...)` | Generates a simulated message ID, writes the recipient, channel, body, and ID to the application log, and returns a successful result. |

**Use Case**: Default gateway for local development and tests.

---

### `app/Services/Messaging/AfricasTalkingGateway.php`

- **Implements**: `MessagingGatewayInterface`
- **Purpose**: Sends SMS or WhatsApp messages through Africa's Talking.

| Method                          | Description                                                                                                                                                                                                                     |
| ------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `send(...)`                     | Reads credentials and endpoints from configuration, normalizes the phone number, posts a form request with Laravel's HTTP client, and maps successful, failed, and exceptional responses into the common gateway result format. |
| `normalizePhone(string $phone)` | Protected helper that converts a local Zambian number beginning with `0` to international format beginning with `260`.                                                                                                          |

**Configuration**:

- `MESSAGING_DRIVER=africas_talking` selects this gateway through `AppServiceProvider`.
- `AT_USERNAME`, `AT_API_KEY`, and `AT_SENDER_ID` provide Africa's Talking credentials and sender configuration.
- SMS and WhatsApp endpoints are defined in `config/messaging.php`.
- Missing credentials, unsuccessful HTTP responses, and thrown exceptions return structured failure results and are logged.

## Service Container Integration

`App\Providers\AppServiceProvider` binds `MessagingGatewayInterface` to:

- `AfricasTalkingGateway` when `config('messaging.driver')` is `africas_talking`.
- `SimulatedMessagingGateway` for all other driver values, including the default `simulated` driver.

The bound messaging gateway is injected into the notification channels in `app/Notifications/Channels/SmsChannel.php` and `app/Notifications/Channels/WhatsAppChannel.php`.

## Cross-Cutting Considerations

- Services use Eloquent models and database transactions for domain state changes.
- Payment and messaging gateways expose common interfaces so integrations can be replaced or tested independently.
- Audit services capture actor context and request metadata for traceability.
- External payment refunds are outside the current `RefundCalculationService` responsibility.
