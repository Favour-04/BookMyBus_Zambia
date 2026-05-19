<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Seed the BookMyBus Zambia database.
    // Order matters — foreign keys require parents to exist first.
     
    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🚌 Seeding BookMyBus Zambia...');
        $this->command->info('─────────────────────────────────');

        $this->call([
            AdminSeeder::class,    // 1. Admin user
            UserSeeder::class,     // 2. Traveler accounts
            OperatorSeeder::class, // 3. Bus companies (needs admin ID for verified_by)
            BusSeeder::class,      // 4. Buses (needs operator IDs)
            RouteSeeder::class,    // 5. Routes (needs operator + bus IDs)
            BookingSeeder::class,  // 6. Bookings, payments, tickets (needs all above)
        ]);

        $this->command->info('─────────────────────────────────');
        $this->command->info('✅ All done! Test credentials:');
        $this->command->info('');
        $this->command->info('  Admin:    admin@bookmybus.zm     / Admin@1234');
        $this->command->info('  Traveler: chanda@example.zm      / Password@1');
        $this->command->info('  Traveler: mutale@example.zm      / Password@1');
        $this->command->info('  Traveler: natasha@example.zm     / Password@1');
        $this->command->info('  Operator: powertools@bookmybus.zm / Operator@1234');
        $this->command->info('  Operator: shalom@bookmybus.zm     / Operator@1234');
        $this->command->info('');
    }
}
