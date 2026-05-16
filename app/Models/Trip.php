<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trip extends Model
{
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'start_time',
        'end_time',
        'distance',
        'average_speed',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'distance' => 'float',
        'average_speed' => 'float',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function getDurationAttribute()
    {
        $end = $this->end_time ?? now();
        return $this->start_time->diff($end)->format('%h h %i m');
    }
}
