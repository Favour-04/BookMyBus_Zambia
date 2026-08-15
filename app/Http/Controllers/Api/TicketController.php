<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TicketController extends Controller
{
    //Get all tickets for the authenticated traveler.
     
    public function index(Request $request): JsonResponse
    {
        $tickets = Ticket::with(['booking.route.operator', 'booking.route.bus'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('issued_at')
            ->get()
            ->map(fn($ticket) => $this->formatTicket($ticket));

        return response()->json(['tickets' => $tickets]);
    }

    // Get a single ticket by QR code string.
    // Used by traveler to view their ticket.
    public function show(Request $request, string $qrCode): JsonResponse
    {
        $ticket = Ticket::with(['booking.route.operator', 'booking.route.bus', 'user'])
            ->where('qr_code', $qrCode)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json(['ticket' => $this->formatTicket($ticket)]);
    }

    // Verify a ticket at the bus station (operator only).
    // Marks the ticket as used if valid.
     
    public function verify(Request $request): JsonResponse
    {
        $data = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $ticket = Ticket::with(['booking.route', 'user'])
            ->where('qr_code', $data['qr_code'])
            ->firstOrFail();

        // Ensure ticket belongs to a route this operator owns
        if ($ticket->booking->route->operator_id !== auth()->guard('operator')->id()) {
            return response()->json(['message' => 'This ticket does not belong to your route.'], 403);
        }

        if (!$ticket->isValid()) {
            return response()->json([
                'message' => 'Ticket is ' . $ticket->status . '.',
                'ticket'  => $this->formatTicket($ticket),
            ], 422);
        }

        $ticket->markAsUsed();

        return response()->json([
            'message' => 'Ticket verified successfully. Passenger may board.',
            'ticket'  => $this->formatTicket($ticket),
        ]);
    }

    // private helpers

    private function formatTicket(Ticket $ticket): array
    {
        $booking = $ticket->booking;
        $route   = $booking->route;

        return [
            'reference_id'   => $booking->reference_id,
            'qr_code'        => $ticket->qr_code,
            'status'         => $ticket->status,
            'passenger_name' => $ticket->user->full_name ?? $booking->user->full_name,
            'operator'       => $route->operator->company_name,
            'origin'         => $route->origin,
            'destination'    => $route->destination,
            'travel_date'    => $route->travel_date->format('d M Y'),
            'departure_time' => $route->departure_time,
            'seat_number'    => $booking->seat_number,
            'fare'           => $route->fare,
            'issued_at'      => $ticket->issued_at->format('d M Y H:i'),
        ];
    }
}
