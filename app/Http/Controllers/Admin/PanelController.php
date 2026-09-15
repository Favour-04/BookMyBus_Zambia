<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Bus;
use App\Models\Route as BusRoute;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PanelController extends Controller
{
    /**
     * Admin bus search dashboard.
     */
    public function index(): View
    {
        $stats = [
            'routes_today'     => BusRoute::whereDate('travel_date', today())->count(),
            'active_buses'     => Bus::where('is_active', true)->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];

        $origins      = BusRoute::query()->select('origin')->distinct()->orderBy('origin')->pluck('origin');
        $destinations = BusRoute::query()->select('destination')->distinct()->orderBy('destination')->pluck('destination');

        return view('admin.index', compact('stats', 'origins', 'destinations'));
    }

    /**
     * Available buses, route filters, and fare matrix.
     */
    public function searchResults(Request $request): View
    {
        $filters = $request->validate([
            'origin'      => 'nullable|string|max:255',
            'destination' => 'nullable|string|max:255',
            'travel_date' => 'nullable|date',
            'bus_class'   => 'nullable|in:economy,business,luxury',
        ]);

        $routes = BusRoute::query()
            ->with(['bus', 'operator'])
            ->where('is_active', true)
            ->when($filters['origin'] ?? null, fn ($q, $v) => $q->where('origin', 'like', "%{$v}%"))
            ->when($filters['destination'] ?? null, fn ($q, $v) => $q->where('destination', 'like', "%{$v}%"))
            ->when($filters['travel_date'] ?? null, fn ($q, $v) => $q->whereDate('travel_date', $v))
            ->when($filters['bus_class'] ?? null, fn ($q, $v) => $q->whereHas('bus', fn ($b) => $b->where('bus_class', $v)))
            ->orderBy('travel_date')
            ->orderBy('departure_time')
            ->paginate(10)
            ->withQueryString();

        // Fare matrix: lowest / highest / average fare grouped by bus class.
        $fareMatrix = BusRoute::query()
            ->join('buses', 'buses.id', '=', 'routes.bus_id')
            ->where('routes.is_active', true)
            ->selectRaw('buses.bus_class, MIN(routes.fare) as min_fare, MAX(routes.fare) as max_fare, AVG(routes.fare) as avg_fare, COUNT(*) as trip_count')
            ->groupBy('buses.bus_class')
            ->get();

        return view('admin.search-results', [
            'routes'     => $routes,
            'filters'    => $filters,
            'fareMatrix' => $fareMatrix,
        ]);
    }

    /**
     * Show the traveler / admin login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'These credentials do not match our records.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Welcome back, ' . Auth::user()->full_name . '!');
    }

    /**
     * Show the account registration form.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name'           => 'required|string|max:255',
            'email'               => 'required|email|unique:users,email',
            'phone_number'        => 'required|string|unique:users,phone_number',
            'password'            => ['required', 'confirmed', Password::min(8)],
            'preferred_language'  => 'nullable|in:en,ny,bem,loz',
        ]);

        $user = User::create([
            'full_name'           => $data['full_name'],
            'email'               => $data['email'],
            'phone_number'        => $data['phone_number'],
            'password'            => $data['password'],
            'role'                => 'traveler',
            'preferred_language'  => $data['preferred_language'] ?? 'en',
        ]);

        Auth::login($user);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Account created successfully. Welcome to BookMyBus Zambia!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }

    /**
     * Interactive seat layout & seat selection UI.
     */
    public function booking(BusRoute $route): View
    {
        $route->load('bus', 'operator');

        return view('admin.booking', [
            'route'       => $route,
            'bookedSeats' => $route->bookedSeats(),
        ]);
    }

    public function holdSeat(Request $request, BusRoute $route): RedirectResponse
    {
        $data = $request->validate([
            'seat_number' => 'required|integer|min:1|max:' . $route->bus->seat_capacity,
        ]);

        if (in_array((int) $data['seat_number'], $route->bookedSeats(), true)) {
            return back()->withErrors(['seat_number' => 'That seat has just been taken. Please choose another one.']);
        }

        $booking = Booking::create([
            'user_id'     => Auth::id(),
            'route_id'    => $route->id,
            'seat_number' => $data['seat_number'],
            'status'      => 'pending',
        ]);

        return redirect()->route('admin.payment', $booking)
            ->with('success', 'Seat ' . $data['seat_number'] . ' held for 10 minutes. Complete payment to confirm your ticket.');
    }

    /**
     * Mobile money checkout interface.
     */
    public function payment(Booking $booking): View
    {
        $booking->load('route.bus', 'route.operator', 'payment');

        return view('admin.payment', ['booking' => $booking]);
    }

    public function processPayment(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'payment_method' => 'required|in:mtn_money,airtel_money,zanaco,card',
            'phone_number'   => 'required_if:payment_method,mtn_money,airtel_money|nullable|string|max:20',
        ]);

        if ($booking->isExpired()) {
            $booking->update(['status' => 'expired']);

            return back()->withErrors(['payment_method' => 'Your seat hold has expired. Please start a new booking.']);
        }

        $booking->payment()->firstOrCreate([], [
            'amount'         => $booking->route->fare,
            'currency'       => 'ZMW',
            'payment_method' => $data['payment_method'],
            'status'         => 'pending',
        ]);

        // Real integration point: dispatch the STK / USSD push to the MTN, Airtel or
        // Zamtel gateway here and let PaymentController@callback confirm the booking.

        return redirect()->route('admin.profile')
            ->with('success', 'A payment request was sent to your ' . str_replace('_', ' ', $data['payment_method']) . ' number. Approve it on your phone to confirm your ticket.');
    }

    /**
     * Profile editor view.
     */
    public function profile(): View
    {
        $user = Auth::user();

        $bookings = Booking::with('route', 'payment')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('admin.user-profile', compact('user', 'bookings'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $data = $request->validate([
            'full_name'          => 'required|string|max:255',
            'email'              => 'required|email|unique:users,email,' . $user->id,
            'phone_number'       => 'required|string|unique:users,phone_number,' . $user->id,
            'preferred_language' => 'nullable|in:en,ny,bem,loz',
            'password'           => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->fill([
            'full_name'          => $data['full_name'],
            'email'              => $data['email'],
            'phone_number'       => $data['phone_number'],
            'preferred_language' => $data['preferred_language'] ?? $user->preferred_language,
        ]);

        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }

        $user->save();

        return back()->with('success', 'Your profile has been updated.');
    }

    /**
     * Help desk and support contact form.
     */
    public function support(): View
    {
        return view('admin.support');
    }

    public function submitSupport(Request $request): RedirectResponse
    {
        $request->validate([
            'subject'      => 'required|string|max:255',
            'category'     => 'required|in:booking,payment,refund,account,other',
            'reference_id' => 'nullable|string|max:50',
            'message'      => 'required|string|max:2000',
        ]);

        // Real integration point: create a Support/Ticket record or dispatch a
        // notification/mail to the support team here.

        return back()->with('success', 'Your support request has been submitted. Our team will respond within 24 hours.');
    }
}
