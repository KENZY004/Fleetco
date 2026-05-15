<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverTelemetry extends Model
{
    use HasFactory;

    protected $table = 'driver_telemetry';

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'latitude',
        'longitude',
        'speed_kmh',
        'heading',
        'captured_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
