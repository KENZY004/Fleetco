<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' ' . fake()->word(),
            'license_plate' => strtoupper(fake()->bothify('??-##-??-####')),
            'status' => fake()->randomElement(['active', 'idle', 'maintenance', 'offline']),
            'telemetry_token' => 'FLT-' . strtoupper(Str::random(8)),
        ];
    }
}
