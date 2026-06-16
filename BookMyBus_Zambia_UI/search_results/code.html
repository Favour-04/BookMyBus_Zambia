<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
  <title>Search Results | BookMyBus Zambia</title>

  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Inter:wght@400;500;600;700&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: #f9f9fc;
      color: #1a1c1e;
    }

    h1, h2, h3 {
      font-family: 'Manrope', sans-serif;
    }

    .material-symbols-outlined {
      font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }

    /* Toast Notification */
    .toast-notify {
      position: fixed;
      bottom: 30px;
      left: 50%;
      transform: translateX(-50%) translateY(20px);
      background: #1f2937;
      color: white;
      padding: 10px 20px;
      border-radius: 40px;
      font-size: 0.85rem;
      font-weight: 600;
      z-index: 1000;
      opacity: 0;
      transition: opacity 0.2s ease;
      pointer-events: none;
      white-space: nowrap;
      box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
    }

    .toast-notify.show {
      opacity: 1;
      transform: translateX(-50%) translateY(0);
    }

    /* Filter Active States */
    .filter-time-btn.active {
      background-color: #197b30 !important;
      color: white !important;
    }

    .filter-time-btn.active .material-symbols-outlined {
      color: white !important;
    }

    .operator-checkbox {
      transition: all 0.2s;
    }

    /* Navigation - NO LOGIN, NO OPERATOR PORTAL */
    .nav {
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 50;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(12px);
      box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.5rem;
      max-width: 80rem;
      margin: 0 auto;
    }

    .nav-logo {
      font-size: 1.25rem;
      font-weight: 800;
      color: #00601f;
      text-decoration: none;
    }

    .nav-links {
      display: none;
      gap: 2rem;
    }

    @media (min-width: 768px) {
      .nav-links {
        display: flex;
      }
    }

    .nav-link {
      font-size: 0.875rem;
      font-weight: 700;
      color: #71717a;
      text-decoration: none;
    }

    .nav-link:hover, .nav-link.active {
      color: #00601f;
    }

    .nav-link.active {
      border-bottom: 2px solid #ea580c;
      padding-bottom: 0.25rem;
    }

    /* Main Content */
    main {
      padding-top: 6rem;
      padding-bottom: 5rem;
      padding-left: 1rem;
      padding-right: 1rem;
      max-width: 80rem;
      margin: 0 auto;
    }

    @media (min-width: 768px) {
      main {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
      }
    }

    /* Search Header */
    .search-header {
      margin-bottom: 2.5rem;
    }

    .breadcrumb {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-weight: 700;
      color: #954a00;
      margin-bottom: 0.5rem;
    }

    .header-title {
      font-size: 2.25rem;
      font-weight: 900;
      letter-spacing: -0.02em;
    }

    @media (min-width: 768px) {
      .header-title {
        font-size: 3rem;
      }
    }

    .header-date {
      color: #71717a;
      font-weight: 500;
      margin-top: 0.25rem;
    }

    /* Grid */
    .main-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 2rem;
    }

    @media (min-width: 1024px) {
      .main-grid {
        grid-template-columns: repeat(12, 1fr);
      }
    }

    /* Sidebar */
    .sidebar {
      display: none;
    }

    @media (min-width: 1024px) {
      .sidebar {
        display: block;
        grid-column: span 3;
      }
    }

    .filters-card {
      background: #f3f3f6;
      padding: 1.5rem;
      border-radius: 1rem;
    }

    .filter-section {
      margin-bottom: 2rem;
    }

    .filter-label {
      font-size: 0.625rem;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      font-weight: 700;
      color: #a1a1aa;
      margin-bottom: 1rem;
      display: block;
    }

    .filter-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 0.5rem;
    }

    .filter-time-btn {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 0.75rem;
      border-radius: 0.5rem;
      background: white;
      border: none;
      cursor: pointer;
      width: 100%;
    }

    .price-slider {
      width: 100%;
      height: 4px;
      accent-color: #00601f;
    }

    .operator-list {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
    }

    .operator-item {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      cursor: pointer;
    }

    .checkbox {
      width: 1.25rem;
      height: 1.25rem;
      border-radius: 0.25rem;
      border: 2px solid #00601f;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .checkbox.checked {
      background: #00601f;
    }

    .checkbox.empty {
      background: transparent;
      border-color: #d4d4d8;
    }

    /* Results Section */
    .results-section {
      grid-column: span 9;
    }

    .result-card {
      background: white;
      border-radius: 1rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
      position: relative;
      overflow: hidden;
    }

    .premium-badge {
      position: absolute;
      top: 0;
      right: 0;
      background: #954a00;
      padding: 0.25rem 1rem;
      border-bottom-left-radius: 0.75rem;
      font-size: 0.625rem;
      font-weight: 700;
      color: white;
    }

    .card-content {
      padding: 1.5rem;
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      align-items: center;
    }

    @media (min-width: 768px) {
      .card-content {
        padding: 2rem;
      }
    }

    .operator-info {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
    }

    @media (min-width: 768px) {
      .operator-info {
        align-items: flex-start;
        width: 8rem;
        flex-shrink: 0;
      }
    }

    .operator-logo {
      width: 3.5rem;
      height: 3.5rem;
      background: #e8e8ea;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .operator-name {
      font-weight: 800;
      font-size: 0.875rem;
      text-align: center;
    }

    .trip-details {
      flex-grow: 1;
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
      align-items: center;
      width: 100%;
    }

    @media (min-width: 768px) {
      .trip-details {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    .time-point {
      text-align: center;
    }

    .time-label {
      font-size: 0.625rem;
      text-transform: uppercase;
      font-weight: 700;
      color: #a1a1aa;
    }

    .time-value {
      font-size: 1.875rem;
      font-weight: 900;
    }

    .route-visual {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .duration {
      font-size: 0.625rem;
      font-weight: 700;
      color: #00601f;
    }

    .route-line {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      width: 100%;
    }

    .dot-start {
      width: 0.5rem;
      height: 0.5rem;
      border-radius: 50%;
      border: 2px solid #00601f;
    }

    .line {
      flex-grow: 1;
      height: 2px;
      border-top: 2px dashed #e4e4e7;
      position: relative;
    }

    .bus-icon {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
    }

    .dot-end {
      width: 0.5rem;
      height: 0.5rem;
      border-radius: 50%;
      background: #00601f;
    }

    .price-section {
      width: 100%;
      background: #f3f3f6;
      padding: 1rem;
      border-radius: 0.75rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    @media (min-width: 768px) {
      .price-section {
        width: 12rem;
        flex-direction: column;
        align-items: flex-end;
        background: transparent;
        padding: 0;
      }
    }

    .price-amount {
      font-size: 1.875rem;
      font-weight: 900;
    }

    .seats-left {
      font-size: 0.625rem;
      font-weight: 700;
      color: #00601f;
    }

    .seats-left.low {
      color: #dc3545;
    }

    .select-btn {
      background: #00601f;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.5rem;
      font-weight: 700;
      border: none;
      cursor: pointer;
    }

    /* Info Grid */
    .info-grid {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem;
      margin-top: 2rem;
    }

    @media (min-width: 768px) {
      .info-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    .info-card {
      padding: 1.5rem;
      border-radius: 1rem;
      cursor: pointer;
    }

    .info-card.primary {
      background: rgba(0,96,31,0.05);
      border: 1px solid rgba(0,96,31,0.1);
    }

    .info-card.secondary {
      background: rgba(149,74,0,0.05);
      border: 1px solid rgba(149,74,0,0.1);
    }

    .info-card.neutral {
      background: #f4f4f5;
    }

    /* Footer */
    .footer {
      background: #fafafa;
      border-top: 1px solid #e4e4e7;
      padding: 2rem 1.5rem;
      margin-top: 3rem;
    }

    .footer-content {
      max-width: 80rem;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 2rem;
    }

    .footer-links {
      display: flex;
      flex-wrap: wrap;
      gap: 1.5rem;
    }

    .footer-links a {
      font-size: 0.75rem;
      color: #71717a;
      text-decoration: underline;
    }

    /* Mobile Bottom Nav */
    .mobile-bottom-nav {
      display: none;
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background: white;
      border-top: 1px solid #e4e4e7;
      padding: 0.75rem 1.5rem;
      justify-content: space-between;
      z-index: 40;
    }

    @media (max-width: 768px) {
      .mobile-bottom-nav {
        display: flex;
      }
      main {
        padding-bottom: 5rem;
      }
    }

    .mobile-nav-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.25rem;
      cursor: pointer;
    }

    .mobile-nav-item span:first-child {
      font-size: 1.25rem;
    }

    .mobile-nav-item span:last-child {
      font-size: 0.625rem;
      font-weight: 700;
    }

    /* Loading Spinner */
    .loading-spinner {
      display: inline-block;
      width: 40px;
      height: 40px;
      border: 3px solid #e4e4e7;
      border-top-color: #00601f;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .flex-row {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .items-center {
      align-items: center;
    }

    .justify-between {
      justify-content: space-between;
    }

    .text-right {
      text-align: right;
    }

    .text-center {
      text-align: center;
    }

    .mt-4 {
      margin-top: 1rem;
    }

    .mb-4 {
      margin-bottom: 1rem;
    }

    .cursor-pointer {
      cursor: pointer;
    }
  </style>
</head>
<body>

<!-- Navigation - NO LOGIN, NO OPERATOR PORTAL -->
<nav class="nav">
  <div class="nav-container">
    <a href="index.html" class="nav-logo">🚌 BookMyBus Zambia</a>
    <div class="nav-links">
      <a href="search-results.html" class="nav-link active">Search Results</a>
      <a href="my-bookings.html" class="nav-link">My Bookings</a>
      <a href="support.html" class="nav-link">Support</a>
    </div>
    <!-- NO LOGIN BUTTONS, NO OPERATOR PORTAL LINK -->
  </div>
</nav>

<main>
  <!-- Search Header -->
  <header class="search-header">
    <div class="flex-row justify-between items-center" style="flex-wrap: wrap; gap: 1.5rem;">
      <div>
        <div class="breadcrumb">
          <span>One Way</span>
          <span class="material-symbols-outlined" style="font-size: 0.25rem;">circle</span>
          <span id="passengerCount">1 Passenger</span>
        </div>
        <h1 class="header-title">
          <span id="originCity">Lusaka</span>
          <span>→</span>
          <span id="destinationCity">Kitwe</span>
        </h1>
        <p class="header-date" id="searchDate">Friday, 24 May 2024 • 12 departures available</p>
      </div>
      <button class="filter-time-btn" style="flex-direction: row; gap: 0.5rem; background: #f3f3f6; width: auto;" onclick="goBack()">
        <span class="material-symbols-outlined">edit</span>
        <span>Modify Search</span>
      </button>
    </div>
  </header>

  <div class="main-grid">

    <!-- Sidebar Filters -->
    <aside class="sidebar">
      <div class="filters-card">
        <div class="flex-row justify-between items-center mb-4">
          <h3 style="font-weight: 800;">Filters</h3>
          <button id="clearFiltersBtn" style="font-size: 0.75rem; color: #00601f; background: none; border: none; cursor: pointer;">Clear All</button>
        </div>

        <!-- Time Filters -->
        <div class="filter-section">
          <label class="filter-label">Time of Day</label>
          <div class="filter-grid">
            <button data-time="dawn" class="filter-time-btn"><span class="material-symbols-outlined">wb_twilight</span><span>Dawn</span></button>
            <button data-time="morning" class="filter-time-btn active"><span class="material-symbols-outlined">light_mode</span><span>Morning</span></button>
            <button data-time="afternoon" class="filter-time-btn"><span class="material-symbols-outlined">wb_sunny</span><span>Afternoon</span></button>
            <button data-time="night" class="filter-time-btn"><span class="material-symbols-outlined">bedtime</span><span>Night</span></button>
          </div>
        </div>

        <!-- Price Filter -->
        <div class="filter-section">
          <label class="filter-label">Price Range (ZMW)</label>
          <input type="range" class="price-slider" min="360" max="800" value="800" id="priceSlider">
          <div class="flex-row justify-between mt-4">
            <span>K360</span>
            <span id="priceValue">K800</span>
            <span>K800</span>
          </div>
        </div>

        <!-- Operators Filter -->
        <div class="filter-section">
          <label class="filter-label">Preferred Operator</label>
          <div class="operator-list">
            <label class="operator-item" data-op="Power Tools"><div class="checkbox checked"><span class="material-symbols-outlined" style="font-size: 0.75rem; color: white;">check</span></div><span>Power Tools</span></label>
            <label class="operator-item" data-op="Rayon Bus"><div class="checkbox empty"></div><span>Rayon Bus</span></label>
            <label class="operator-item" data-op="Likili Motorways"><div class="checkbox empty"></div><span>Likili Motorways</span></label>
            <label class="operator-item" data-op="Mazhandu Family Bus"><div class="checkbox empty"></div><span>Mazhandu Family Bus</span></label>
          </div>
        </div>
      </div>
    </aside>

    <!-- Results Section -->
    <div class="results-section">
      <div id="resultsContainer"></div>

      <!-- Info Cards -->
      <div class="info-grid">
        <div class="info-card primary">
          <span class="material-symbols-outlined" style="color: #00601f; margin-bottom: 1rem;">security</span>
          <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Verified Operators</h4>
          <p style="font-size: 0.75rem; color: #71717a;">All operators pass safety inspection.</p>
        </div>
        <div class="info-card secondary">
          <span class="material-symbols-outlined" style="color: #954a00; margin-bottom: 1rem;">confirmation_number</span>
          <h4 style="font-weight: 700; margin-bottom: 0.5rem;">Instant Ticket</h4>
          <p style="font-size: 0.75rem; color: #71717a;">QR via SMS/WhatsApp after payment.</p>
        </div>
        <div class="info-card neutral">
          <span class="material-symbols-outlined" style="color: #71717a; margin-bottom: 1rem;">support_agent</span>
          <h4 style="font-weight: 700; margin-bottom: 0.5rem;">24/7 Support</h4>
          <p style="font-size: 0.75rem; color: #71717a;">Zambian support team available.</p>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Footer -->
<footer class="footer">
  <div class="footer-content">
    <div>
      <div style="font-weight: 700; margin-bottom: 0.5rem;">🚌 BookMyBus Zambia</div>
      <p style="font-size: 0.75rem; color: #71717a;">© 2025 BookMyBus Zambia. Premium Travel Excellence.</p>
    </div>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Carrier Partners</a>
      <a href="#">Contact Us</a>
    </div>
  </div>
</footer>

<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
  <div class="mobile-nav-item" onclick="window.location.href='index.html'">
    <span class="material-symbols-outlined">search</span>
    <span>Find</span>
  </div>
  <div class="mobile-nav-item" onclick="window.location.href='my-bookings.html'">
    <span class="material-symbols-outlined">confirmation_number</span>
    <span>Trips</span>
  </div>
  <div class="mobile-nav-item" onclick="window.location.href='user-profile.html'">
    <span class="material-symbols-outlined">account_circle</span>
    <span>Profile</span>
  </div>
</div>

<div id="toastMsg" class="toast-notify"></div>

<script>
  // Sample trip data
  const tripsData = [
    { id: 1, operator: "Power Tools", departureTime: "06:00", arrivalTime: "11:30", duration: "5h 30m", price: 320, seatsLeft: 12, type: "Luxury Class", rating: 4.8, departureTerminal: "Inter-City Terminus", arrivalTerminal: "Kitwe Station", category: "morning" },
    { id: 2, operator: "Rayob Bus", departureTime: "08:15", arrivalTime: "14:30", duration: "6h 15m", price: 260, seatsLeft: 4, type: "Semi-Luxury", rating: 4.2, departureTerminal: "Inter-City Terminus", arrivalTerminal: "Kitwe Station", category: "morning" },
    { id: 3, operator: "Likili Motorways", departureTime: "09:00", arrivalTime: "15:00", duration: "6h 0m", price: 240, seatsLeft: 0, type: "Standard", rating: 3.9, category: "morning", soldOut: true },
    { id: 4, operator: "Mazhandu Family Bus", departureTime: "14:30", arrivalTime: "20:00", duration: "5h 30m", price: 380, seatsLeft: 24, type: "Luxury Class", rating: 4.7, category: "afternoon" },
    { id: 5, operator: "Power Tools", departureTime: "19:00", arrivalTime: "00:30", duration: "5h 30m", price: 300, seatsLeft: 8, type: "Business", rating: 4.5, category: "night" }
  ];

  let activeFilters = { time: "morning", maxPrice: 800, operators: ["Power Tools"] };

  function showToast(msg) {
    const toast = document.getElementById('toastMsg');
    toast.innerText = msg;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), 2000);
  }

  function showLoading() {
    document.getElementById('resultsContainer').innerHTML = '<div class="text-center" style="padding: 3rem;"><div class="loading-spinner"></div><p style="margin-top: 1rem;">Loading buses...</p></div>';
  }

  function renderTrips() {
    showLoading();
    setTimeout(() => {
      let filtered = tripsData.filter(trip => {
        if (trip.price > activeFilters.maxPrice) return false;
        if (activeFilters.time !== "all" && trip.category !== activeFilters.time) return false;
        if (activeFilters.operators.length > 0 && !activeFilters.operators.includes(trip.operator)) return false;
        return true;
      });

      const container = document.getElementById('resultsContainer');
      if (filtered.length === 0) {
        container.innerHTML = `<div style="background: white; padding: 3rem; text-align: center; border-radius: 1rem;"><span class="material-symbols-outlined" style="font-size: 3rem; color: #a1a1aa;">search_off</span><p style="margin-top: 0.5rem; font-weight: bold;">No trips match your filters.</p><button onclick="clearAllFilters()" style="margin-top: 1rem; color: #00601f; background: none; border: none; cursor: pointer;">Clear all filters</button></div>`;
        return;
      }

      container.innerHTML = filtered.map(trip => {
        const isSoldOut = trip.seatsLeft === 0;
        const luxuryBadge = trip.type === "Luxury Class" ? `<div class="premium-badge">${trip.type}</div>` : (trip.type ? `<div style="position: absolute; top: 0; right: 0; background: #00601f; padding: 0.25rem 1rem; border-bottom-left-radius: 0.75rem; font-size: 0.625rem; font-weight: 700; color: white;">${trip.type}</div>` : '');
        const seatsClass = trip.seatsLeft <= 4 ? 'low' : '';
        return `<div class="result-card">
          ${luxuryBadge}
          <div class="card-content">
            <div class="operator-info">
              <div class="operator-logo"><div style="width: 100%; height: 100%; background: linear-gradient(135deg, #00601f, #197b30); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">BUS</div></div>
              <div class="operator-name">${trip.operator}</div>
              <div class="flex-row items-center"><span class="material-symbols-outlined" style="font-size: 0.75rem; color: #954a00;">star</span><span style="font-size: 0.625rem;">${trip.rating}</span></div>
            </div>
            <div class="trip-details">
              <div class="time-point"><div class="time-label">Departure</div><div class="time-value">${trip.departureTime}</div><div style="font-size: 0.75rem; color: #71717a;">${trip.departureTerminal || 'Inter-City Terminus'}</div></div>
              <div class="route-visual"><div class="duration">${trip.duration}</div><div class="route-line"><div class="dot-start"></div><div class="line"><div class="bus-icon"><span class="material-symbols-outlined" style="font-size: 1rem;">directions_bus</span></div></div><div class="dot-end"></div></div><div style="font-size: 0.625rem; color: #a1a1aa;">Direct Trip</div></div>
              <div class="time-point text-right"><div class="time-label">Arrival</div><div class="time-value">${trip.arrivalTime}</div><div style="font-size: 0.75rem; color: #71717a;">${trip.arrivalTerminal || 'Kitwe Station'}</div></div>
            </div>
            <div class="price-section"><div><span style="font-size: 0.625rem;">ZMW</span><span class="price-amount">${trip.price}</span></div><div class="seats-left ${seatsClass}">${isSoldOut ? 'Sold Out' : trip.seatsLeft + ' Seats left'}</div><button class="select-btn" data-trip='${JSON.stringify(trip)}' ${isSoldOut ? 'disabled style="opacity:0.5; cursor:not-allowed;"' : ''}>${isSoldOut ? 'Sold Out' : 'Select Seats'}</button></div>
          </div>
        </div>`;
      }).join('');

      document.querySelectorAll('.select-btn').forEach(btn => {
        btn.addEventListener('click', () => {
          if (btn.disabled) return;
          const trip = JSON.parse(btn.getAttribute('data-trip'));
          localStorage.setItem('selectedTrip', JSON.stringify(trip));
          window.location.href = 'seat-selection.html';
        });
      });
    }, 300);
  }

  function updateUI() {
    document.querySelectorAll('.filter-time-btn').forEach(btn => {
      if (btn.getAttribute('data-time') === activeFilters.time) btn.classList.add('active');
      else btn.classList.remove('active');
    });
    document.querySelectorAll('.operator-item').forEach(item => {
      const op = item.getAttribute('data-op');
      const checkDiv = item.querySelector('.checkbox');
      if (activeFilters.operators.includes(op)) {
        checkDiv.classList.add('checked');
        checkDiv.classList.remove('empty');
        checkDiv.innerHTML = '<span class="material-symbols-outlined" style="font-size: 0.75rem; color: white;">check</span>';
      } else {
        checkDiv.classList.remove('checked');
        checkDiv.classList.add('empty');
        checkDiv.innerHTML = '';
      }
    });
    document.getElementById('priceValue').innerText = `K${activeFilters.maxPrice}`;
    document.getElementById('priceSlider').value = activeFilters.maxPrice;
  }

  function clearAllFilters() {
    activeFilters = { time: "all", maxPrice: 800, operators: [] };
    updateUI();
    renderTrips();
    showToast("All filters cleared");
  }

  function goBack() { window.location.href = 'index.html'; }

  document.getElementById('priceSlider').addEventListener('input', (e) => {
    activeFilters.maxPrice = parseInt(e.target.value);
    updateUI();
    renderTrips();
  });

  document.querySelectorAll('.filter-time-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      activeFilters.time = btn.getAttribute('data-time');
      updateUI();
      renderTrips();
    });
  });

  document.querySelectorAll('.operator-item').forEach(item => {
    item.addEventListener('click', () => {
      const op = item.getAttribute('data-op');
      if (activeFilters.operators.includes(op)) {
        activeFilters.operators = activeFilters.operators.filter(o => o !== op);
      } else {
        activeFilters.operators.push(op);
      }
      updateUI();
      renderTrips();
    });
  });

  document.getElementById('clearFiltersBtn').addEventListener('click', clearAllFilters);

  renderTrips();
</script>
</body>
</html>
