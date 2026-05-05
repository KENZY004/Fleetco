<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'license_plate',
        'status',
        'tracking_hash',
        'current_odometer',
        'next_service_at'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vehicle) {
            $vehicle->tracking_hash = \Illuminate\Support\Str::random(16);
        });
    }

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

    /**
     * Get the issues for the vehicle.
     */
    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }
}
