<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ProductDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countProductDetails = ProductDetails::count();
        $productDetailId = $this->faker->numberBetween(1, $countProductDetails);
        $productDetail = ProductDetails::find($productDetailId);

        return [
            'quantity' => 1,
            'product_detail_id' => $productDetailId,
            'price' => $productDetail->product->price1,
            'order_id' => 1
        ];
    }
}
