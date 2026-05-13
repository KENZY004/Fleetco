<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverInvitation extends Model
{
    protected $fillable = [
        'fleet_id',
        'invited_by',
        'email',
        'name',
        'license_number',
        'plate_number',
        'token',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    public function fleet()
    {
        return $this->belongsTo(Fleet::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())->whereNull('accepted_at');
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function isUsed()
    {
        return !is_null($this->accepted_at);
    }
}
