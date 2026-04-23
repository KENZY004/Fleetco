<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Clickbar\Magellan\Data\Geometries\Polygon;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;

class Landmark extends Model
{
    use HasPostgisColumns;

    protected $fillable = [
        'name',
        'type',
        'area',
        'metadata',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            $this->postgisColumns = [];
        }
    }

    protected array $postgisColumns = [
        'area' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
