<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Route;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $chanda  = User::where('email', 'chanda@example.zm')->first();
        $mutale  = User::where('email', 'mutale@example.zm')->first();
        $natasha = User::where('email', 'natasha@example.zm')->first();

        // Kitwe → Lusaka (economy, today) — first route seeded
        $kitweRoute    = Route::where('origin', 'Kitwe')
            ->where('destination', 'Lusaka')
            ->where('fare', 180.00)
            ->first();

        // Lusaka → Livingstone (economy, today)
        $livingstoneRoute = Route::where('origin', 'Lusaka')
            ->where('destination', 'Livingstone')
            ->where('fare', 220.00)
            ->first();

        $seed = [
            // Confirmed booking + payment + ticket
            [
                'user'        => $chanda,
                'route'       => $kitweRoute,
                'seat_number' => 5,
                'amount'      => $kitweRoute->fare,
                'status'      => 'confirmed',
                'with_ticket' => true,
            ],
            [
                'user'        => $mutale,
                'route'       => $kitweRoute,
                'seat_number' => 6,
                'amount'      => $kitweRoute->fare,
                'status'      => 'confirmed',
                'with_ticket' => true,
            ],
            // Pending booking (seat held, no payment yet)
            [
                'user'        => $natasha,
                'route'       => $kitweRoute,
                'seat_number' => 7,
                'amount'      => $kitweRoute->fare,
                'status'      => 'pending',
                'with_ticket' => false,
            ],
            // Confirmed on a different route
            [
                'user'        => $natasha,
                'route'       => $livingstoneRoute,
                'seat_number' => 12,
                'amount'      => $livingstoneRoute->fare,
                'status'      => 'confirmed',
                'with_ticket' => true,
            ],
        ];

        foreach ($seed as $item) {
            $booking = Booking::create([
                'user_id'     => $item['user']->id,
                'route_id'    => $item['route']->id,
                'seat_number' => $item['seat_number'],
                'amount'      => $item['amount'],
                'status'      => $item['status'],
            ]);

            if ($item['status'] === 'confirmed') {
                $payment = Payment::create([
                    'booking_id'            => $booking->id,
                    'amount'                => $item['route']->fare,
                    'currency'              => 'ZMW',
                    'payment_method'        => 'mtn_money',
                    'status'                => 'successful',
                    'transaction_reference' => 'MTN-' . strtoupper(substr(md5(uniqid()), 0, 10)),
                    'gateway_response'      => ['provider' => 'MTN', 'code' => '200', 'message' => 'OK'],
                    'paid_at'               => now(),
                ]);
            }

            if ($item['with_ticket']) {
                Ticket::create([
                    'booking_id' => $booking->id,
                    'user_id'    => $item['user']->id,
                ]);
            }
        }

        $this->command->info('✅ 4 bookings created (3 confirmed with tickets, 1 pending)');
    }
}
