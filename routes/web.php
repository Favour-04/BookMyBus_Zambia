<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Operator\DashboardController;
use App\Http\Controllers\Operator\TripManagementController;
use App\Http\Controllers\Operator\BookingManagementController;
use App\Http\Controllers\Operator\RevenueController;
use App\Http\Controllers\Operator\CustomerController;
use App\Http\Controllers\Operator\ProfileController as OperatorProfileController;
use App\Http\Controllers\Operator\FareRuleController;
use App\Http\Controllers\Operator\PromoCodeController;
use App\Http\Controllers\Operator\RouteTemplateController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\Operator\LoginController as OperatorLoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ============================================
// PUBLIC ROUTES (No Authentication Required)
// ============================================

Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/search', [LandingController::class, 'search'])->name('trips.search');
Route::get('/support', function () {
    return view('support_page');
})->name('support.page');

// Static informational pages
Route::view('/privacy-policy', 'privacy_policy')->name('privacy-policy');
Route::view('/terms-of-service', 'terms_of_service')->name('terms-of-service');
Route::view('/carrier-partners', 'carrier_partners')->name('carrier-partners');
Route::view('/contact-us', 'contact_us')->name('contact-us');

// ============================================
// TRAVELER AUTH ROUTES (Guest Only)
// ============================================

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// ============================================
// OPERATOR AUTH ROUTES (Operator Guest Only)
// ============================================

Route::middleware('guest:operator')->group(function () {
    Route::get('/operator/login', [OperatorLoginController::class, 'showLoginForm'])->name('operator.login');
    Route::post('/operator/login', [OperatorLoginController::class, 'login']);
});

// ============================================
// ADMIN AUTH ROUTES (Admin Guest Only)
// ============================================

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [App\Http\Controllers\Auth\Admin\LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [App\Http\Controllers\Auth\Admin\LoginController::class, 'login']);
});

// ============================================
// TRAVELER AUTHENTICATED ROUTES
// ============================================

