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
        /**
         * #1B4464
         * #AFAE2D
         * #255E3D
         * #5FB3C5
         * #B9A48A
         * #6d7cb5
         * #040404
         */

        Color::create([
            'name' => 'Azul Oscuro',
            'value' => 'azul-oscuro',
            'hex' => '#1B4464'
        ]);

        Color::create([
            'name' => 'Verde Oliva',
            'value' => 'verde-oliva',
            'hex' => '#AFAE2D'
        ]);

        Color::create([
            'name' => 'Verde Oscuro',
            'value' => 'verde-oscuro',
            'hex' => '#255E3D'
        ]);

        Color::create([
            'name' => 'Azul Claro',
            'value' => 'azul-claro',
            'hex' => '#5FB3C5'
        ]);

        Color::create([
            'name' => 'Beige',
            'value' => 'beige',
            'hex' => '#B9A48A'
        ]);

        Color::create([
            'name' => 'Lila',
            'value' => 'lila',
            'hex' => '#6d7cb5'
        ]);

        Color::create([
            'name' => 'black',
            'value' => 'black',
            'hex' => '#040404'
        ]);
    }
}
