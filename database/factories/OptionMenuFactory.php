<?php

namespace Database\Factories;

use App\Models\OptionMenu;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OptionMenu>
 */
class OptionMenuFactory extends Factory
{

    protected $model = OptionMenu::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(2),
            'route' => $this->faker->sentence(2),
            'icon' => $this->faker->sentence(2),
            'groupmenu_id' => 1,
            'order' => $this->faker->randomNumber(),
        ];
    }
}
