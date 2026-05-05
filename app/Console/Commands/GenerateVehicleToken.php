<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;

class GenerateVehicleToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:token {license_plate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a Sanctum API token for a specific vehicle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $licensePlate = $this->argument('license_plate');
        $vehicle = Vehicle::where('license_plate', $licensePlate)->first();

        if (!$vehicle) {
            $this->error("Vehicle with license plate [{$licensePlate}] not found.");
            return 1;
        }

        $token = $vehicle->createToken('telematics-device')->plainTextToken;

        $this->info("Successfully generated token for vehicle: {$vehicle->name}");
        $this->line("");
        $this->warn($token);
        $this->line("");
        $this->info("Copy the orange token above and use it in your API requests.");
    }
}
