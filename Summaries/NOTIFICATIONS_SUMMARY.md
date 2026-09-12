# Notifications Directory Summary

This document provides a comprehensive summary of the notifications and custom notification channels in `app/Notifications`.

## Overview

The notification layer uses Laravel notifications to deliver booking and trip updates through email, SMS, and WhatsApp. All four notification classes implement `ShouldQueue`, so delivery is queued, and the SMS and WhatsApp channels record delivery outcomes in `NotificationLog`.

### Notification Types

- **`TicketIssued`**: Confirms a successful booking and provides ticket and QR-code details.
- **`BookingCancelled`**: Notifies a traveler that a booking was cancelled and includes refund context where applicable.
- **`DepartureReminder`**: Reminds a traveler about an upcoming departure, seat, and booking reference.
- **`TripStatusChanged`**: Notifies confirmed passengers when a trip is delayed, departed, arrived, or cancelled.

### Delivery Channels

| Channel  | Implementation                               | Delivery Behavior                                                                                                        |
| -------- | -------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------ |
| Mail     | Laravel `mail` channel                       | Builds a `MailMessage` with subject, greeting, trip details, and optional action link.                                   |
| SMS      | `App\Notifications\Channels\SmsChannel`      | Builds notification text with `toSms()`, sends it through the configured messaging gateway, and records the result.      |
| WhatsApp | `App\Notifications\Channels\WhatsAppChannel` | Builds notification text with `toWhatsApp()`, sends it through the configured messaging gateway, and records the result. |

## Notification Classes

### `app/Notifications/TicketIssued.php`

- **Namespace**: `App\Notifications`
- **Implements**: `ShouldQueue`
- **Constructor Data**: `Booking $booking`, `Ticket $ticket`
- **Purpose**: Notifies a traveler after payment succeeds and a digital ticket is issued.

| Method                    | Description                                                                                                                                     |
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------- |
| `via($notifiable)`        | Returns `mail`, `SmsChannel`, and `WhatsAppChannel`.                                                                                            |
| `toMail($notifiable)`     | Sends confirmation email with route, date, departure time, seat, booking reference, QR code, and a `View Ticket Online` link to `tickets.show`. |
| `toSms($notifiable)`      | Sends a compact confirmation containing route, date, time, seat, booking reference, QR code, and boarding instructions.                         |
| `toWhatsApp($notifiable)` | Sends formatted ticket details with route, schedule, seat, reference, QR code, and boarding instructions.                                       |

**Triggered By**:

- `App\Services\PaymentService` after successful web mobile-money payment processing.
- `App\Http\Controllers\Api\PaymentController` after successful API payment callback processing.

---

### `app/Notifications/BookingCancelled.php`

- **Namespace**: `App\Notifications`
- **Implements**: `ShouldQueue`
- **Constructor Data**: `Booking $booking`
- **Purpose**: Notifies a traveler when their booking is cancelled.

| Method                    | Description                                                                                                           |
| ------------------------- | --------------------------------------------------------------------------------------------------------------------- |
| `via($notifiable)`        | Returns `mail`, `SmsChannel`, and `WhatsAppChannel`.                                                                  |
| `toMail($notifiable)`     | Sends cancellation email with origin, destination, booking reference, and refund wording when a refund amount exists. |
| `toSms($notifiable)`      | Sends a concise cancellation message with booking reference and route.                                                |
| `toWhatsApp($notifiable)` | Sends a cancellation message and directs the traveler to contact support if the cancellation was unexpected.          |

**Triggered By**: `App\Http\Controllers\BookingController` during traveler self-cancellation, including both pending-booking release and confirmed-booking cancellation paths.

---

### `app/Notifications/DepartureReminder.php`

- **Namespace**: `App\Notifications`
- **Implements**: `ShouldQueue`
- **Constructor Data**: `Booking $booking`
- **Purpose**: Reminds confirmed passengers that their trip is departing soon.

| Method                    | Description                                                                                                                |
| ------------------------- | -------------------------------------------------------------------------------------------------------------------------- |
| `via($notifiable)`        | Returns `mail`, `SmsChannel`, and `WhatsAppChannel`.                                                                       |
| `toMail($notifiable)`     | Sends route, departure date and time, seat, booking reference, and an instruction to arrive at the boarding point on time. |
| `toSms($notifiable)`      | Sends a compact departure reminder with route, date, time, seat, and booking reference.                                    |
| `toWhatsApp($notifiable)` | Sends formatted departure details with route, schedule, seat, and booking reference.                                       |

**Triggered By**: `App\Console\Commands\SendDepartureReminders`, which notifies eligible confirmed bookings through the scheduled `notifications:departure-reminders` command.

---

### `app/Notifications/TripStatusChanged.php`

