<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $fillable = [
        'vehicle_id',
        'title',
        'description',
        'status',
        'priority',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
