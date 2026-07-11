# Views Summary - BookMyBus Zambia

## Overview

This document summarizes the Blade view templates in the `resources/views` directory of the BookMyBus Zambia application. The views are built using Tailwind CSS and follow a consistent design system with custom color variables.

---

## View Files Summary

### 1. `welcome.blade.php`

**Purpose:** Default Laravel welcome page (starter template)

**Key Features:**

- Standard Laravel welcome page with documentation links
- Uses Tailwind CSS v4.0.7 with inline styles
- Contains navigation for login/register routes
- Displays Laravel logo SVG graphics
- Responsive layout with flex/grid utilities

**Notes:** This is a default Laravel template and not part of the custom application UI.

---

### 2. `landing_search.blade.php`

**Purpose:** Main landing page with trip search functionality

**Key Features:**

- **Navigation Bar:** Fixed top navigation with links to Find Trips, My Bookings, Operator Portal, Support, and Sign In
- **Hero Section:** Full-width hero with gradient overlay background image, headline "Travel Zambia with Confidence"
- **Search Form:**
    - Origin field (default: "Lusaka")
    - Destination field (default: "Kitwe")
    - Date picker with min/max date constraints (today to +30 days)
    - Passenger counter (1-5 passengers) with increment/decrement buttons
    - Submit button to find trips
- **Why Choose Us Section:** Bento grid layout highlighting:
    - Secure Transactions (shield icon)
    - Lightning Fast booking (speed icon)
    - Mobile Money Ready (smartphone icon)
    - 24/7 Premium Support
- **Popular Routes:** Dynamic route cards showing origin → destination with fare and distance
- **Trusted Operators:** Logos of EURO-TRANS, POWER-TOOLS, MAZHANDU, FM-TRAVELLER
- **Footer:** Copyright and policy links

**Data Variables:**

- `$routes` - Collection of route objects
- `$detectedCity` - Optional detected city for personalized route suggestions

---

### 3. `search_results.blade.php`

**Purpose:** Display search results for available bus trips

**Key Features:**

- **Filters Sidebar:**
    - Time of Day filter (Dawn, Morning, Afternoon, Night)
    - Price Range slider (ZMW 150 - 800)
    - Preferred Operator checkboxes (dynamic from trips collection)
    - Map view ad for route tracking
- **Results Header:**
    - Route summary (origin → destination)
    - Passenger count and date
    - Modify Search button
- **Trip Cards:** Each trip displays:
    - Operator name and rating
    - Departure time and origin terminal
    - Visual route indicator with distance
    - Arrival time and destination station
    - Fare and available seats count (with sold-out detection)
    - "View Seats" CTA button (disabled when sold out)
- **Empty State:** Message when no buses found
- **Info Grid:** Verified Operators, Instant Ticket, 24/7 Support

**Data Variables:**

- `$request` - Search request object (origin, destination, passengers, travel_date)
- `$trips` - Collection of available trip objects

---

### 4. `_search_results.blade.php`

**Purpose:** Alternative/duplicate search results view (backup version)

**Notes:** This file may be a legacy backup or development artifact. It was listed in the directory but may not be actively used.

**Key Features:**

- Similar to `search_results.blade.php` but with static operator data
- Uses placeholder images for operator logos
- Static operator names (Power Tools, Euro Africa, Likili Motorways, Mazhandu)
- Same filter sidebar and trip card layout

**Data Variables:**

- `$request` - Search request object
- `$trips` - Collection of available trip objects

---

### 5. `seat_selection.blade.php`

**Purpose:** Interactive seat selection interface for booking

**Key Features:**

- **Seat Legend:** Available, Selected, and Occupied seat indicators
- **Bus Interior Visualization:**
    - Cockpit/driver area indicator
    - 5-column seat grid layout
    - Dynamic seat rendering based on `$route->bus->seat_capacity`
    - Color-coded seats (available, booked, selected)
    - JavaScript seat selection with visual feedback
- **Trip Summary Card:**
    - Route information
    - Bus class badge
    - Departure/arrival times
    - Selected seat display
    - Fare display
- **Booking Form:**
    - Passenger full name
    - NRC/ID Number
    - Phone number
    - Confirm Booking button (disabled until seat selected)
