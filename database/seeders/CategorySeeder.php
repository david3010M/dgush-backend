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
            'value' => 'vestidos'
        ]);

        Category::create([
            'name' => 'Blusas',
            'value' => 'blusas',
        ]);

        Category::create([
            'name' => 'Pantalones',
            'value' => 'pantalones',
        ]);

        Category::create([
            'name' => 'Faldas',
            'value' => 'faldas',
        ]);

        Category::create([
            'name' => 'Shorts',
            'value' => 'shorts',
        ]);

        Category::create([
            'name' => 'Chaquetas',
            'value' => 'chaquetas',
        ]);

        Category::create([
            'name' => 'Sweaters',
            'value' => 'sweaters',
        ]);

        Category::create([
            'name' => 'Camisas',
            'value' => 'camisas',
        ]);

        Category::create([
            'name' => 'Trajes de Baño',
            'value' => 'trajes-de-bano',
        ]);

        Category::create([
            'name' => 'Ropa Deportiva',
            'value' => 'ropa-deportiva',
        ]);
    }
}
