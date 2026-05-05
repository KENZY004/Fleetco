<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskEvent extends Model
{
    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'telematics_log_id',
        'type',
        'impact_score',
        'details',
        'occurred_at',
        'resolved_at',
        'resolution_note',
    ];

    /**
     * Casting.
     */
    protected $casts = [
        'details' => 'array',
        'occurred_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    /**
     * Relationship to the driver.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Relationship to the vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relationship to the specific log that triggered the event.
     */
    public function telematicsLog(): BelongsTo
    {
        return $this->belongsTo(TelematicsLog::class);
    }
}
