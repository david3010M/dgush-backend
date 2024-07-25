<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductDetailsSeeder extends Seeder
{
    protected $model = ProductDetails::class;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $colors = Color::all();
        $sizes = Size::all();

        foreach ($products as $product) {
            // Selecciona aleatoriamente 4 colores para cada producto
            $selectedColors = $colors->random(4);

            foreach ($selectedColors as $color) {
                // Selecciona aleatoriamente una cantidad de tamaños para cada color (puede ser de 1 a todos los tamaños disponibles)
                $selectedSizes = $sizes->random(rand(1, $sizes->count()));

                foreach ($selectedSizes as $size) {
                    // Crea un registro en productDetails con stock de 10
                    ProductDetails::create([
                        'stock' => 100,
                        'product_id' => $product->id,
                        'color_id' => $color->id,
                        'size_id' => $size->id,
                    ]);
                }
            }
        }
    }
}
