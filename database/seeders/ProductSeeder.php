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
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1591369822096-ffd140ec948f',
            'subcategory_id' => 1,
        ]);

        Product::create([
            'name' => 'Vestido Formal',
            'description' => 'Vestido formal para dama',
            'detailweb' => 'Vestido formal para dama',
            'price1' => 200,
            'price2' => 180,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1612722432474-b971cdcea546',
            'subcategory_id' => 2,
        ]);

        Product::create([
            'name' => 'Vestido Cóctel',
            'description' => 'Vestido cóctel para dama',
            'detailweb' => 'Vestido cóctel para dama',
            'price1' => 150,
            'price2' => 135,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1595777457583-95e059d581b8',
            'subcategory_id' => 3,
        ]);

        Product::create([
            'name' => 'Vestido de Noche',
            'description' => 'Vestido de noche para dama',
            'detailweb' => 'Vestido de noche para dama',
            'price1' => 250,
            'price2' => 225,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1612872217406-ed2471abf0a0',
            'subcategory_id' => 4,
        ]);

        Product::create([
            'name' => 'Blusa Casual',
            'description' => 'Blusa casual para dama',
            'detailweb' => 'Blusa casual para dama',
            'price1' => 50,
            'price2' => 45,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 5,
        ]);

        Product::create([
            'name' => 'Blusa Formal',
            'description' => 'Blusa formal para dama',
            'detailweb' => 'Blusa formal para dama',
            'price1' => 100,
            'price2' => 90,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1608234807905-4466023792f5',
            'subcategory_id' => 6,
        ]);

        Product::create([
            'name' => 'Blusa Deportiva',
            'description' => 'Blusa deportiva para dama',
            'detailweb' => 'Blusa deportiva para dama',
            'price1' => 75,
            'price2' => 67.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1578587018452-892bacefd3f2',
            'subcategory_id' => 7,
        ]);

        Product::create([
            'name' => 'Blusa de Noche',
            'description' => 'Blusa de noche para dama',
            'detailweb' => 'Blusa de noche para dama',
            'price1' => 125,
            'price2' => 112.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa',
            'subcategory_id' => 8,
        ]);

        Product::create([
            'name' => 'Pantalón Casual',
            'description' => 'Pantalón casual para dama',
            'detailweb' => 'Pantalón casual para dama',
            'price1' => 75,
            'price2' => 67.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1',
            'subcategory_id' => 9,
        ]);

        Product::create([
            'name' => 'Pantalón Formal',
            'description' => 'Pantalón formal para dama',
            'detailweb' => 'Pantalón formal para dama',
            'price1' => 100,
            'price2' => 90,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1699205016746-8aa2e6e48453',
            'subcategory_id' => 10,
        ]);

        Product::create([
            'name' => 'Pantalón Deportivo',
            'description' => 'Pantalón deportivo para dama',
            'detailweb' => 'Pantalón deportivo para dama',
            'price1' => 50,
            'price2' => 45,
            'score' => 5,
            'image' => '',
            'subcategory_id' => 5,
        ]);

        Product::create([
            'name' => 'Pantalón de Noche',
            'description' => 'Pantalón de noche para dama',
            'detailweb' => 'Pantalón de noche para dama',
            'price1' => 125,
            'price2' => 112.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1699205016746-8aa2e6e48453',
            'subcategory_id' => 6,
        ]);

        Product::create([
            'name' => 'Falda Casual',
            'description' => 'Falda casual para dama',
            'detailweb' => 'Falda casual para dama',
            'price1' => 50,
            'price2' => 45,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1592301933927-35b597393c0a',
            'subcategory_id' => 6,
        ]);

        Product::create([
            'name' => 'Falda Formal',
            'description' => 'Falda formal para dama',
            'detailweb' => 'Falda formal para dama',
            'price1' => 75,
            'price2' => 67.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1592301933927-35b597393c0a',
            'subcategory_id' => 7,
        ]);

        Product::create([
            'name' => 'Falda Deportiva',
            'description' => 'Falda deportiva para dama',
            'detailweb' => 'Falda deportiva para dama',
            'price1' => 25,
            'price2' => 22.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1592301933927-35b597393c0a',
            'subcategory_id' => 8,
        ]);

        Product::create([
            'name' => 'Falda de Noche',
            'description' => 'Falda de noche para dama',
            'detailweb' => 'Falda de noche para dama',
            'price1' => 100,
            'price2' => 90,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1592301933927-35b597393c0a',
            'subcategory_id' => 9,
        ]);

        Product::create([
            'name' => 'Short Casual',
            'description' => 'Short casual para dama',
            'detailweb' => 'Short casual para dama',
            'price1' => 25,
            'price2' => 22.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1657823501874-58b8d8a0fb23',
            'subcategory_id' => 10,
        ]);

        Product::create([
            'name' => 'Short Formal',
            'description' => 'Short formal para dama',
            'detailweb' => 'Short formal para dama',
            'price1' => 50,
            'price2' => 45,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1657823501874-58b8d8a0fb23',
            'subcategory_id' => 1,
        ]);

        Product::create([
            'name' => 'Short Deportivo',
            'description' => 'Short deportivo para dama',
            'detailweb' => 'Short deportivo para dama',
            'price1' => 25,
            'price2' => 22.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1657823501874-58b8d8a0fb23',
            'subcategory_id' => 2,
        ]);

        Product::create([
            'name' => 'Short de Noche',
            'description' => 'Short de noche para dama',
            'detailweb' => 'Short de noche para dama',
            'price1' => 75,
            'price2' => 67.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1657823501874-58b8d8a0fb23',
            'subcategory_id' => 3,
        ]);

        Product::create([
            'name' => 'Blusa Casual',
            'description' => 'Blusa casual para dama',
            'detailweb' => 'Blusa casual para dama',
            'price1' => 50,
            'price2' => 45,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 4,
        ]);

        Product::create([
            'name' => 'Blusa Formal',
            'description' => 'Blusa formal para dama',
            'detailweb' => 'Blusa formal para dama',
            'price1' => 100,
            'price2' => 90,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 5,
        ]);

        Product::create([
            'name' => 'Blusa Deportiva',
            'description' => 'Blusa deportiva para dama',
            'detailweb' => 'Blusa deportiva para dama',
            'price1' => 75,
            'price2' => 67.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 6,
        ]);

        Product::create([
            'name' => 'Blusa de Noche',
            'description' => 'Blusa de noche para dama',
            'detailweb' => 'Blusa de noche para dama',
            'price1' => 125,
            'price2' => 112.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 7,
        ]);

        Product::create([
            'name' => 'Pantalón Casual',
            'description' => 'Pantalón casual para dama',
            'detailweb' => 'Pantalón casual para dama',
            'price1' => 75,
            'price2' => 67.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 8,
        ]);

        Product::create([
            'name' => 'Pantalón Formal',
            'description' => 'Pantalón formal para dama',
            'detailweb' => 'Pantalón formal para dama',
            'price1' => 100,
            'price2' => 90,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 9,
        ]);

        Product::create([
            'name' => 'Pantalón Deportivo',
            'description' => 'Pantalón deportivo para dama',
            'detailweb' => 'Pantalón deportivo para dama',
            'price1' => 50,
            'price2' => 45,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 10,
        ]);

        Product::create([
            'name' => 'Pantalón de Noche',
            'description' => 'Pantalón de noche para dama',
            'detailweb' => 'Pantalón de noche para dama',
            'price1' => 125,
            'price2' => 112.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 1,
        ]);

        Product::create([
            'name' => 'Falda Casual',
            'description' => 'Falda casual para dama',
            'detailweb' => 'Falda casual para dama',
            'price1' => 50,
            'price2' => 45,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 2,
        ]);

        Product::create([
            'name' => 'Falda Formal',
            'description' => 'Falda formal para dama',
            'detailweb' => 'Falda formal para dama',
            'price1' => 75,
            'price2' => 67.5,
            'score' => 5,
            'image' => 'https://images.unsplash.com/photo-1549062572-544a64fb0c56',
            'subcategory_id' => 3,
        ]);


    }
}
