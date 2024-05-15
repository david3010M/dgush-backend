<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSizeSeeder extends Seeder
{
    protected $model = ProductSize::class;

    public function run(): void
    {

//        CREATE PRODUCT SIZE FROM A RANDOM NUMBER SINCE 4 TO 7 AS MAXIMUM
        Product::all()->each(function (Product $product) {
            $random = rand(3, 6);
            for ($i = 1; $i <= $random; $i++) {
                ProductSize::create([
                    'product_id' => $product->id,
                    'size_id' => $i,
                ]);
            }
        });
    }
}
