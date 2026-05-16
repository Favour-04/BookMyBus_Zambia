<?php

namespace Database\Seeders;

use App\Models\Operator;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        $operators = [
            [
                'company_name' => 'Power Tools Bus Services',
                'email'        => 'powertools@bookmybus.zm',
                'phone_number' => '+260211100001',
                'password'     => Hash::make('Operator@1234'),
                'tpin'         => 'ZM-TPIN-10001',
                'address'      => 'Lumumba Road, Kitwe, Copperbelt',
                'is_verified'  => true,
                'verified_at'  => now(),
                'verified_by'  => $admin?->id,
            ],
            [
                'company_name' => 'Shalom Express',
                'email'        => 'shalom@bookmybus.zm',
                'phone_number' => '+260211100002',
                'password'     => Hash::make('Operator@1234'),
                'tpin'         => 'ZM-TPIN-10002',
                'address'      => 'Cairo Road, Lusaka',
                'is_verified'  => true,
                'verified_at'  => now(),
                'verified_by'  => $admin?->id,
            ],
            [
                'company_name' => 'Mazhandu Family Bus',
                'email'        => 'mazhandu@bookmybus.zm',
                'phone_number' => '+260211100003',
                'password'     => Hash::make('Operator@1234'),
                'tpin'         => 'ZM-TPIN-10003',
                'address'      => 'Chachacha Road, Lusaka',
                'is_verified'  => false, // pending — to test admin verify flow
            ],
        ];

        foreach ($operators as $operator) {
            Operator::updateOrCreate(['email' => $operator['email']], $operator);
        }

        $this->command->info('✅ 3 operators created (2 verified, 1 pending) — password: Operator@1234');
    }
}
