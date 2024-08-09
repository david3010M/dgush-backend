<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * id
     * name
     * description
     * detailweb
     * price1
     * price2
     * score
     * image,
     * subcategory_id 1-10
     */
    protected $model = Product::class;

    public function run(): void
    {
        Product::create([
            'name' => 'Vestido Casual',
            'description' => 'Vestido casual para dama',
            'detailweb' => 'Vestido casual para dama',
            'price1' => 100,
            'price2' => 90,
            'status' => 'onsale',
            'subcategory_id' => 1,
        ]);

        Product::create([
            'name' => 'Vestido Formal',
            'description' => 'Vestido formal para dama',
            'detailweb' => 'Vestido formal para dama',
            'price1' => 200,
            'price2' => 180,
            'subcategory_id' => 2,
        ]);

        Product::create([
            'name' => 'Vestido Cóctel',
            'description' => 'Vestido cóctel para dama',
            'detailweb' => 'Vestido cóctel para dama',
            'price1' => 150,
            'price2' => 135,
            'subcategory_id' => 3,
        ]);

        Product::create([
            'name' => 'Vestido de Noche',
            'description' => 'Vestido de noche para dama',
            'detailweb' => 'Vestido de noche para dama',
            'price1' => 250,
            'price2' => 225,
            'subcategory_id' => 4,
        ]);

        Product::create([
            'name' => 'Blusa Casual',
            'description' => 'Blusa casual para dama',
            'detailweb' => 'Blusa casual para dama',
            'price1' => 50,
            'price2' => 45,
            'subcategory_id' => 4,
        ]);

        Product::create([
            'name' => 'Blusa Formal',
            'description' => 'Blusa formal para dama',
            'detailweb' => 'Blusa formal para dama',
            'price1' => 100,
            'price2' => 90,
            'status' => 'onsale',
            'subcategory_id' => 5,
        ]);

        Product::create([
            'name' => 'Blusa Deportiva',
            'description' => 'Blusa deportiva para dama',
            'detailweb' => 'Blusa deportiva para dama',
            'price1' => 75,
            'price2' => 67.5,
            'subcategory_id' => 6,
        ]);

        Product::create([
            'name' => 'Blusa de Noche',
            'description' => 'Blusa de noche para dama',
            'detailweb' => 'Blusa de noche para dama',
            'price1' => 125,
            'price2' => 112.5,
            'subcategory_id' => 7,
        ]);

        Product::create([
            'name' => 'Pantalón Casual',
            'description' => 'Pantalón casual para dama',
            'detailweb' => 'Pantalón casual para dama',
            'price1' => 75,
            'price2' => 67.5,
            'status' => 'new',
            'subcategory_id' => 8,
        ]);

        Product::create([
            'name' => 'Pantalón Formal',
            'description' => 'Pantalón formal para dama',
            'detailweb' => 'Pantalón formal para dama',
            'price1' => 100,
            'price2' => 90,
            'subcategory_id' => 9,
        ]);

        Product::create([
            'name' => 'Pantalón Deportivo',
            'description' => 'Pantalón deportivo para dama',
            'detailweb' => 'Pantalón deportivo para dama',
            'price1' => 50,
            'price2' => 45,
            'subcategory_id' => 10,
        ]);

        Product::create([
            'name' => 'Pantalón de Noche',
            'description' => 'Pantalón de noche para dama',
            'detailweb' => 'Pantalón de noche para dama',
            'price1' => 125,
            'price2' => 112.5,
            'status' => 'new',
            'subcategory_id' => 1,
        ]);

        Product::create([
            'name' => 'Falda Casual',
            'description' => 'Falda casual para dama',
            'detailweb' => 'Falda casual para dama',
            'price1' => 50,
            'price2' => 45,
            'status' => 'new',
            'subcategory_id' => 2,
        ]);

        Product::create([
            'name' => 'Falda Formal',
            'description' => 'Falda formal para dama',
            'detailweb' => 'Falda formal para dama',
            'price1' => 75,
            'price2' => 67.5,
            'subcategory_id' => 3,
        ]);

        Product::create([
            'name' => 'Falda Deportiva',
            'description' => 'Falda deportiva para dama',
            'detailweb' => 'Falda deportiva para dama',
            'price1' => 25,
            'price2' => 22.5,
            'subcategory_id' => 8,
        ]);

        Product::create([
            'name' => 'Falda de Noche',
            'description' => 'Falda de noche para dama',
            'detailweb' => 'Falda de noche para dama',
            'price1' => 100,
            'price2' => 90,
            'status' => 'onsale',
            'subcategory_id' => 9,
        ]);

        Product::create([
            'name' => 'Short Casual',
            'description' => 'Short casual para dama',
            'detailweb' => 'Short casual para dama',
            'price1' => 25,
            'price2' => 22.5,
            'subcategory_id' => 10,
        ]);

        Product::create([
            'name' => 'Short Formal',
            'description' => 'Short formal para dama',
            'detailweb' => 'Short formal para dama',
            'price1' => 50,
            'price2' => 45,
            'subcategory_id' => 1,
        ]);

        Product::create([
            'name' => 'Short Deportivo',
            'description' => 'Short deportivo para dama',
            'detailweb' => 'Short deportivo para dama',
            'price1' => 25,
            'price2' => 22.5,
            'subcategory_id' => 2,
        ]);

        Product::create([
            'name' => 'Short de Noche',
            'description' => 'Short de noche para dama',
            'detailweb' => 'Short de noche para dama',
            'price1' => 75,
            'price2' => 67.5,
            'subcategory_id' => 3,
        ]);

    }
}
