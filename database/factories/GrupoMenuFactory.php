<?php

namespace Database\Factories;

use App\Models\GrupoMenu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GrupoMenu>
 */
class GrupoMenuFactory extends Factory
{
    protected $model = GrupoMenu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'icon' => $this->faker->word,
            'order' => $this->faker->randomNumber(),
        ];
    }
}
