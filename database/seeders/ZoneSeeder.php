<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'José Leonardo Ortiz',
            'La Victoria',
            'Santa Victoria',
            'Bancarios',
            '9 de Octubre',
            'La Primavera',
        ];

        foreach ($data as $value) {
            Zone::factory()->create(['name' => $value]);
        }
    }
}
