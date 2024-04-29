<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategorySeeder extends Seeder
{
    protected $model = Subcategory::class;

    public function run(): void
    {
        // CREATE SUBCATEGORIES FOR "Vestidos"
        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 1,
        ]);

        Subcategory::create([
            'name' => 'Formales',
            'order' => 2,
            'category_id' => 1,
        ]);

        Subcategory::create([
            'name' => 'Cóctel',
            'order' => 3,
            'category_id' => 1,
        ]);

        Subcategory::create([
            'name' => 'De Noche',
            'order' => 4,
            'category_id' => 1,
        ]);

//        CREATE SUBCATEGORIES FOR "Blusas"
        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 2,
        ]);

        Subcategory::create([
            'name' => 'Formales',
            'order' => 2,
            'category_id' => 2,
        ]);

        Subcategory::create([
            'name' => 'Deportivas',
            'order' => 3,
            'category_id' => 2,
        ]);

        Subcategory::create([
            'name' => 'De Noche',
            'order' => 4,
            'category_id' => 2,
        ]);

//        CREATE SUBCATEGORIES FOR "Pantalones"
        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 3,
        ]);

        Subcategory::create([
            'name' => 'Formales',
            'order' => 2,
            'category_id' => 3,
        ]);

        Subcategory::create([
            'name' => 'Deportivos',
            'order' => 3,
            'category_id' => 3,
        ]);

        Subcategory::create([
            'name' => 'Jeans',
            'order' => 4,
            'category_id' => 3,
        ]);

//        CREATE SUBCATEGORIES FOR "Faldas"
        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 4,
        ]);

        Subcategory::create([
            'name' => 'Formales',
            'order' => 2,
            'category_id' => 4,
        ]);

        Subcategory::create([
            'name' => 'Deportivas',
            'order' => 3,
            'category_id' => 4,
        ]);

        Subcategory::create([
            'name' => 'De Noche',
            'order' => 4,
            'category_id' => 4,
        ]);

//        CREATE SUBCATEGORIES FOR "Shorts"
        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 5,
        ]);

        Subcategory::create([
            'name' => 'Deportivos',
            'order' => 2,
            'category_id' => 5,
        ]);

        Subcategory::create([
            'name' => 'De Noche',
            'order' => 3,
            'category_id' => 5,
        ]);

//        CREATE SUBCATEGORIES FOR "Chaquetas"

        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 6,
        ]);

        Subcategory::create([
            'name' => 'Formales',
            'order' => 2,
            'category_id' => 6,
        ]);

        Subcategory::create([
            'name' => 'Deportivas',
            'order' => 3,
            'category_id' => 6,
        ]);

        Subcategory::create([
            'name' => 'De Noche',
            'order' => 4,
            'category_id' => 6,
        ]);

//        CREATE SUBCATEGORIES FOR "Sweaters"

        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 7,
        ]);

        Subcategory::create([
            'name' => 'Formales',
            'order' => 2,
            'category_id' => 7,
        ]);

        Subcategory::create([
            'name' => 'Deportivos',
            'order' => 3,
            'category_id' => 7,
        ]);

        Subcategory::create([
            'name' => 'De Noche',
            'order' => 4,
            'category_id' => 7,
        ]);

//        CREATE SUBCATEGORIES FOR "Camisas"

        Subcategory::create([
            'name' => 'Casuales',
            'order' => 1,
            'category_id' => 8,
        ]);

        Subcategory::create([
            'name' => 'Formales',
            'order' => 2,
            'category_id' => 8,
        ]);

        Subcategory::create([
            'name' => 'Deportivas',
            'order' => 3,
            'category_id' => 8,
        ]);

        Subcategory::create([
            'name' => 'De Noche',
            'order' => 4,
            'category_id' => 8,
        ]);

//        CREATE SUBCATEGORIES FOR "Trajes de Baño"

        Subcategory::create([
            'name' => 'Enterizos',
            'order' => 1,
            'category_id' => 9,
        ]);

        Subcategory::create([
            'name' => 'Bikinis',
            'order' => 2,
            'category_id' => 9,
        ]);

        Subcategory::create([
            'name' => 'Tankinis',
            'order' => 3,
            'category_id' => 9,
        ]);

        Subcategory::create([
            'name' => 'Shorts',
            'order' => 4,
            'category_id' => 9,
        ]);

//        CREATE SUBCATEGORIES FOR "Ropa Deportiva"

        Subcategory::create([
            'name' => 'Conjuntos',
            'order' => 1,
            'category_id' => 10,
        ]);

        Subcategory::create([
            'name' => 'Leggins',
            'order' => 2,
            'category_id' => 10,
        ]);

        Subcategory::create([
            'name' => 'Shorts',
            'order' => 3,
            'category_id' => 10,
        ]);

        Subcategory::create([
            'name' => 'Camisetas',
            'order' => 4,
            'category_id' => 10,
        ]);

    }
}
