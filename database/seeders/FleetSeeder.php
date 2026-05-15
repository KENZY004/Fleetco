<?php

namespace Database\Seeders;

use App\Models\Fleet;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\TelematicsLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create or update default Fleet
        $fleet = Fleet::updateOrCreate(
            ['name' => 'Fleetco HQ'],
            [
                'name' => 'Fleetco HQ',
            ]
        );

        // 2. Create initial Admin user
        User::updateOrCreate(
            ['email' => 'fleetcosupport@gmail.com'],
            [
                'name'              => 'Fleet Administrator',
                'password'          => Hash::make('Fleetco@MinVa'),
                'role'              => 'admin',
                'fleet_id'          => $fleet->id,
                'email_verified_at' => now(),
            ]
        );

        // 3. Create sample Driver
        User::updateOrCreate(
            ['email' => 'kenzninnu409@gmail.com'],
            [
                'name'     => 'driver1',
                'password' => Hash::make('password'),
                'role'     => 'driver',
                'fleet_id' => $fleet->id,
            ]
        );

        // 4. Create 3 sample vehicles with starting locations
        $vehicles = [
            ['lp' => 'MH-01-V-2024', 'name' => 'Swift Courier 01'],
            ['lp' => 'MH-04-A-1100', 'name' => 'Express Van 02'],
            ['lp' => 'MH-12-Z-9988', 'name' => 'Heavy Hauler 03'],
        ];

        foreach ($vehicles as $vData) {
            $vehicle = Vehicle::updateOrCreate(
                ['license_plate' => $vData['lp']],
                [
                    'name'     => $vData['name'],
                    'status'   => 'active',
                    'fleet_id' => $fleet->id,
                ]
            );

            // Create a log entry so the map centers correctly
            TelematicsLog::create([
                'vehicle_id' => $vehicle->id,
                'location'   => DB::raw("ST_GeomFromText('POINT(72.8777 19.0760)')"), // Mumbai center
                'speed'      => 0,
                'captured_at' => now(),
            ]);
        }
    }
}
