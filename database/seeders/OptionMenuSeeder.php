<?php

namespace Database\Seeders;

use App\Models\OptionMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OptionMenuSeeder extends Seeder
{
    protected $model = OptionMenu::class;

    public function run(): void
    {
        OptionMenu::create([
            'name' => 'Productos Detacados',
            'route' => 'products-featured',
            'order' => 1,
            'icon' => 'icono-1',
            'groupmenu_id' => 1,
        ]);

        OptionMenu::create([
            'name' => 'Productos',
            'route' => 'products',
            'order' => 2,
            'icon' => 'icono-2',
            'groupmenu_id' => 1,
        ]);
    }
}
