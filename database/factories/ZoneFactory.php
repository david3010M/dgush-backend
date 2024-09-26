<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sendCost' => $this->faker->randomFloat(0, 10, 20),
        ];
    }
}
