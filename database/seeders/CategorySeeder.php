<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    protected $model = Category::class;

    public function run(): void
    {
//        CREATE CATEGORIES FOR WOMAN CLOTHES
        Category::create([
            'name' => 'Vestidos',
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Blusas',
            'order' => 2,
        ]);

        Category::create([
            'name' => 'Pantalones',
            'order' => 3,
        ]);

        Category::create([
            'name' => 'Faldas',
            'order' => 4,
        ]);

        Category::create([
            'name' => 'Shorts',
            'order' => 5,
        ]);

        Category::create([
            'name' => 'Chaquetas',
            'order' => 6,
        ]);

        Category::create([
            'name' => 'Sweaters',
            'order' => 7,
        ]);

        Category::create([
            'name' => 'Camisas',
            'order' => 8,
        ]);

        Category::create([
            'name' => 'Trajes de Baño',
            'order' => 9,
        ]);

        Category::create([
            'name' => 'Ropa Deportiva',
            'order' => 10,
        ]);
    }
}
