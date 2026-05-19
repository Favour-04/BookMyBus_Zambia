<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\Operator;
use Illuminate\Database\Seeder;

class BusSeeder extends Seeder
{
    public function run(): void
    {
        $powerTools = Operator::where('email', 'powertools@bookmybus.zm')->first();
        $shalom     = Operator::where('email', 'shalom@bookmybus.zm')->first();

        $buses = [
            // Power Tools buses
            [
                'operator_id'         => $powerTools->id,
                'registration_number' => 'ABB 1234 CP',
                'model'               => 'Scania K410',
                'seat_capacity'       => 49,
                'bus_class'           => 'economy',
                'amenities'           => ['ac'],
                'is_active'           => true,
            ],
            [
                'operator_id'         => $powerTools->id,
                'registration_number' => 'ABB 5678 CP',
                'model'               => 'Volvo B11R',
                'seat_capacity'       => 44,
                'bus_class'           => 'business',
                'amenities'           => ['ac', 'wifi', 'usb_charging', 'reclining_seats'],
                'is_active'           => true,
            ],

            // Shalom buses
            [
                'operator_id'         => $shalom->id,
                'registration_number' => 'ABA 9012 LS',
                'model'               => 'Higer KLQ6122',
                'seat_capacity'       => 55,
                'bus_class'           => 'economy',
                'amenities'           => ['ac'],
                'is_active'           => true,
            ],
            [
                'operator_id'         => $shalom->id,
                'registration_number' => 'ABA 3456 LS',
                'model'               => 'Yutong ZK6122H9',
                'seat_capacity'       => 40,
                'bus_class'           => 'luxury',
                'amenities'           => ['ac', 'wifi', 'usb_charging', 'reclining_seats', 'onboard_toilet'],
                'is_active'           => true,
            ],
        ];

        foreach ($buses as $bus) {
            Bus::updateOrCreate(
                ['registration_number' => $bus['registration_number']],
                $bus
            );
        }

        $this->command->info('✅ 4 buses created (2 per operator)');
    }
}
