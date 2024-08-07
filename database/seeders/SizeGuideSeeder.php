<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SizeGuide;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeGuideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            SizeGuide::factory()->create([
                'name' => 'Guía de Tallas',
                'route' => 'https://dgush-storage.sfo3.digitaloceanspaces.com/GuiaTallas/faldas.jpg',
                'category_id' => $category->id
            ]);
        }

    }
}
