<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone_number' => fake()->phoneNumber(),
            'license_number' => strtoupper(fake()->bothify('??-########')),
            'risk_score' => fake()->randomFloat(2, 0, 100),
            'clean_pings_count' => 0,
        ];
    }
}
