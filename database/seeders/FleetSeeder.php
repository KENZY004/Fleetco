<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use App\Models\TelematicsLog;
use App\Models\RiskEvent;
use App\Models\Trip;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Clickbar\Magellan\Data\Geometries\Point;
use Carbon\Carbon;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Fleet Commander',
            'email' => 'admin@fleetco.com',
            'password' => Hash::make('password'),
        ]);

        // 2. Create Drivers
        $drivers = [
            ['name' => 'Sarah Connor', 'risk_score' => 98.5],
            ['name' => 'Max Rockatansky', 'risk_score' => 74.2],
            ['name' => 'Dominic Toretto', 'risk_score' => 88.0],
            ['name' => 'Rick Deckard', 'risk_score' => 92.1],
        ];

        foreach ($drivers as $d) {
            Driver::create($d);
        }

        // 3. Create Vehicles & Initial Logs
        $vehicles = [
            ['name' => 'Scout-01', 'license_plate' => 'IF-2024-X1', 'status' => 'active', 'lat' => 19.0760, 'lng' => 72.8777],
            ['name' => 'Heavy-Cargo-04', 'license_plate' => 'IF-2024-H4', 'status' => 'active', 'lat' => 19.2183, 'lng' => 72.9781],
            ['name' => 'intercept-V8', 'license_plate' => 'IF-2024-V8', 'status' => 'idle', 'lat' => 18.9220, 'lng' => 72.8347],
            ['name' => 'Drone-Hub', 'license_plate' => 'IF-2024-DH', 'status' => 'maintenance', 'lat' => 19.1176, 'lng' => 72.9060],
        ];

        foreach ($vehicles as $vData) {
            $vehicle = Vehicle::create([
                'name' => $vData['name'],
                'license_plate' => $vData['license_plate'],
                'status' => $vData['status'],
            ]);

            // Create 5 historical logs for each to show path
            for ($i = 4; $i >= 0; $i--) {
                $latOffset = ($i * 0.005) * (rand(0, 1) ? 1 : -1);
                $lngOffset = ($i * 0.005) * (rand(0, 1) ? 1 : -1);
                
                TelematicsLog::create([
                    'vehicle_id' => $vehicle->id,
                    'driver_id' => rand(1, 4),
                    'location' => Point::makeGeodetic($vData['lat'] + $latOffset, $vData['lng'] + $lngOffset),
                    'speed' => rand(40, 115),
                    'heading' => rand(0, 360),
                    'captured_at' => Carbon::now()->subMinutes($i * 5),
                ]);
            }

            // Create some recent trips
            Trip::create([
                'vehicle_id' => $vehicle->id,
                'start_time' => Carbon::now()->subHours(2),
                'end_time' => Carbon::now()->subHours(1),
                'distance' => rand(10, 50),
                'average_speed' => rand(30, 80),
            ]);
        }

        // 4. Create some Anomaly Alerts
        RiskEvent::create([
            'driver_id' => 2,
            'vehicle_id' => 1,
            'type' => 'speeding',
            'impact_score' => 15.00,
            'details' => ['speed' => 125, 'limit' => 100],
            'occurred_at' => Carbon::now()->subMinutes(12),
        ]);

        RiskEvent::create([
            'driver_id' => 3,
            'vehicle_id' => 2,
            'type' => 'geofence_breach',
            'impact_score' => 25.00,
            'details' => ['landmark' => 'Sector 7 Area'],
            'occurred_at' => Carbon::now()->subMinutes(35),
        ]);
    }
}
