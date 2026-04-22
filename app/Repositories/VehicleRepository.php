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
        return Vehicle::with(['telematicsLogs' => function ($query) {
            $query->latest('captured_at')->limit(1);
        }])->get();
    }

    /**
     * Find a specific vehicle by ID.
     */
    public function find(string $id): ?Vehicle
    {
        return Vehicle::with(['telematicsLogs' => function ($query) {
            $query->latest('captured_at')->limit(1);
        }])->find($id);
    }
}
