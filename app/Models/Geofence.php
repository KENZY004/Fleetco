<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;

class Geofence extends Model
{
    use HasPostgisColumns;

    protected $fillable = ['name', 'type', 'description', 'area', 'status'];

    protected array $postgisColumns = [
        'area' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];
}
