<?php

namespace App\Repositories;

use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;

class TripRepository
{
    /**
     * Get recent trips for a specific vehicle or all vehicles.
     */
    public function getRecent(int $limit = 10): Collection
    {
        return Trip::with('vehicle')
            ->latest('start_time')
            ->limit($limit)
            ->get();
    }
}
