<?php

namespace Database\Seeders;

use App\Models\Fleet;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // Remove legacy admin if exists
        User::where('email', 'admin@fleetco.com')->delete();

        // Create or update default Fleet
        $fleet = Fleet::updateOrCreate(
            ['name' => 'Fleetco HQ'],
            [
                'name'        => 'Fleetco HQ',
                'description' => 'Default operational fleet',
            ]
        );

        // Create initial Admin user with fleet assigned
        User::updateOrCreate(
            ['email' => 'fleetcosupport@gmail.com'],
            [
                'first_name'        => 'Fleet',
                'last_name'         => 'Administrator',
                'name'              => 'Fleet Administrator',
                'password'          => Hash::make('Fleetco@MinVa'),
                'role'              => 'admin',
                'fleet_id'          => $fleet->id,
                'email_verified_at' => now(),
            ]
        );
    }
}
