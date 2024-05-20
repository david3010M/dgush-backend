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
            'value' => 'vestidos',
            'order' => 1,
        ]);

        Category::create([
            'name' => 'Blusas',
            'value' => 'blusas',
            'order' => 2,
        ]);

        Category::create([
            'name' => 'Pantalones',
            'value' => 'pantalones',
            'order' => 3,
        ]);

        Category::create([
            'name' => 'Faldas',
            'value' => 'faldas',
            'order' => 4,
        ]);

        Category::create([
            'name' => 'Shorts',
            'value' => 'shorts',
            'order' => 5,
        ]);

        Category::create([
            'name' => 'Chaquetas',
            'value' => 'chaquetas',
            'order' => 6,
        ]);

        Category::create([
            'name' => 'Sweaters',
            'value' => 'sweaters',
            'order' => 7,
        ]);

        Category::create([
            'name' => 'Camisas',
            'value' => 'camisas',
            'order' => 8,
        ]);

        Category::create([
            'name' => 'Trajes de Baño',
            'value' => 'trajes-de-bano',
            'order' => 9,
        ]);

        Category::create([
            'name' => 'Ropa Deportiva',
            'value' => 'ropa-deportiva',
            'order' => 10,
        ]);
    }
}
