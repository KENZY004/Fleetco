<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Services\TelemetryProcessor;

class SimulateFleet extends Command
{
    protected $signature = 'fleet:simulate {--interval=5 : Interval in seconds between updates} {--count=10 : Total updates to send}';
    protected $description = 'Simulate fleet movement for all vehicles except MOBILE-01';

    public function handle(TelemetryProcessor $processor)
    {
        $vehicles = Vehicle::where('license_plate', '!=', 'MOBILE-01')->get();
        $count = $this->option('count');
        $interval = $this->option('interval');

        $this->info("Starting simulation for " . $vehicles->count() . " vehicles...");

        for ($i = 0; $i < $count; $i++) {
            $this->comment("Update cycle " . ($i + 1) . "/$count...");

            foreach ($vehicles as $vehicle) {
                // Get last known location
                $lastLog = $vehicle->latestTelematics;
                
                if ($lastLog) {
                    $lat = $lastLog->location->getLatitude();
                    $lng = $lastLog->location->getLongitude();
                } else {
                    // Default to Mumbai center if no logs
                    $lat = 19.0760;
                    $lng = 72.8777;
                }

                // Random small movement (approx 10-50 meters)
                $newLat = $lat + (rand(-10, 10) / 10000);
                $newLng = $lng + (rand(-10, 10) / 10000);

                $processor->process($vehicle, [
                    'lat' => $newLat,
                    'lng' => $newLng,
                    'speed' => rand(20, 60),
                    'heading' => rand(0, 360),
                ]);
            }

            if ($i < $count - 1) {
                sleep($interval);
            }
        }

        $this->info("Simulation complete.");
    }
}
