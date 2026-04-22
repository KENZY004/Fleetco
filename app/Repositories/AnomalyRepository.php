<?php

namespace App\Repositories;

use App\Models\RiskEvent;
use Illuminate\Database\Eloquent\Collection;

class AnomalyRepository
{
    /**
     * Get the latest anomalies with driver and vehicle context.
     */
    public function getRecent(int $limit = 5): Collection
    {
        return RiskEvent::with(['driver', 'vehicle'])
            ->latest('occurred_at')
            ->limit($limit)
            ->get();
    }
}
