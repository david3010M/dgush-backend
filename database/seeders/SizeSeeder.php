<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    protected $model = Size::class;

    public function run(): void
    {
        Size::create([
            'name' => 'XS',
            'value' => 'XS'
        ]);

        Size::create([
            'name' => 'S',
            'value' => 'S'
        ]);

        Size::create([
            'name' => 'M',
            'value' => 'M'
        ]);

        Size::create([
            'name' => 'L',
            'value' => 'L'
        ]);

        Size::create([
            'name' => 'XL',
            'value' => 'XL'
        ]);

        Size::create([
            'name' => 'XXL',
            'value' => 'XXL'
        ]);
    }
}
