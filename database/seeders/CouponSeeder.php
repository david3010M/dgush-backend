<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    protected $model = Coupon::class;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['name' => 'Primera Compra', 'description' => 'Descuento del 10% en tu primera compra', 'code' => 'PRIMERA10', 'type' => 'percentage', 'active' => true, 'value' => 10, 'expires_at' => '2024-12-31'],
            ['name' => 'Envío Gratis', 'description' => 'Envío gratis en tu compra', 'code' => 'FREESEND', 'type' => 'discount', 'active' => true, 'value' => 10, 'expires_at' => '2024-12-31'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }

    }
}
