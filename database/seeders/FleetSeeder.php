<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // Create initial Admin user (only if not already exists)
        if (!User::where('email', 'admin@fleetco.com')->exists()) {
            User::create([
                'first_name' => 'Fleet',
                'last_name'  => 'Commander',
                'name'       => 'Fleet Commander',
                'email'      => 'admin@fleetco.com',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
            ]);
        }

        // All drivers, vehicles, and other data are created dynamically
        // through the admin UI — no dummy data is seeded.
    }
}
