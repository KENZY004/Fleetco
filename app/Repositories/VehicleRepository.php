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
        return Vehicle::with(['driver', 'latestTelematics'])->get();
    }

    /**
     * Find a specific vehicle by ID.
     */
    public function find(int $id): ?Vehicle
    {
        return Vehicle::with(['driver', 'latestTelematics'])->find($id);
    }

    public function create(array $data): Vehicle
    {
        return Vehicle::create([
            'name' => $data['name'],
            'license_plate' => strtoupper($data['license_plate']),
            'status' => $data['status'],
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
