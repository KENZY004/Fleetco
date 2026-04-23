<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    protected $fillable = [
        'name',
        'license_plate',
        'status',
        'current_driver_id',
    ];

    /**
     * Get the current driver of the vehicle.
     */
    public function driver(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Driver::class, 'current_driver_id');
    }

    /**
     * Get the telematics logs for the vehicle.
     */
    public function telematicsLogs(): HasMany
    {
        return $this->hasMany(TelematicsLog::class);
    }

    /**
     * Get the latest telematics log for the vehicle.
     */
    public function latestTelematics(): HasOne
    {
        return $this->hasOne(TelematicsLog::class)->latestOfMany('captured_at');
    }

    /**
     * Get the risk events for the vehicle.
     */
    public function riskEvents(): HasMany
    {
        return $this->hasMany(RiskEvent::class);
    }

    /**
     * Update vehicle status based on speed.
     */
    public function updateStatusFromTelemetry(float $speed): void
    {
        $newStatus = $speed > 0 ? 'active' : 'idle';
        
        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
    }
}