- **Namespace**: `App\Notifications`
- **Implements**: `ShouldQueue`
- **Constructor Data**: `Route $route`, `string $status`
- **Purpose**: Notifies confirmed passengers when the operational status of their trip changes.

| Method                    | Description                                                                                                                                                |
| ------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `via($notifiable)`        | Returns `mail`, `SmsChannel`, and `WhatsAppChannel`.                                                                                                       |
| `toMail($notifiable)`     | Sends the status description, route, and scheduled departure details.                                                                                      |
| `toSms($notifiable)`      | Sends a compact route and schedule update followed by the status description.                                                                              |
| `toWhatsApp($notifiable)` | Sends formatted route and schedule details followed by the status description.                                                                             |
| `describe()`              | Protected helper that maps `delayed`, `departed`, `arrived`, and `cancelled` statuses to traveler-facing text; unknown statuses receive a generic message. |

**Supported Status Messages**:

| Status       | Message Behavior                                                                              |
| ------------ | --------------------------------------------------------------------------------------------- |
| `delayed`    | Includes delay minutes and the delay reason when available.                                   |
| `departed`   | States that the trip has departed from the origin.                                            |
| `arrived`    | States that the trip has arrived at the destination.                                          |
| `cancelled`  | States that the trip was cancelled and that a refund, if applicable, follows operator policy. |
| Other values | Uses a generic current-status message.                                                        |

**Triggered By**: `App\Http\Controllers\Operator\TripManagementController` when an operator marks a trip delayed, departed, arrived, or cancelled with notification. Notifications are sent to the route's confirmed passengers.

## Custom Notification Channels

### `app/Notifications/Channels/SmsChannel.php`

- **Purpose**: Adapts Laravel notifications to the application's SMS messaging gateway.
- **Dependency**: `App\Services\Messaging\MessagingGatewayInterface`
- **Log Model**: `App\Models\NotificationLog`

| Method                                          | Description                                                                                                                                                                                                                                     |
| ----------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `send($notifiable, Notification $notification)` | Skips recipients without a phone number, renders `toSms()`, sends through the messaging gateway with channel `sms`, and creates a notification log containing recipient, notification type, message, status, gateway message ID, and timestamp. |

**Behavior**:

- The notification is not sent when `phone_number` is empty.
- Gateway success maps to `sent`; gateway failure maps to `failed`.
- The first 120 characters of the message are stored as the log subject.

---

### `app/Notifications/Channels/WhatsAppChannel.php`

- **Purpose**: Adapts Laravel notifications to the application's WhatsApp messaging gateway.
- **Dependency**: `App\Services\Messaging\MessagingGatewayInterface`
- **Log Model**: `App\Models\NotificationLog`

| Method                                          | Description                                                                                                                                                                                                                                               |
| ----------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `send($notifiable, Notification $notification)` | Skips recipients without a phone number, renders `toWhatsApp()`, sends through the messaging gateway with channel `whatsapp`, and creates a notification log containing recipient, notification type, message, status, gateway message ID, and timestamp. |

**Behavior**:

- The notification is not sent when `phone_number` is empty.
- Gateway success maps to `sent`; gateway failure maps to `failed`.
- The first 120 characters of the message are stored as the log subject.

## Notification Logging

`App\Models\NotificationLog` stores the delivery audit record for SMS and WhatsApp notifications.

| Attribute                           | Description                                                       |
| ----------------------------------- | ----------------------------------------------------------------- |
| `notifiable_type` / `notifiable_id` | Polymorphic recipient identity.                                   |
| `channel`                           | `sms` or `whatsapp`.                                              |
| `notification_type`                 | Fully qualified notification class name.                          |
| `subject`                           | First 120 characters of the rendered message.                     |
| `body`                              | Full rendered message.                                            |
| `status`                            | `sent` or `failed`, based on the gateway response.                |
| `gateway_message_id`                | Provider or simulated gateway message identifier, when available. |
| `sent_at`                           | Delivery attempt timestamp.                                       |

## Queue and Gateway Integration

- Each notification uses Laravel's `Queueable` trait and implements `ShouldQueue`.
- Mail delivery uses Laravel's standard notification mail channel.
- SMS and WhatsApp delivery depend on the service-container binding for `MessagingGatewayInterface`.
- The default messaging gateway is simulated; `MESSAGING_DRIVER=africas_talking` selects the Africa's Talking implementation.
- Gateway configuration and implementation details are documented in `Summaries/SERVICES_SUMMARY.md`.

## Operational Flow

```
Payment succeeds
  → TicketIssued
    → mail + SMS + WhatsApp

Traveler cancels booking
  → BookingCancelled
    → mail + SMS + WhatsApp

Scheduled departure-reminder command
  → DepartureReminder
    → mail + SMS + WhatsApp

Operator changes trip status
  → TripStatusChanged
    → confirmed passengers
      → mail + SMS + WhatsApp
```