Route::middleware('auth')->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Booking
    Route::get('/booking/{route}/seats', [BookingController::class, 'showSeats'])->name('booking.seats');
    Route::post('/booking/store', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/payment/ticket/{booking}', [BookingController::class, 'paymentTicket'])->name('payment.ticket');
    Route::post('/payment/process/{booking}', [BookingController::class, 'processPayment'])->name('payment.process');
    Route::get('/booking/success/{booking}', [BookingController::class, 'success'])->name('booking.success');

    // Booking History
    Route::get('/my-booking', [BookingController::class, 'customerLookupView'])->name('booking.lookup');
Route::post('/my-booking/lookup', [BookingController::class, 'customerLookup'])->name('booking.lookup.search');

    // Promo Code Validation (AJAX)
    Route::post('/booking/validate-promo', [BookingController::class, 'validatePromoCode'])->name('booking.validate-promo');

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ============================================
// OPERATOR ROUTES (Requires Operator Auth)
// ============================================

Route::prefix('operator')->name('operator.')->middleware('auth:operator')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Revenue & Reports
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue');

    // Fleet / Bus Management
    Route::get('/buses', [App\Http\Controllers\Operator\BusController::class, 'index'])->name('buses.index');
    Route::get('/buses/{bus}', [App\Http\Controllers\Operator\BusController::class, 'show'])->name('buses.show');
    Route::post('/buses', [App\Http\Controllers\Operator\BusController::class, 'store'])->name('buses.store');
    Route::put('/buses/{bus}', [App\Http\Controllers\Operator\BusController::class, 'update'])->name('buses.update');
    Route::post('/buses/{bus}/toggle-status', [App\Http\Controllers\Operator\BusController::class, 'toggleStatus'])->name('buses.toggle-status');
    Route::delete('/buses/{bus}', [App\Http\Controllers\Operator\BusController::class, 'destroy'])->name('buses.destroy');

    // Audit Log
    Route::get('/audit-log', [App\Http\Controllers\Operator\AuditLogController::class, 'index'])->name('audit-log.index');
    Route::get('/audit-log/{id}', [App\Http\Controllers\Operator\AuditLogController::class, 'show'])->name('audit-log.show');

    // Customer Management
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');

    // Trip Management
    // NOTE: Static routes MUST come before {trip} wildcard routes
    Route::get('/trips', [TripManagementController::class, 'index'])->name('trips.index');
    Route::get('/trips/calendar', [TripManagementController::class, 'calendar'])->name('trips.calendar');
    Route::get('/bookings', [BookingManagementController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/export', [BookingManagementController::class, 'export'])->name('bookings.export');
    Route::post('/bookings/bulk-action', [BookingManagementController::class, 'bulkAction'])->name('bookings.bulk-action');
    Route::patch('/bookings/{booking}/cancel', [BookingManagementController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/process-refund', [BookingManagementController::class, 'processRefund'])->name('bookings.process-refund');
    Route::patch('/bookings/{booking}/board', [BookingManagementController::class, 'markBoarded'])->name('bookings.board');
    Route::patch('/bookings/{booking}/undo-board', [BookingManagementController::class, 'undoBoarded'])->name('bookings.undo-board');
    Route::patch('/bookings/{booking}/notes', [BookingManagementController::class, 'updateNotes'])->name('bookings.notes');
    Route::get('/bookings/{booking}/edit', [BookingManagementController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}', [BookingManagementController::class, 'update'])->name('bookings.update');
    Route::get('/bookings/{booking}/receipt', [BookingManagementController::class, 'printReceipt'])->name('bookings.receipt');
    Route::get('/bookings/{booking}', [BookingManagementController::class, 'show'])->name('bookings.show');
    Route::get('/trips/export', [TripManagementController::class, 'export'])->name('trips.export');
    Route::get('/trips/stats', [TripManagementController::class, 'stats'])->name('trips.stats');
    Route::get('/trips/upcoming', [TripManagementController::class, 'upcoming'])->name('trips.upcoming');
    Route::post('/trips', [TripManagementController::class, 'store'])->name('trips.store');

    // Wildcard {trip} routes below static ones
    Route::get('/trips/{trip}/seat-map', [TripManagementController::class, 'seatMap'])->name('trips.seat-map');
    Route::get('/trips/{trip}/occupancy', [TripManagementController::class, 'occupancy'])->name('trips.occupancy');
    Route::get('/trips/{trip}/bookings', [BookingManagementController::class, 'tripBookings'])->name('trips.bookings');
    Route::get('/trips/{trip}', [TripManagementController::class, 'show'])->name('trips.show');
    Route::put('/trips/{trip}', [TripManagementController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [TripManagementController::class, 'cancel'])->name('trips.cancel');
    Route::patch('/trips/{trip}/status', [TripManagementController::class, 'updateStatus'])->name('trips.update-status');

    // Fare Rules
    Route::get('/fare-rules', [FareRuleController::class, 'index'])->name('fare-rules.index');

    // Cancellation Rules
    Route::post('/fare-rules/cancellation', [FareRuleController::class, 'storeCancellationRule'])->name('fare-rules.cancellation.store');
    Route::put('/fare-rules/cancellation/{id}', [FareRuleController::class, 'updateCancellationRule'])->name('fare-rules.cancellation.update');
    Route::delete('/fare-rules/cancellation/{id}', [FareRuleController::class, 'destroyCancellationRule'])->name('fare-rules.cancellation.destroy');

    // Service Fees
    Route::post('/fare-rules/service-fees', [FareRuleController::class, 'storeServiceFee'])->name('fare-rules.service-fees.store');
    Route::put('/fare-rules/service-fees/{id}', [FareRuleController::class, 'updateServiceFee'])->name('fare-rules.service-fees.update');
    Route::delete('/fare-rules/service-fees/{id}', [FareRuleController::class, 'destroyServiceFee'])->name('fare-rules.service-fees.destroy');

    // Promo Codes
    Route::get('/promo-codes', [PromoCodeController::class, 'index'])->name('promo-codes.index');
    Route::post('/promo-codes', [PromoCodeController::class, 'store'])->name('promo-codes.store');
    Route::put('/promo-codes/{id}', [PromoCodeController::class, 'update'])->name('promo-codes.update');
    Route::delete('/promo-codes/{id}', [PromoCodeController::class, 'destroy'])->name('promo-codes.destroy');

    // Route Templates
    Route::get('/route-templates', [RouteTemplateController::class, 'index'])->name('route-templates.index');
    Route::post('/route-templates', [RouteTemplateController::class, 'store'])->name('route-templates.store');
    Route::put('/route-templates/{id}', [RouteTemplateController::class, 'update'])->name('route-templates.update');
    Route::delete('/route-templates/{id}', [RouteTemplateController::class, 'destroy'])->name('route-templates.destroy');
    Route::post('/route-templates/{id}/create-trip', [RouteTemplateController::class, 'createTrip'])->name('route-templates.create-trip');
    Route::post('/route-templates/{id}/create-bulk-trips', [RouteTemplateController::class, 'createBulkTrips'])->name('route-templates.create-bulk-trips');
    Route::get('/route-templates/json', [RouteTemplateController::class, 'getTemplatesJson'])->name('route-templates.json');

    // Profile
    Route::get('/profile', [OperatorProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [OperatorProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [OperatorProfileController::class, 'updatePassword'])->name('profile.password');

    // Passenger List
    Route::get('/passengers', [App\Http\Controllers\Operator\PassengerListController::class, 'index'])->name('passengers.index');
    Route::get('/passengers/manifest/{routeId}', [App\Http\Controllers\Operator\PassengerListController::class, 'manifest'])->name('passengers.manifest');
    Route::get('/passengers/export/{routeId}', [App\Http\Controllers\Operator\PassengerListController::class, 'export'])->name('passengers.export');
    Route::post('/passengers/bulk-checkin', [App\Http\Controllers\Operator\PassengerListController::class, 'bulkCheckin'])->name('passengers.bulk-checkin');

    // Drivers
    Route::get('/drivers', [App\Http\Controllers\Operator\DriverController::class, 'index'])->name('drivers.index');
    Route::post('/drivers', [App\Http\Controllers\Operator\DriverController::class, 'store'])->name('drivers.store');
    Route::put('/drivers/{id}', [App\Http\Controllers\Operator\DriverController::class, 'update'])->name('drivers.update');
    Route::get('/drivers/{id}/json', [App\Http\Controllers\Operator\DriverController::class, 'json'])->name('drivers.json');
    Route::delete('/drivers/{id}', [App\Http\Controllers\Operator\DriverController::class, 'destroy'])->name('drivers.destroy');

    // Trip Management Enhancements
    Route::post('/trips/{trip}/delay', [TripManagementController::class, 'markDelayed'])->name('trips.delay');
    Route::post('/trips/{trip}/depart', [TripManagementController::class, 'markDeparted'])->name('trips.depart');
    Route::post('/trips/{trip}/arrive', [TripManagementController::class, 'markArrived'])->name('trips.arrive');
    Route::post('/trips/{trip}/assign-driver', [TripManagementController::class, 'assignDriver'])->name('trips.assign-driver');
    Route::get('/trips/{trip}/json', [TripManagementController::class, 'tripJson'])->name('trips.json');
    Route::post('/trips/{trip}/cancel-notify', [TripManagementController::class, 'cancelWithNotification'])->name('trips.cancel-notify');
    Route::get('/schedule/printable', [TripManagementController::class, 'printableSchedule'])->name('schedule.printable');

    // Logout
    Route::post('/logout', [OperatorLoginController::class, 'logout'])->name('logout');
});

// ============================================
// ADMIN ROUTES (Requires Admin Auth)
// ============================================

Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Operator verification & management
    Route::get('/operators', [App\Http\Controllers\Admin\OperatorController::class, 'index'])->name('operators.index');
    Route::get('/operators/{id}', [App\Http\Controllers\Admin\OperatorController::class, 'show'])->name('operators.show');
    Route::post('/operators/{id}/verify', [App\Http\Controllers\Admin\OperatorController::class, 'verify'])->name('operators.verify');
    Route::post('/operators/{id}/suspend', [App\Http\Controllers\Admin\OperatorController::class, 'suspend'])->name('operators.suspend');
    Route::delete('/operators/{id}', [App\Http\Controllers\Admin\OperatorController::class, 'destroy'])->name('operators.destroy');

    // Admin audit log
    Route::get('/audit-log', [App\Http\Controllers\Admin\AuditLogController::class, 'index'])->name('audit-log.index');

    // System-wide bookings
    Route::get('/bookings', [App\Http\Controllers\Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{id}', [App\Http\Controllers\Admin\BookingController::class, 'show'])->name('bookings.show');

    // Traveler / user management
    Route::get('/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/suspend', [App\Http\Controllers\Admin\UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{id}/activate', [App\Http\Controllers\Admin\UserController::class, 'activate'])->name('users.activate');
    Route::delete('/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::post('/logout', [App\Http\Controllers\Auth\Admin\LoginController::class, 'logout'])->name('logout');
});

// ============================================
// FALLBACK ROUTE
// ============================================

// Route::fallback(fn() => view('errors.404'));
