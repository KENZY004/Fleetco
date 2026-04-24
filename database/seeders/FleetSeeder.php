<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FleetSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        User::create([
            'first_name' => 'Fleet',
            'last_name' => 'Commander',
            'name' => 'Fleet Commander',
            'email' => 'admin@fleetco.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // 2. Create Base Drivers
        $drivers = [
            ['name' => 'Sarah Connor', 'risk_score' => 100.0],
            ['name' => 'Max Rockatansky', 'risk_score' => 100.0],
        ];

        foreach ($drivers as $d) {
            Driver::create($d);
        }

        // NOTE: No vehicles are pre-created.
        // Vehicles will be automatically created in the database 
        // the moment a driver starts transmitting from the tracking page.
    }
}
