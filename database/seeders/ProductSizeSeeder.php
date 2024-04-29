<?php

namespace Database\Seeders;

use App\Models\ProductSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSizeSeeder extends Seeder
{
    protected $model = ProductSize::class;

    public function run(): void
    {
        ProductSize::create([
            'product_id' => 1,
            'size_id' => 1,
        ]);

        ProductSize::create([
            'product_id' => 1,
            'size_id' => 2,
        ]);

        ProductSize::create([
            'product_id' => 1,
            'size_id' => 3,
        ]);

        ProductSize::create([
            'product_id' => 2,
            'size_id' => 4,
        ]);

        ProductSize::create([
            'product_id' => 2,
            'size_id' => 5,
        ]);

        ProductSize::create([
            'product_id' => 2,
            'size_id' => 6,
        ]);

        ProductSize::create([
            'product_id' => 3,
            'size_id' => 1,
        ]);

        ProductSize::create([
            'product_id' => 3,
            'size_id' => 2,
        ]);

        ProductSize::create([
            'product_id' => 3,
            'size_id' => 3,
        ]);
    }
}
