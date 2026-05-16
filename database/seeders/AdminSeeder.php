<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bookmybus.zm'],
            [
                'full_name'    => 'BookMyBus Admin',
                'email'        => 'admin@bookmybus.zm',
                'phone_number' => '+260971000001',
                'password'     => Hash::make('Admin@1234'),
                'role'         => 'admin',
            ]
        );

        $this->command->info('✅ Admin user created: admin@bookmybus.zm / Admin@1234');
    }
}
