<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'full_name'    => 'Chanda Mwale',
                'email'        => 'chanda@example.zm',
                'phone_number' => '+260977100001',
                'password'     => Hash::make('Password@1'),
                'role'         => 'traveler',
            ],
            [
                'full_name'    => 'Mutale Bwalya',
                'email'        => 'mutale@example.zm',
                'phone_number' => '+260977100002',
                'password'     => Hash::make('Password@1'),
                'role'         => 'traveler',
            ],
            [
                'full_name'    => 'Natasha Phiri',
                'email'        => 'natasha@example.zm',
                'phone_number' => '+260977100003',
                'password'     => Hash::make('Password@1'),
                'role'         => 'traveler',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }

        $this->command->info('✅ 3 traveler accounts created — password: Password@1');
    }
}
