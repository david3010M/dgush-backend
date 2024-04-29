<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    protected $model = Color::class;

    public function run(): void
    {
        Color::create([
            'name' => 'Rojo',
            'hex' => '#FF0000',
        ]);

        Color::create([
            'name' => 'Azul',
            'hex' => '#0000FF',
        ]);

        Color::create([
            'name' => 'Verde',
            'hex' => '#00FF00',
        ]);

        Color::create([
            'name' => 'Amarillo',
            'hex' => '#FFFF00',
        ]);

        Color::create([
            'name' => 'Naranja',
            'hex' => '#FFA500',
        ]);

        Color::create([
            'name' => 'Morado',
            'hex' => '#800080',
        ]);

        Color::create([
            'name' => 'Rosa',
            'hex' => '#FFC0CB',
        ]);
    }
}
