<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestVehicle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fleet:test-vehicle';
    protected $description = 'Create a test vehicle and output its telemetry token';

    public function handle()
    {
        $vehicle = \App\Models\Vehicle::updateOrCreate(
            ['license_plate' => 'TEST-001'],
            ['name' => 'Interceptor Test Unit', 'status' => 'idle']
        );

        $this->info('Test Vehicle Ready!');
        $this->info('License Plate: ' . $vehicle->license_plate);
        $this->info('Telemetry Token: ' . $vehicle->telemetry_token);
        $this->info('Use this token on the /track-me page.');
    }
}