- **Security Assurance:** Encryption notice
- **JavaScript:** Interactive seat selection, form validation, and error handling

**Data Variables:**

- `$route` - Route object with trip details (includes `bus->seat_capacity`)
- `$bookedSeats` - Array of already booked seat numbers
- `$searchBackUrl` - URL to return to search results
- `$errors` - Validation error messages

---

### 6. `payment_ticket.blade.php`

**Purpose:** Secure payment processing page

**Key Features:**

- **Mobile Money Options:**
    - Airtel Money (red theme)
    - MTN MoMo (yellow theme, marked as recommended)
- **Payment Form:**
    - Phone number input (with validation for Zambian numbers)
    - Pay button with total fare
    - Provider selection with dynamic form updates
- **Reservation Countdown Timer:** Shows time remaining for seat hold
- **Expired Booking Banner:** Warning when reservation expires
- **Digital Ticket Display:**
    - Scenic header image
    - Status badge (Pending Payment)
    - Passenger name and seat number
    - Route visualization (origin → destination)
    - Departure date/time
    - Booking ID and class type
    - QR code for boarding
- **Fare Summary:** Base fare, booking fee, VAT breakdown
- **Security Assurance:** Bank-grade security notice
- **Layout:** Uses `@extends('layouts.app')` with `@section('content')`
- **JavaScript:** Provider selection, countdown timer, loading state handling

**Data Variables:**

- `$booking` - Booking object
- `$total_fare` - Total fare amount
- `$passenger_name`, `$seat_number`
- `$origin`, `$destination`, `$origin_code`, `$destination_code`
- `$departure_date`, `$departure_time`, `$class_type`, `$booking_id`
- `$held_until` - Reservation expiry timestamp
- `$expired` - Boolean for expired reservation

---

### 7. `history_page.blade.php`

**Purpose:** Digital ticket view for confirmed bookings

**Key Features:**

- **Payment Success Banner:** Green banner with check icon when booking is confirmed
- **Digital Ticket:**
    - Scenic header image
    - Status badge (Confirmed/Pending)
    - Passenger name and seat number
    - Route information
    - Departure date/time
    - Booking ID and bus class
    - QR code generated from booking reference
- **Fare Summary:** Total fare with "Book Another Trip" button

**Data Variables:**

- `$booking` - Booking object with passenger details, route, and amount

---

### 8. `operator-dashboard.blade.php`

**Purpose:** Enhanced operator dashboard with bento grid layout and real-time monitoring

**Key Features:**

- **Sidebar Navigation:**
    - Dashboard (active)
    - Manage Trips
    - Seat Maps
    - Revenue
    - Profile
    - New Trip button
    - Settings, Support
- **Key Performance Indicators Row:**
    - Total Bookings card with trend indicator
    - Revenue Generated card with daily average
    - Active Fleet card with occupancy progress
    - Average Occupancy highlight card
- **Bento Grid Layout:**
    - **Live Fleet Status:** Real-time bus tracking with status badges and progress bars
    - **Upcoming Trips Table:** Interactive table with route, departure, occupancy, and status columns
    - **Recent Notifications & Alerts:** High-priority alerts with action buttons
- **New Trip Drawer:** Slide-out form panel for creating trips
- **Alert Types:**
    - Maintenance Required (tertiary styling)
    - High Demand Route (secondary styling)
    - Driver Rest Alert (primary styling)

**Data Variables:**

- `$operator` - Operator object with company_name
- `$total_bookings` - Total number of bookings
- `$bookings_trend` - Trend percentage vs last month
- `$revenue` - Total revenue amount
- `$revenue_trend` - Revenue trend percentage
- `$revenue_average` - Daily average revenue
- `$active_trips_count` - Number of active trips
- `$total_trips_today` - Total trips scheduled today
- `$avg_occupancy` - Average occupancy percentage
- `$fleet_status` - Array of bus status objects
- `$upcoming_trips` - Array of upcoming trip objects
- `$alerts` - Array of alert/notification objects

---

### 9. `operator_profile.blade.php`

**Purpose:** Operator account and business profile management with tabbed interface

**Key Features:**

- **Profile Header Card:**
    - Business avatar (placeholder icon)
    - Business name and verification status badge
    - Email address display
