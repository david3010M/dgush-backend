<?php

namespace Database\Seeders;

use App\Http\Controllers\ProductController;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ImageSeeder extends Seeder
{

    protected $model = Image::class;

    public function run(): void
    {
        $disk = Storage::disk('spaces');
        $files = $disk->allFiles();

//        FILES THAT START WITH {ID}/

//        foreach ($files as $file) {
//            $path = explode('/', $file);
//            $id = $path[0];
//            $name = $path[1];
//            $url = $disk->url($file);
//            Image::create([
//                'name' => $name,
//                'url' => $url,
//                'product_id' => $id
//            ]);
//        }

//        FILES THAT START WITH {ID}_/ FOR ALL PRODUCTS WITH RANDOM ASSIGNATIONS
        $products = Product::all();
        foreach ($products as $product) {
            $id = random_int(1, 7);

            $files = $disk->files($id);

            foreach ($files as $file) {
                $path = explode('/', $file);
                $name = $path[1];
                $url = $disk->url($file);
                Image::create([
                    'name' => $name,
                    'url' => $url,
                    'product_id' => $product->id
                ]);
            }
        }


    }
}
