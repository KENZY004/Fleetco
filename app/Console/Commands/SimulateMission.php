<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Models\TelematicsLog;
use Clickbar\Magellan\Data\Geometries\Point;
use Carbon\Carbon;

class SimulateMission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fleet:simulate {vehicle_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate a live mission for a vehicle, sending GPS pings every 2 seconds.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vehicleId = $this->argument('vehicle_id');
        $vehicle = Vehicle::find($vehicleId);

        if (!$vehicle) {
            $this->error("Vehicle not found!");
            return 1;
        }

        $this->info("Initiating Live Simulation for: {$vehicle->name}");
        $this->info("Press Ctrl+C to stop the simulation.");

        // A sample route (e.g., from one city to another, or moving across a map)
        // We'll interpolate between these points
        $route = [
            ['lat' => 19.0760, 'lng' => 72.8777], // Mumbai
            ['lat' => 19.1136, 'lng' => 72.8697], 
            ['lat' => 19.1350, 'lng' => 72.8700],
            ['lat' => 19.1550, 'lng' => 72.8900],
            ['lat' => 19.1750, 'lng' => 72.9200], // Moving towards Thane
            ['lat' => 19.2000, 'lng' => 72.9500],
            ['lat' => 19.2500, 'lng' => 72.9800],
        ];

        // Ensure we have some base odometer
        if ($vehicle->current_odometer == 0) {
            $vehicle->update(['current_odometer' => 1000]);
        }

        // Ensure we have a next_service_at
        if (!$vehicle->next_service_at) {
            $vehicle->update(['next_service_at' => $vehicle->current_odometer + 50]); // Service due in 50 km
        }

        $currentPoint = $route[0];
        $targetIndex = 1;
        
        while (true) {
            if ($targetIndex >= count($route)) {
                $targetIndex = 0; // Loop back to the start!
                $currentPoint = $route[0];
            }

            $targetPoint = $route[$targetIndex];
            
            // Move 5% towards the target every 2 seconds
            $currentPoint['lat'] = $currentPoint['lat'] + ($targetPoint['lat'] - $currentPoint['lat']) * 0.05;
            $currentPoint['lng'] = $currentPoint['lng'] + ($targetPoint['lng'] - $currentPoint['lng']) * 0.05;

            // If we are very close to the target, move to next target
            if (abs($currentPoint['lat'] - $targetPoint['lat']) < 0.001 && abs($currentPoint['lng'] - $targetPoint['lng']) < 0.001) {
                $targetIndex++;
            }

            // Create Telematics Log directly
            $log = TelematicsLog::create([
                'vehicle_id' => $vehicle->id,
                'driver_id' => $vehicle->current_driver_id ?? 1,
                'location' => Point::makeGeodetic($currentPoint['lat'], $currentPoint['lng']),
                'speed' => rand(40, 65), // Simulated speed
                'heading' => rand(0, 360),
                'captured_at' => Carbon::now(),
            ]);

            // Broadcast Event
            event(new \App\Events\TelematicsReceived($log));

            // Run Telematics Controller Logic (Normally done via API, we just duplicate the distance calc for the simulator)
            $lastLog = TelematicsLog::where('vehicle_id', $vehicle->id)
                                ->orderBy('captured_at', 'desc')
                                ->skip(1)
                                ->first();

            $distanceKm = 0;
            if ($lastLog) {
                $lat1 = $lastLog->location->getY();
                $lon1 = $lastLog->location->getX();
                $lat2 = $currentPoint['lat'];
                $lon2 = $currentPoint['lng'];

                $theta = $lon1 - $lon2;
                $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
                $dist = acos($dist);
                $dist = rad2deg($dist);
                $miles = $dist * 60 * 1.1515;
                $distanceKm = $miles * 1.609344;
            }

            $newOdometer = $vehicle->current_odometer + (is_nan($distanceKm) ? 0 : $distanceKm);
            
            $vehicle->update([
                'status' => 'active',
                'current_odometer' => $newOdometer
            ]);

            $this->line("Ping sent: [{$currentPoint['lat']}, {$currentPoint['lng']}] | Speed: {$log->speed} | Odo: " . round($newOdometer, 2) . " km");

            // Sleep 2 seconds before next ping
            sleep(2);
        }

        return 0;
    }
}
