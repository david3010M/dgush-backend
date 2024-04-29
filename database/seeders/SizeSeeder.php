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
        ]);

        Size::create([
            'name' => 'S',
        ]);

        Size::create([
            'name' => 'M',
        ]);

        Size::create([
            'name' => 'L',
        ]);

        Size::create([
            'name' => 'XL',
        ]);

        Size::create([
            'name' => 'XXL',
        ]);
    }
}
