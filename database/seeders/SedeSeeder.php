<?php

namespace Database\Seeders;

use App\Models\Sede;
use Illuminate\Database\Seeder;

class SedeSeeder extends Seeder
{
    public function run(): void
    {
        $array = [
            ['name' => 'Galería La Central', 'address' => 'Galería La Central 3er piso #109',
                'phone' => '914173535', 'email' => 'lesleslie.vas.eco@gmail.com', 'district_id' => 1,
            ],
            ['name' => 'Bolognesi', 'address' => 'Av. Bolognesi #135',
                'phone' => '914173535', 'email' => 'lesleslie.vas.eco@gmail.com', 'district_id' => 2,
            ]
        ];

        foreach ($array as $item) {
            Sede::create($item);
        }
    }
}
