<?php

namespace App\Repositories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Collection;

class DriverRepository
{
    public function all(): Collection
    {
        return Driver::with(['user', 'riskEvents', 'vehicle'])->withCount('telematicsLogs')->get();
    }

    public function find(int $id): ?Driver
    {
        return Driver::find($id);
    }

    public function getUnassigned(): Collection
    {
        return \App\Models\Driver::whereDoesntHave('vehicle')->get();
    }

    public function getUnlinkedUsers(): Collection
    {
        return \App\Models\User::whereDoesntHave('driver')->where('role', '!=', 'admin')->get();
    }

    public function create(array $data): Driver
    {
        return Driver::create([
            'name' => $data['name'],
            'phone_number' => $data['phone_number'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'risk_score' => 100.00,
        ]);
    }

    public function update(Driver $driver, array $data): bool
    {
        return $driver->update($data);
    }

    public function delete(Driver $driver): ?bool
    {
        return $driver->delete();
    }
}
