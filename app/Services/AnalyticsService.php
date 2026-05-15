<?php

namespace App\Services;

use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\RiskEvent;
use App\Models\TelematicsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get all dashboard stats for the current fleet state.
     */
    public function getDashboardStats(): array
    {
        return [
            'totalVehicles' => $this->getTotalVehiclesCount(),
            'activeVehicles' => $this->getActiveVehiclesCount(),
            'idleVehicles' => $this->getIdleVehiclesCount(),
            'totalDistance' => $this->getTotalFleetDistance(),
            'activeAnomalies' => $this->getActiveAnomaliesCount(),
        ];
    }

    /**
     * Count total registered vehicles.
     */
    public function getTotalVehiclesCount(): int
    {
        return Vehicle::count();
    }

    /**
     * Count vehicles that have sent a signal in the last 5 minutes.
     */
    public function getActiveVehiclesCount(): int
    {
        // A vehicle is active if it has a driver who is currently ON DUTY
        return Vehicle::whereHas('driver.dutyLogs', function ($query) {
            $query->whereNull('ended_at')->where('status', 'on_duty');
        })->count();
    }

    /**
     * Count vehicles that are NOT active.
     */
    public function getIdleVehiclesCount(): int
    {
        return $this->getTotalVehiclesCount() - $this->getActiveVehiclesCount();
    }

    /**
     * Calculate the total distance covered by the entire fleet.
     */
    public function getTotalFleetDistance(): float
    {
        return (float) Trip::sum('distance');
    }

    /**
     * Count critical alerts/anomalies in the last 24 hours.
     */
    public function getActiveAnomaliesCount(): int
    {
        return RiskEvent::where('occurred_at', '>=', Carbon::now()->subDay())
            ->whereIn('type', ['speeding', 'geofence_breach'])
            ->count();
    }
}
