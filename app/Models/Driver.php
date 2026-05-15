<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'fleet_id',
        'name',
        'phone_number',
        'license_number',
        'risk_score',
        'clean_pings_count',
    ];

    /**
     * Get the telematics logs for the driver.
     */
    public function telematicsLogs(): HasMany
    {
        return $this->hasMany(TelematicsLog::class);
    }

    /**
     * Get the risk events for the driver.
     */
    public function riskEvents(): HasMany
    {
        return $this->hasMany(RiskEvent::class);
    }

    /**
     * Relationship to the authentication user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle currently assigned to this driver.
     */
    public function vehicle(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Vehicle::class, 'current_driver_id');
    }

    public function fleet(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Fleet::class);
    }

    /**
     * Get the duty logs for this driver.
     * Note: driver_id in duty_logs table refers to the user_id.
     */
    public function dutyLogs(): HasMany
    {
        return $this->hasMany(DutyLog::class, 'driver_id', 'user_id');
    }

}
