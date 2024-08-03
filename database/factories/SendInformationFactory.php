<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SendInformation>
 */
class SendInformationFactory extends Factory
{
    public function definition(): array
    {
        $method = $this->faker->randomElement(['delivery', 'pickup']);
        $district_id = null;
        $sede_id = null;
        if ($method === 'delivery') {
            $district_id = $this->faker->randomElement([1, 2, 3]);
        } else {
            $sede_id = $this->faker->randomElement([1, 2]);
        }

        return [
            'names' => $this->faker->name(),
            'dni' => $this->faker->randomNumber(8),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'reference' => $this->faker->sentence(),
            'comment' => $this->faker->sentence(),
            'method' => $method,
            'district_id' => $district_id,
            'sede_id' => $sede_id,
            'order_id' => 1,
        ];
    }
}
