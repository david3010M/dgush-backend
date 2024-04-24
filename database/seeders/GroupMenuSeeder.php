<?php

namespace Database\Seeders;

use App\Models\GroupMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupMenuSeeder extends Seeder
{
    protected $model = GroupMenu::class;

    public function run(): void
    {
        GroupMenu::create([
            'name' => 'Administración',
            'icon' => 'fas fa-cogs',
            'order' => 1,
        ]);

        GroupMenu::create([
            'name' => 'Ventas',
            'icon' => 'fas fa-shopping-cart',
            'order' => 2,
        ]);

        GroupMenu::create([
            'name' => 'Inventario',
            'icon' => 'fas fa-boxes',
            'order' => 3,
        ]);

        GroupMenu::create([
            'name' => 'Reportes',
            'icon' => 'fas fa-chart-line',
            'order' => 4,
        ]);

        GroupMenu::create([
            'name' => 'Configuración',
            'icon' => 'fas fa-cog',
            'order' => 5,
        ]);
    }
}
