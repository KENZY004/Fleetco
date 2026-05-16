<?php

namespace App\Repositories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Collection;

class VehicleRepository
{
    /**
     * Get all vehicles with their latest telematics logs.
     */
    public function getAllWithStatus(): Collection
    {
        $user = auth()->user();
        $fleetId = $user?->fleet_id;
        
        $vehicles = Vehicle::with([
                'driver', 
                'latestTelematics', 
                'activeRoute',
                'driver.dutyLogs' => function($q) {
                    $q->whereNull('ended_at')->where('status', 'on_duty');
                }
            ])
            ->withCount('telematicsLogs')
            ->when($fleetId, function($q) use ($fleetId) {
                return $q->where('fleet_id', $fleetId);
            })
            ->get();
            
        $timeout = \Carbon\Carbon::now()->subSeconds(45);

        $vehicles->each(function ($vehicle) use ($timeout) {
            // Check if assigned driver has an active ON DUTY log from eager load
            $isOnDuty = $vehicle->driver && $vehicle->driver->dutyLogs->isNotEmpty();

            if ($isOnDuty) {
                $vehicle->status = 'active';
            } elseif ($vehicle->status !== 'offline' && (!$vehicle->latestTelematics || $vehicle->latestTelematics->captured_at->lt($timeout))) {
                $vehicle->status = 'offline';
            }
        });

        return $vehicles;
    }

    /**
     * Find a specific vehicle by ID.
     */
    public function find(int $id): ?Vehicle
    {
        return Vehicle::with(['driver', 'latestTelematics', 'activeRoute'])->find($id);
    }

    public function create(array $data): Vehicle
    {
        return Vehicle::create([
            'fleet_id'      => auth()->user()?->fleet_id,
            'name'          => $data['name'],
            'license_plate' => strtoupper($data['license_plate']),
            'status'        => $data['status'],
        ]);
    }

    public function update(Vehicle $vehicle, array $data): bool
    {
        if (isset($data['license_plate'])) {
            $data['license_plate'] = strtoupper($data['license_plate']);
        }
        return $vehicle->update($data);
    }

    public function delete(Vehicle $vehicle): ?bool
    {
        return $vehicle->delete();
    }

    public function unassignDriverFromOthers(int $driverId, int $excludeVehicleId): void
    {
        Vehicle::where('current_driver_id', $driverId)
            ->where('id', '!=', $excludeVehicleId)
            ->update(['current_driver_id' => null]);
    }
}
