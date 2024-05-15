<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductColorSeeder extends Seeder
{
    protected $model = ProductColor::class;

    public function run(): void
    {
//        CREATE PRODUCT COLOR FROM A RANDOM NUMBER SINCE 4 TO 7 AS MAXIMUM
        Product::all()->each(function (Product $product) {
            $random = rand(4, 7);
            for ($i = 1; $i <= $random; $i++) {
                ProductColor::create([
                    'product_id' => $product->id,
                    'color_id' => $i,
                ]);
            }
        });
    }
}
