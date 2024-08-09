<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SizeGuide;
use Illuminate\Database\Seeder;

class SizeGuideSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            SizeGuide::factory()->create([
                'name' => 'Size Guide - ' . $product->id,
                'route' => 'https://dgush-storage.sfo3.digitaloceanspaces.com/GuiaTallas/faldas.jpg',
                'product_id' => $product->id
            ]);
        }

    }
}