- **Tabbed Interface:**
    - Account Details (active) - Edit business information form
    - Change Password - Password update form
- **Account Details Form:**
    - Company Name input
    - Email Address input
    - Phone Number input
    - TPIN input (optional)
    - Business Address input (optional)
    - Save Changes button
- **Change Password Form:**
    - Current Password input
    - New Password input
    - Confirm New Password input
    - Update Password button
- **Status Messages:**
    - Success flash message display
    - Validation error display

**Data Variables:**

- `$operator` - Operator object with company_name, email, phone_number, tpin, address, is_verified
- `$errors` - Validation error messages
- Session status messages

---

### 10. `manage_trips.blade.php`

**Purpose:** Advanced trip management with filtering, search, and drawer-based trip creation

**Key Features:**

- **Sidebar Navigation:**
    - Dashboard
    - Manage Trips (active)
    - Seat Maps
    - Revenue
    - Profile
    - New Trip button
    - Settings, Support
- **Header:**
    - Page title and subtitle
    - Search input with real-time filtering
    - Notification and schedule icons
- **Filter Bar:**
    - Status filter pills (All trips, Scheduled, On route, Delayed, Completed)
    - Date filter dropdown
    - Export and New trip buttons
- **Trips Table:**
    - Trip ID, Route, Date & time, Bus, Occupancy, Status, Actions columns
    - Interactive rows with hover actions
    - Status badges with color coding
    - Action buttons (Edit, View seat map, Cancel)
- **New Trip Drawer:**
    - Slide-out form panel
    - Route origin/destination selectors
    - Date and time pickers
    - Bus assignment with radio selection
    - Trip class and fare inputs
    - Notes field
    - Save/Cancel buttons

**Data Variables:**

- `$operator` - Operator object with company_name
- `$trips` - Array of trip data (id, route_from, route_to, date, departure, bus, booked, capacity, fare, status, status_type)
- `$routes` - Array of route data (from, to)
- `$buses` - Array of bus data (id, plate, model, capacity)

---

### 11. `profile.blade.php`

**Purpose:** Traveler profile and account management page

**Key Features:**

- **Navigation Bar:** Fixed top navigation with links to Find Trips, My Account, Support, and Sign Out
- **Profile Header:**
    - Avatar placeholder with person icon
    - User name and email display
- **Tabbed Interface:**
    - Account Details (active) - Edit personal information form
    - Change Password - Password update form
    - Booking History - List of past bookings
- **Account Details Form:**
    - Full Name input
    - Email Address input
    - Phone Number input
    - Preferred Language selector (English, Nyanja, Bemba)
    - Save Changes button
- **Change Password Form:**
    - Current Password input
    - New Password input
    - Confirm New Password input
    - Update Password button
- **Booking History Tab:**
    - List of bookings with route, reference, date, amount, and status
    - Status badges (confirmed, pending, cancelled)
    - Empty state with "Find a Trip" CTA
- **Status Messages:**
    - Success flash message display
    - Validation error display
- **JavaScript:** Tab switching functionality

**Data Variables:**

- `$user` - User object with full_name, email, phone_number, preferred_language
- `$bookings` - Collection of user's booking history
- `$errors` - Validation error messages
- Session status messages

---

### 12. `support_page.blade.php`

**Purpose:** Customer support page with FAQ and contact options

**Key Features:**

- **Navigation Bar:** Fixed top navigation with links to Search Results, My Bookings, Support
- **FAQ Section:** Expandable FAQ items with questions about booking, payment, cancellation, tickets, missed buses
- **Contact Options:**
    - Call Us (phone numbers)
    - WhatsApp (chat link)
    - Email Us (email address)
- **Contact Form:**
    - Name, Email, Subject, Message fields
    - Send Message button
- **Footer:** Copyright and policy links

**Data Variables:**

- No dynamic data variables (static content)

---

### 13. `auth/login.blade.php`

**Purpose:** Traveler sign-in page

**Key Features:**

- **Split Layout:** Left decorative panel, right form panel
- **Left Panel:**
    - Gradient background (green theme)
    - Feature highlights (Digital tickets, Choose seat, Mobile money)
    - Link to operator portal
