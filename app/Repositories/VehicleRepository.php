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
        $query = Vehicle::with(['driver', 'latestTelematics', 'activeRoute']);
        
        if (auth()->check() && auth()->user()->fleet_id) {
            $query->where('fleet_id', auth()->user()->fleet_id);
        }

        $vehicles = $query->get();
        $timeout = \Carbon\Carbon::now()->subSeconds(45);

        // Map through vehicles and force status based on Duty + Telemetry
        $vehicles->each(function ($vehicle) use ($timeout) {
            // Check if assigned driver is currently ON DUTY
            $isOnDuty = $vehicle->driver && $vehicle->driver->dutyLogs()
                ->whereNull('ended_at')
                ->where('status', 'on_duty')
                ->exists();

            if ($isOnDuty) {
                // If on duty, they are ALWAYS active
                $vehicle->status = 'active';
            } elseif ($vehicle->status !== 'offline' && (!$vehicle->latestTelematics || $vehicle->latestTelematics->captured_at->lt($timeout))) {
                // Otherwise, fall back to telemetry timeout
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
