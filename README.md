# BookMyBus Zambia 🚌

A **Laravel**-based web application for booking bus tickets across Zambia. Passengers can search routes, select seats, and pay via mobile money (MTN MoMo / Airtel Money). Bus operators manage trips, view bookings, and track occupancy through a dedicated operator portal.

---

## ✨ Features

### For Travelers
- **Search Routes** – Find trips by origin, destination, and travel date
- **Interactive Seat Selection** – View available seats and pick your preferred one
- **Mobile Money Payments** – Pay securely via MTN MoMo or Airtel Money (simulated in development)
- **Booking History** – Look up your bookings using a reference ID or personal details
- **Profile Management** – Update personal information and change password

### For Operators
- **Dashboard** – Overview of trips, bookings, and revenue
- **Trip Management** – Create, update, cancel, and manage trips
- **Booking Management** – View all bookings, filter by trip, export data
- **Seat Map & Occupancy** – Visual seat grid and occupancy rates per trip
- **Operator Profile** – Manage company details and password

### General
- **Multi-guard Authentication** – Separate login systems for travelers and operators
- **Soft Deletes** – Data is preserved for auditing
- **Responsive UI** – Works on desktop and mobile

---

## 🧱 Tech Stack

| Layer        | Technology                        |
|-------------|-----------------------------------|
| Backend     | Laravel 11 (PHP 8.x)              |
| Frontend    | Blade templates, JavaScript, CSS  |
| Database    | MySQL / MariaDB                   |
| Cache       | File-based (configurable to Redis)|
| Auth        | Laravel Sanctum (traveler), Session-based (operator) |
| Payments    | Simulated MTN MoMo & Airtel Money |
| Versioning  | Git + GitHub                      |

---

## 📁 Project Structure

```
BookMyBus_Zambia/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                    # Traveler authentication
│   │   │   │   └── Operator/            # Operator authentication
│   │   │   ├── Operator/                # Operator portal controllers
│   │   │   │   ├── DashboardController
│   │   │   │   ├── TripManagementController
│   │   │   │   ├── BookingManagementController
│   │   │   │   └── ProfileController
│   │   │   ├── BookingController        # Traveler booking flow
│   │   │   ├── LandingController        # Public pages
│   │   │   └── ProfileController        # Traveler profile
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php                     # Traveler model
│   │   ├── Operator.php                 # Bus operator model
│   │   ├── Bus.php                      # Bus fleet
│   │   ├── Route.php                    # Trip routes
│   │   ├── Booking.php                  # Booking records
│   │   ├── Payment.php                  # Payment transactions
│   │   └── Ticket.php                   # Digital tickets
│   └── Services/
│       ├── PaymentService.php           # Payment orchestration
│       └── MobileMoney/
│           ├── GatewayInterface.php     # Payment gateway contract
│           └── SimulatedGateway.php     # Simulated MTN/Airtel gateway
├── config/              # Laravel configuration
├── database/
│   └── migrations/      # Database schema migrations
├── resources/
│   └── views/           # Blade templates
│       ├── layouts/     # Layout components
│       ├── operator/    # Operator portal views
│       └── ...          # Traveler-facing views
├── routes/
│   └── web.php          # All web route definitions
├── BookMyBus_Zambia_UI/ # UI mockups (separate from application)
└── documentation/       # Project documentation
```

---

## 🚀 Local Development Setup

### Prerequisites

- PHP 8.1+
- Composer
- MySQL / MariaDB
- Node.js & npm (for frontend assets)
- XAMPP / WAMP / Laragon (Windows) or Valet (macOS)

### Installation

```bash
# 1. Clone the repository
git clone https://github.com/Favour-04/BookMyBus_Zambia.git
cd BookMyBus_Zambia

# 2. Install PHP dependencies
composer install

# 3. Install frontend dependencies
npm install

# 4. Environment configuration
cp .env.example .env
php artisan key:generate

# 5. Configure database in .env
#    DB_DATABASE=bookmybus
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Run migrations
php artisan migrate

# 7. (Optional) Seed test data
php artisan db:seed

# 8. Build frontend assets
npm run build

# 9. Start the development server
php artisan serve
```

The application will be available at `http://localhost:8000`.

---

## 🔐 Authentication Guards

The system uses Laravel's **multi-guard authentication**:

| Guard       | Model      | Login Route              | Used For            |
|-------------|------------|---------------------------|----------------------|
| `web`       | `User`     | `/login`                  | Traveler web access  |
| `operator`  | `Operator` | `/operator/login`         | Operator portal      |
| `admin`     | `User`     | (not yet implemented)     | Admin panel          |

### Test Credentials

After seeding, you can log in with:
- **Traveler:** `traveler@example.com` / `password`
- **Operator:** `operator@example.com` / `password`

---

## 🧭 Route Map

### Public Routes
| Method | URI              | Controller Method              | Description                   |
|--------|------------------|--------------------------------|-------------------------------|
| GET    | `/`              | `LandingController@index`      | Homepage / search form        |
| GET    | `/search`        | `LandingController@search`     | Search trip results           |

