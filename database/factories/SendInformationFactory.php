<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SendInformation>
 */
class SendInformationFactory extends Factory
{
//    'names',
//        'dni',
//        'email',
//        'phone',
//        'address',
//        'reference',
//        'comment',
//        'method',
//        'district_id',
//        'order_id',
    public function definition(): array
    {
        return [
            'names' => $this->faker->name(),
            'dni' => $this->faker->randomNumber(8),
            'email' => $this->faker->email(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'reference' => $this->faker->sentence(),
            'comment' => $this->faker->sentence(),
            'method' => $this->faker->randomElement(['delivery', 'recoger en tienda']),
            'district_id' => 1,
            'order_id' => 1
        ];
    }
}
