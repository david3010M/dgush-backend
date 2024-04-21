<?php

namespace Database\Factories;

use App\Models\GroupMenu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GroupMenu>
 */
class GroupMenuFactory extends Factory
{
    protected $model = GroupMenu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'icon' => $this->faker->sentence(2),
            'order' => $this->faker->randomNumber(),
        ];
    }
}
