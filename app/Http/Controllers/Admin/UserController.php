<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * List travelers with search, status filter and pagination.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = trim((string) $request->get('search'));

        $query = User::withCount('bookings')->where('role', 'traveler');

        if ($status === 'active') {
            $query->where('is_active', true);
        } elseif ($status === 'suspended') {
            $query->where('is_active', false);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $counts = [
            'all'        => User::where('role', 'traveler')->count(),
            'active'     => User::where('role', 'traveler')->where('is_active', true)->count(),
            'suspended'  => User::where('role', 'traveler')->where('is_active', false)->count(),
            'new_month'  => User::where('role', 'traveler')
                            ->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count(),
        ];

        return view('admin.users.index', compact('users', 'status', 'search', 'counts'));
    }

    /**
     * Show a single traveler's profile and booking history.
     */
    public function show($id)
    {
        $user = User::withCount('bookings')->findOrFail($id);

        $stats = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'confirmed'      => Booking::where('user_id', $user->id)->where('status', 'confirmed')->count(),
            'pending'        => Booking::where('user_id', $user->id)->where('status', 'pending')->count(),
            'cancelled'      => Booking::where('user_id', $user->id)->where('status', 'cancelled')->count(),
            'total_spent'    => Booking::where('user_id', $user->id)->where('status', 'confirmed')->sum('amount'),
        ];

        $bookings = Booking::with(['route'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(15)
            ->get();

        AdminAuditService::log(
            'user.viewed',
            "Viewed traveler {$user->full_name}",
            $user
        );

        return view('admin.users.show', compact('user', 'stats', 'bookings'));
    }

    /**
     * Show the form to create a new user account.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user (traveler or admin).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone_number'  => 'required|string|unique:users,phone_number',
            'password'      => 'required|string|min:8|confirmed',
            'role'          => 'required|in:traveler,admin',
            'preferred_language' => 'nullable|string|max:10',
        ]);

        $user = User::create([
            'full_name'          => $validated['full_name'],
            'email'              => $validated['email'],
            'phone_number'       => $validated['phone_number'],
            'password'           => $validated['password'],
            'role'               => $validated['role'],
            'preferred_language' => $validated['preferred_language'] ?? 'en',
            'is_active'          => true,
        ]);

        AdminAuditService::log(
            'user.created',
            "Created {$user->role} {$user->full_name}",
            $user,
            null,
            ['role' => $user->role],
            $request
        );

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "Account for {$user->full_name} created successfully.");
    }

    /**
     * Suspend (deactivate) a traveler account.
     */
    public function suspend(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if (!$user->isActive()) {
            return back()->with('warning', "{$user->full_name}'s account is already suspended.");
        }
        if ((int) Auth::guard('admin')->id() === (int) $user->id) {
            return back()->with('error', 'You cannot suspend your own admin account.');
        }

        $user->update(['is_active' => false]);

        AdminAuditService::log(
            'user.suspended',
            "Suspended traveler {$user->full_name}",
            $user,
            ['is_active' => true],
            ['is_active' => false],
            $request
        );

        return back()->with('success', "{$user->full_name}'s account has been suspended.");
    }

    /**
     * Reactivate a traveler account.
     */
    public function activate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->isActive()) {
            return back()->with('warning', "{$user->full_name}'s account is already active.");
        }

        $user->update(['is_active' => true]);

        AdminAuditService::log(
            'user.activated',
            "Activated traveler {$user->full_name}",
            $user,
            ['is_active' => false],
            ['is_active' => true],
            $request
        );

        return back()->with('success', "{$user->full_name}'s account has been activated.");
    }

    /**
     * Soft delete a traveler account.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ((int) Auth::guard('admin')->id() === (int) $user->id) {
            return back()->with('error', 'You cannot remove your own admin account.');
        }

        $name = $user->full_name;

        AdminAuditService::log(
            'user.deleted',
            "Removed traveler {$name}",
            $user,
            $user->only(['email', 'is_active']),
            ['deleted_at' => now()->toDateTimeString()],
            $request
        );

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "{$name}'s account has been removed.");
    }
}