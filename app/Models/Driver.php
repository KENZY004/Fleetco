<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'risk_score',
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
}
