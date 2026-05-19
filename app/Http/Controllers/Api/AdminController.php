<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Operator;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /*
      System overview dashboard stats.
     */
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'total_users'              => User::where('role', 'traveler')->count(),
            'total_operators'          => Operator::count(),
            'pending_operator_approvals' => Operator::where('is_verified', false)->count(),
            'total_bookings'           => Booking::count(),
            'confirmed_bookings'       => Booking::where('status', 'confirmed')->count(),
            'total_revenue_zmw'        => Payment::where('status', 'successful')->sum('amount'),
        ]);
    }

    /*
      List of all operators (whether verified and pending).
     */
    public function operators(Request $request): JsonResponse
    {
        $status    = $request->query('status', 'all');
        $query     = Operator::withCount(['buses', 'routes']);

        if ($status === 'pending') {
            $query->where('is_verified', false);
        } elseif ($status === 'verified') {
            $query->where('is_verified', true);
        }

        return response()->json(['operators' => $query->get()]);
    }

    /*
     Function for approving and verifying a bus operator.
     */
    public function verifyOperator(Request $request, int $id): JsonResponse
    {
        $operator = Operator::findOrFail($id);

        if ($operator->is_verified) {
            return response()->json(['message' => 'Operator is already verified.'], 409);
        }

        $operator->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
        ]);

        return response()->json([
            'message'  => 'Operator verified successfully.',
            'operator' => $operator,
        ]);
    }

    /*
     Suspend / unverify an operator.
     */
    public function suspendOperator(int $id): JsonResponse
    {
        $operator = Operator::findOrFail($id);

        $operator->update([
            'is_verified' => false,
            'verified_at' => null,
            'verified_by' => null,
        ]);

        return response()->json(['message' => 'Operator suspended successfully.']);
    }

   
     // List all travelers.
     
    public function users(): JsonResponse
    {
        $users = User::where('role', 'traveler')
            ->withCount('bookings')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['users' => $users]);
    }

    //List all bookings system-wide.
     
    public function bookings(Request $request): JsonResponse
    {
        $status   = $request->query('status');
        $query    = Booking::with(['user', 'route.operator', 'payment'])
                           ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        return response()->json(['bookings' => $query->paginate(50)]);
    }
}