- **Right Panel:**
    - Email field with icon
    - Password field with show/hide toggle
    - Remember me checkbox
    - Sign In button
    - Registration link (disabled, "coming soon")
- **Form Validation:** Error display for invalid credentials

**Data Variables:**

- `$errors` - Validation error messages

---

### 14. `auth/operator-login.blade.php`

**Purpose:** Operator portal sign-in page

**Key Features:**

- **Split Layout:** Left decorative panel (orange theme), right form panel
- **Left Panel:**
    - Orange gradient background (distinguishes from traveler login)
    - Operator-specific features (Seat inventory, Revenue tracking, Route publishing)
    - Link to traveler sign-in
- **Right Panel:**
    - Email field with icon
    - Password field with show/hide toggle
    - Remember me checkbox
    - Sign In to Portal button
    - Pending verification notice
- **Form Validation:** Error display for invalid credentials

**Data Variables:**

- `$errors` - Validation error messages
- Session status messages

---

### 15. `auth/register.blade.php`

**Purpose:** User registration page

**Key Features:**

- **Centered Card Layout:** With blur effect and gradient background
- **Form Fields:**
    - Full Name (floating label)
    - Email Address (floating label)
    - Phone Number (floating label)
    - Password (floating label with show/hide toggle)
    - Password Strength Indicator (4-level bar)
    - Terms of Service checkbox
- **Submit Button:** Create Account with icon
- **Login Link:** Link to sign-in page
- **Benefits Grid:** Secure Booking, Mobile Money, 24/7 Support, Digital Tickets

**Data Variables:**

- `$errors` - Validation error messages

---

### 16. `auth/forgot-password.blade.php`

**Purpose:** Password reset request page

**Key Features:**

- **Centered Card Layout:** Clean, minimal design
- **Form Fields:**
    - Email Address input
- **Submit Button:** Email Password Reset Link
- **Back to Login Link**

**Data Variables:**

- None (static form)

---

### 17. `auth/reset-password.blade.php`

**Purpose:** Password reset confirmation page

**Key Features:**

- **Centered Card Layout:** Clean, minimal design
- **Form Fields:**
    - Email Address (pre-filled, hidden)
    - New Password input
    - Confirm Password input
- **Submit Button:** Reset Password

**Data Variables:**

- `$token` - Password reset token
- `$email` - User email address

---

## Design System

### Color Palette

The application uses a custom Material Design-inspired color system:

- **Primary:** `#00601f` (Green) - Main brand color
- **Primary Container:** `#197b30`
- **Secondary Container:** `#ff8921` (Orange) - Accent color
- **Tertiary Container:** `#d1200f` (Red) - Error/warning
- **Surface:** `#f9f9fc` - Background
- **On Surface:** `#1a1c1e` - Text

### Typography

- **Headline Font:** Manrope (for headings and titles)
- **Body Font:** Inter (for body text and labels)

### UI Components

- Rounded corners with `rounded-xl` and `rounded-2xl`
- Card-based layout with shadows
- Gradient buttons for primary actions
- Material Symbols icons throughout

---

## User Flow

```
landing_search → search_results → seat_selection → payment_ticket → history_page
```

**Operator Flow:**

```
operator-dashboard → manage_trips → operator_profile
```

---

## TODO Items

Several views contain TODO comments indicating future work:

1. ~~All views need to extend a `layouts.app` when available~~ - Partially complete: `payment_ticket.blade.php` now uses `@extends('layouts.app')`
2. ~~`seat_selection` needs dynamic seat map rendering from Buses table~~ - **Complete**: Now uses `$route->bus->seat_capacity` for dynamic seat rendering
3. ~~`payment_ticket` needs MTN/Airtel Money integration~~ - **Complete**: Full provider selection UI with phone validation implemented
4. ~~`operator-dashboard` and `operator_profile` need dynamic data from database~~ - **Complete**: Both views now receive dynamic data from controllers
5. ~~`manage_trips` needs proper form submission handling and validation~~ - **Complete**: Form submits to `operator.trips.store` route
6. `auth/forgot-password` and `auth/reset-password` need email functionality - Still pending
7. `_search_results.blade.php` - Consider removing or documenting as legacy backup file (file may not exist)
