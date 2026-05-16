<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'service_type',
        'status',
        'odometer_reading',
        'cost',
        'notes',
        'service_date',
        'next_service_at_km',
        'next_service_due_date',
    ];

    protected $casts = [
        'service_date' => 'date',
        'next_service_due_date' => 'date',
        'cost' => 'decimal:2',
        'odometer_reading' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
