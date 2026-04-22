<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'name',
        'license_plate',
        'status',
    ];

    /**
     * Get the telematics logs for the vehicle.
     */
    public function telematicsLogs(): HasMany
    {
        return $this->hasMany(TelematicsLog::class);
    }

    /**
     * Get the risk events for the vehicle.
     */
    public function riskEvents(): HasMany
    {
        return $this->hasMany(RiskEvent::class);
    }
}
