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
    - Preferred Operator checkboxes (Power Tools, Euro Africa, Likili Motorways, Mazhandu)
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
    - Fare and available seats count
    - "View Seats" CTA button
- **Empty State:** Message when no buses found
- **Info Grid:** Verified Operators, Instant Ticket, 24/7 Support

**Data Variables:**

- `$request` - Search request object (origin, destination, passengers, travel_date)
- `$trips` - Collection of available trip objects

---

### 4. `seat_selection.blade.php`

**Purpose:** Interactive seat selection interface for booking

**Key Features:**

- **Seat Legend:** Available, Selected, and Occupied seat indicators
- **Bus Interior Visualization:**
    - Cockpit/driver area indicator
    - 5-column seat grid layout
    - Dynamic seat rendering based on bus capacity
    - Color-coded seats (available, booked, selected)
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

**Data Variables:**

- `$route` - Route object with trip details
- `$bookedSeats` - Array of already booked seat numbers

---

### 5. `payment_ticket.blade.php`

**Purpose:** Secure payment processing page

**Key Features:**

- **Mobile Money Options:**
    - Airtel Money (red theme)
    - MTN MoMo (yellow theme, marked as recommended)
- **Payment Form:**
    - Phone number input for MTN
    - Pay button with total fare
- **Digital Ticket Display:**
    - Scenic header image
    - Status badge (Pending Payment)
    - Passenger name and seat number
    - Route visualization (origin → destination)
    - Departure date/time
    - Booking ID and class type
    - QR code for boarding
- **Security Assurance:** Bank-grade security notice

**Data Variables:**

- `$booking` - Booking object
- `$passenger_name`, `$seat_number`, `$total_fare`
- `$origin`, `$destination`, `$origin_code`, `$destination_code`
- `$departure_date`, `$departure_time`, `$class_type`, `$booking_id`

---

### 6. `history_page.blade.php`

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

### 7. `operator_dashboard.blade.php`

**Purpose:** Operator control panel for managing bus operations

**Key Features:**

- **Sidebar Navigation:**
    - Dashboard (active)
    - Manage Trips
    - Seat Maps
    - Revenue
    - Fleet Management
    - Add New Trip button
    - Settings, Logout
- **Metrics Bento Grid:**
    - Total Bookings (1,284)
    - Revenue (ZMW 42,500)
    - Active Trips (24)
    - Average Occupancy (82%)
- **Upcoming Trips Table:**
    - Trip ID, Route, Departure, Load, Actions
    - Interactive table with hover effects
- **Operational Alerts:**
    - Bus Maintenance Due notifications
    - High Demand Route suggestions
- **Fleet Location Map:** Live map with vehicle markers

**Data Variables:**

- `$trips` - Collection of trip objects

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
    - Average Occupancy highlight card (82%)
- **Bento Grid Layout:**
    - **Live Fleet Status:** Real-time bus tracking with status badges and progress bars
    - **Upcoming Trips Table:** Interactive table with route, departure, occupancy, and status columns
    - **Recent Notifications & Alerts:** High-priority alerts with action buttons
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

**Purpose:** Operator account and business profile management

**Key Features:**

- **Profile Header:**
    - Business logo/avatar
    - Business name and rating
    - Verified Operator badge
    - Edit Profile and View Public Profile buttons
- **Tabbed Interface:**
    - Account Details (active)
    - Fleet Information
    - Payment Methods
    - Billing History
- **Business Information:**
    - Business Name, Registration Number, Date Registered, Business Type, Address
- **Contact Information:**
    - Primary Contact Name, Title, Email, Phone Number
- **Operational Statistics:**
    - Total Trips Operated (1,847)
    - Active Fleet (35 buses)
    - Average Occupancy (82%)
    - On-Time Rate (94%)
- **Account Verification Status:**
    - Business License Verified
    - Tax ID Verified
    - Insurance Certificate (valid/expiry)
- **Danger Zone:** Suspend Account, Delete Account buttons

**Data Variables:**

- No dynamic data variables (static content)

---

### 10. `trip_management.blade.php`

**Purpose:** Comprehensive trip management interface for operators

**Key Features:**

- **Header:**
    - Page title and subtitle
    - Search input for routes/buses
    - Live Operations indicator
- **Quick Stats Bento Grid:**
    - Total Departures Today (24)
    - Average Occupancy (82%)
    - Pending Tasks (07)
    - Fleet Status (32/35 ready)
- **Active Trips Console:**
    - Trip Info, Departure, Occupancy, Fare, Actions columns
    - Manage Seats button per trip
- **Interactive Seat Map:**
    - Visual seat grid (4 columns)
    - Color-coded seats (Available, Booked, Blocked)
    - Selected seat details and status
    - Block Seat and View Passenger buttons
- **Create New Trip Form:**
    - Route Origin & Destination dropdowns
    - Departure Schedule (date/time)
    - Fleet Details dropdown
    - Pricing Strategy input
    - Save as Draft and Publish Trip Live buttons

**Data Variables:**

- `$trips` - Collection of trip objects
- `$seats` - Collection of seat objects
- `$routes` - Collection of route objects
- `$buses` - Collection of bus objects
- `$selected_seat`, `$seat_status`

---

### 11. `manage_trips.blade.php`

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
operator_dashboard → trip_management → operator_profile
```

**Alternative Operator Flow:**

```
operator-dashboard → manage_trips
```

---

## TODO Items

Several views contain TODO comments indicating future work:

1. All views need to extend a `layouts.app` when available
2. `seat_selection` needs dynamic seat map rendering from Buses table
3. `payment_ticket` needs MTN/Airtel Money integration
4. `operator_dashboard` and `operator_profile` need dynamic data from database
5. `trip_management` needs dynamic data for trips, seats, routes, and buses
6. `manage_trips` needs proper form submission handling and validation
