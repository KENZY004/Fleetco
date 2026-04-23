<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Clickbar\Magellan\Data\Geometries\Point;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;

class TelematicsLog extends Model
{
    use HasPostgisColumns;

    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'location',
        'speed',
        'heading',
        'captured_at',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            $this->postgisColumns = [];
        }
    }

    /**
     * Spatial configuration for Magellan.
     */
    protected array $postgisColumns = [
        'location' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    protected $casts = [
        'captured_at' => 'datetime',
    ];

    /**
     * Relationship to the vehicle.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Relationship to the driver.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
