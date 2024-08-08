<?php

namespace Database\Factories;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 1000);
        $sendCost = $this->faker->randomFloat(2, 10, 30);
        $discount = $this->faker->randomFloat(2, 0, $subtotal - $sendCost);
        $total = $subtotal - $discount + $sendCost;

        $countUsers = User::count();
        $countCoupons = Coupon::count();

        $date = $this->faker->dateTimeThisYear();
        $deliveryDate = $date->modify('+4 days');
        $shippingDate = $date->modify('+2 days');

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'sendCost' => $sendCost,
            'total' => $total,
            'quantity' => 2,
            'date' => $date,
            'deliveryDate' => $deliveryDate,
            'shippingDate' => $shippingDate,
            'status' => $this->faker->randomElement(['verificado', 'confirmado', 'enviado', 'entregado', 'cancelado']),
            'user_id' => $this->faker->numberBetween(1, $countUsers),
            'coupon_id' => $this->faker->numberBetween(1, $countCoupons)
        ];
    }
}
