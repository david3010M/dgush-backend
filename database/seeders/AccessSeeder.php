<?php

namespace Database\Seeders;

use App\Models\Access;
use App\Models\OptionMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessSeeder extends Seeder
{
    protected $model = Access::class;

    public function run(): void
    {
//        ADMIN ACCESS
        OptionMenu::all()->each(function ($optionMenu) {
            Access::create([
                'optionmenu_id' => $optionMenu->id,
                'typeuser_id' => 1,
            ]);
        });
    }
}
