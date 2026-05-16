<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Operator;
use App\Models\Route;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    public function run(): void
    {
        $powerTools = Operator::where('email', 'powertools@bookmybus.zm')->first();
        $shalom     = Operator::where('email', 'shalom@bookmybus.zm')->first();

        $ptEconomy  = Bus::where('registration_number', 'ABB 1234 CP')->first();
        $ptBusiness = Bus::where('registration_number', 'ABB 5678 CP')->first();
        $shEconomy  = Bus::where('registration_number', 'ABA 9012 LS')->first();
        $shLuxury   = Bus::where('registration_number', 'ABA 3456 LS')->first();

        $today    = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        $dayAfter = now()->addDays(2)->toDateString();

        $routes = [
            // ── Power Tools ─────────────────────────────────────────
            [
                'operator_id'    => $powerTools->id,
                'bus_id'         => $ptEconomy->id,
                'origin'         => 'Kitwe',
                'destination'    => 'Lusaka',
                'departure_time' => '06:00',
                'arrival_time'   => '11:30',
                'fare'           => 180.00,
                'travel_date'    => $today,
                'is_active'      => true,
            ],
            [
                'operator_id'    => $powerTools->id,
                'bus_id'         => $ptBusiness->id,
                'origin'         => 'Kitwe',
                'destination'    => 'Lusaka',
                'departure_time' => '08:00',
                'arrival_time'   => '13:30',
                'fare'           => 250.00,
                'travel_date'    => $today,
                'is_active'      => true,
            ],
            [
                'operator_id'    => $powerTools->id,
                'bus_id'         => $ptEconomy->id,
                'origin'         => 'Kitwe',
                'destination'    => 'Ndola',
                'departure_time' => '07:00',
                'arrival_time'   => '07:45',
                'fare'           => 50.00,
                'travel_date'    => $today,
                'is_active'      => true,
            ],
            [
                'operator_id'    => $powerTools->id,
                'bus_id'         => $ptEconomy->id,
                'origin'         => 'Kitwe',
                'destination'    => 'Lusaka',
                'departure_time' => '06:00',
                'arrival_time'   => '11:30',
                'fare'           => 180.00,
                'travel_date'    => $tomorrow,
                'is_active'      => true,
            ],

            // ── Shalom Express ───────────────────────────────────────
            [
                'operator_id'    => $shalom->id,
                'bus_id'         => $shEconomy->id,
                'origin'         => 'Lusaka',
                'destination'    => 'Livingstone',
                'departure_time' => '07:00',
                'arrival_time'   => '13:00',
                'fare'           => 220.00,
                'travel_date'    => $today,
                'is_active'      => true,
            ],
            [
                'operator_id'    => $shalom->id,
                'bus_id'         => $shLuxury->id,
                'origin'         => 'Lusaka',
                'destination'    => 'Livingstone',
                'departure_time' => '09:00',
                'arrival_time'   => '15:00',
                'fare'           => 350.00,
                'travel_date'    => $today,
                'is_active'      => true,
            ],
            [
                'operator_id'    => $shalom->id,
                'bus_id'         => $shEconomy->id,
                'origin'         => 'Ndola',
                'destination'    => 'Lusaka',
                'departure_time' => '05:30',
                'arrival_time'   => '11:00',
                'fare'           => 170.00,
                'travel_date'    => $tomorrow,
                'is_active'      => true,
            ],
            [
                'operator_id'    => $shalom->id,
                'bus_id'         => $shLuxury->id,
                'origin'         => 'Lusaka',
                'destination'    => 'Chipata',
                'departure_time' => '06:00',
                'arrival_time'   => '14:00',
                'fare'           => 300.00,
                'travel_date'    => $dayAfter,
                'is_active'      => true,
            ],
        ];

        foreach ($routes as $route) {
            Route::create($route);
        }

        $this->command->info('✅ 8 routes created across Zambia (today, tomorrow, day after)');
    }
}
