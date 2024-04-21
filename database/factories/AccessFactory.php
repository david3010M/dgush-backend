<?php

namespace Database\Factories;

use App\Models\Access;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Access>
 */
class AccessFactory extends Factory
{

    protected $model = Access::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'typeuser_id' => $this->faker->numberBetween(1, 10),
            'optionmenu_id' => $this->faker->numberBetween(1, 10),
        ];
    }
}
