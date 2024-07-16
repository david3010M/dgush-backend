<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => "Verano", "route" => "https://dgush-storage.sfo3.digitaloceanspaces.com/banner/verano.png", "image_id" => ""],
            ['name' => "Remate", "route" => "https://dgush-storage.sfo3.digitaloceanspaces.com/banner/remate.png", "image_id" => ""],
            ['name' => "Cincuenta", "route" => "https://dgush-storage.sfo3.digitaloceanspaces.com/banner/50.png", "image_id" => ""]
        ];

        foreach ($data as $banner) {
            $image = Image::create([
                'name' => $banner['name'],
                'url' => $banner['route'],
            ]);

            $banner['image_id'] = $image->id;

            Banner::create($banner);
        }
    }
}