### Traveler Authentication (Guest only)
| Method | URI                | Controller Method                         |
|--------|--------------------|-------------------------------------------|
| GET    | `/login`           | `LoginController@showLoginForm`           |
| POST   | `/login`           | `LoginController@login`                   |

### Operator Authentication (Operator Guest only)
| Method | URI                  | Controller Method                                 |
|--------|----------------------|---------------------------------------------------|
| GET    | `/operator/login`    | `Operator\LoginController@showLoginForm`          |
| POST   | `/operator/login`    | `Operator\LoginController@login`                  |

### Authenticated Traveler Routes
| Method | URI                          | Controller Method                       |
|--------|------------------------------|-----------------------------------------|
| POST   | `/logout`                    | `LoginController@logout`               |
| GET    | `/booking/{route}/seats`     | `BookingController@showSeats`          |
| POST   | `/booking/store`             | `BookingController@store`              |
| GET    | `/payment/ticket/{booking}`  | `BookingController@paymentTicket`      |
| POST   | `/payment/process/{booking}` | `BookingController@processPayment`     |
| GET    | `/booking/success/{booking}` | `BookingController@success`            |
| GET    | `/my-booking`                | `BookingController@customerLookupView` |
| POST   | `/my-booking/lookup`         | `BookingController@customerLookup`     |
| GET    | `/profile`                   | `ProfileController@index`              |
| PUT    | `/profile`                   | `ProfileController@update`             |
| PUT    | `/profile/password`          | `ProfileController@updatePassword`     |

### Operator Portal Routes (prefix: `/operator`, name: `operator.`)
| Method   | URI                            | Controller Method                                      |
|----------|-------------------------------|--------------------------------------------------------|
| GET      | `/operator/`                  | `DashboardController@index`                            |
| GET      | `/operator/trips`             | `TripManagementController@index`                       |
| GET      | `/operator/trips/export`      | `TripManagementController@export`                      |
| GET      | `/operator/trips/stats`       | `TripManagementController@stats`                       |
| GET      | `/operator/trips/upcoming`    | `TripManagementController@upcoming`                    |
| POST     | `/operator/trips`             | `TripManagementController@store`                       |
| GET      | `/operator/trips/{trip}`      | `TripManagementController@show`                        |
| PUT      | `/operator/trips/{trip}`      | `TripManagementController@update`                      |
| DELETE   | `/operator/trips/{trip}`      | `TripManagementController@cancel`                      |
| PATCH    | `/operator/trips/{trip}/status` | `TripManagementController@updateStatus`              |
| GET      | `/operator/trips/{trip}/seat-map` | `TripManagementController@seatMap`                  |
| GET      | `/operator/trips/{trip}/occupancy` | `TripManagementController@occupancy`              |
| GET      | `/operator/bookings`          | `BookingManagementController@index`                    |
| GET      | `/operator/bookings/export`   | `BookingManagementController@export`                   |
| GET      | `/operator/bookings/{booking}` | `BookingManagementController@show`                   |
| GET      | `/operator/trips/{trip}/bookings` | `BookingManagementController@tripBookings`          |
| GET      | `/operator/profile`           | `Operator\ProfileController@index`                     |
| PUT      | `/operator/profile`           | `Operator\ProfileController@update`                    |
| PUT      | `/operator/profile/password`  | `Operator\ProfileController@updatePassword`            |
| POST     | `/operator/logout`            | `Operator\LoginController@logout`                      |

---

## 💳 Payment Flow

1. Traveler selects seats and proceeds to payment
2. System creates a **pending booking** (held for 10 minutes)
3. Traveler enters phone number and selects provider (MTN or Airtel)
4. `PaymentService` validates the phone number format
5. `SimulatedGateway` processes the charge (~90% simulated success rate)
6. On success: payment is saved, booking is confirmed, ticket is issued
7. On failure: payment is marked as failed, booking remains pending

---

## 🗄️ Database Schema

| Table      | Key Fields                                                                 |
|------------|---------------------------------------------------------------------------|
| `users`    | `full_name`, `email`, `phone_number`, `password`, `role`                  |
| `operators`| `company_name`, `email`, `phone_number`, `password`, `tpin`, `is_verified`|
| `buses`    | `operator_id`, `registration_number`, `model`, `seat_capacity`, `bus_class`|
| `routes`   | `operator_id`, `bus_id`, `origin`, `destination`, `departure_time`, `fare`|
| `bookings` | `user_id`, `route_id`, `seat_number`, `reference_id`, `amount`, `status`  |
| `payments` | `booking_id`, `amount`, `currency`, `payment_method`, `status`, `transaction_reference`|
| `tickets`  | `booking_id`, `user_id`, `qr_code`, `status`, `issued_at`, `used_at`      |

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=BookingTest
```

---

## 🌿 Git Branches

- `main` – Production-ready code
- `develop` – Integration branch for features
- `feature/*` – Individual feature branches
- `fix/*` – Bug fix branches

---

## 📄 License

This project is open-sourced under the MIT license.