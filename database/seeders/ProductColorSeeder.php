<?php

namespace Database\Seeders;

use App\Models\ProductColor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductColorSeeder extends Seeder
{
    protected $model = ProductColor::class;

    public function run(): void
    {
        ProductColor::create([
            'product_id' => 1,
            'color_id' => 1,
        ]);

        ProductColor::create([
            'product_id' => 1,
            'color_id' => 2,
        ]);

        ProductColor::create([
            'product_id' => 1,
            'color_id' => 3,
        ]);

        ProductColor::create([
            'product_id' => 2,
            'color_id' => 4,
        ]);

        ProductColor::create([
            'product_id' => 2,
            'color_id' => 5,
        ]);

        ProductColor::create([
            'product_id' => 2,
            'color_id' => 6,
        ]);

        ProductColor::create([
            'product_id' => 3,
            'color_id' => 1,
        ]);

        ProductColor::create([
            'product_id' => 3,
            'color_id' => 2,
        ]);

        ProductColor::create([
            'product_id' => 3,
            'color_id' => 3,
        ]);

        ProductColor::create([
            'product_id' => 4,
            'color_id' => 4,
        ]);

        ProductColor::create([
            'product_id' => 4,
            'color_id' => 5,
        ]);

        ProductColor::create([
            'product_id' => 4,
            'color_id' => 6,
        ]);

        ProductColor::create([
            'product_id' => 5,
            'color_id' => 1,
        ]);

        ProductColor::create([
            'product_id' => 5,
            'color_id' => 2,
        ]);

        ProductColor::create([
            'product_id' => 5,
            'color_id' => 3,
        ]);

        ProductColor::create([
            'product_id' => 6,
            'color_id' => 4,
        ]);

        ProductColor::create([
            'product_id' => 6,
            'color_id' => 5,
        ]);

        ProductColor::create([
            'product_id' => 6,
            'color_id' => 6,
        ]);
    }
}
