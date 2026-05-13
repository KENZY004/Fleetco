<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // Remove legacy admin if exists
        User::where('email', 'admin@fleetco.com')->delete();

        // Create initial Admin user (ensure fleetcosupport@gmail.com is the only admin)
        User::updateOrCreate(
            ['email' => 'fleetcosupport@gmail.com'],
            [
                'first_name' => 'Fleet',
                'last_name'  => 'Administrator',
                'name'       => 'Fleet Administrator',
                'password'   => Hash::make('Fleetco@MinVa'),
                'role'       => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Add active test route for the driver
        $driver = User::where('email', 'kenzninnu409@gmail.com')->first();
        if ($driver && !\App\Models\FleetRoute::where('driver_id', $driver->id)->exists()) {
            \App\Models\FleetRoute::create([
                'fleet_id' => $driver->fleet_id,
                'driver_id' => $driver->id,
                'name' => 'Test Delivery Route',
                'status' => 'active',
                'waypoints' => [
                    ['lat' => 19.0760, 'lng' => 72.8777, 'label' => 'Warehouse Alpha', 'order' => 1, 'reached_at' => null],
                    ['lat' => 19.0800, 'lng' => 72.8800, 'label' => 'Dropoff Beta', 'order' => 2, 'reached_at' => null]
                ],
                'created_by' => 1
            ]);
        }
    }
}
