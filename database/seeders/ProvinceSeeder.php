<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    protected $model = Province::class;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['id' => 1, 'name' => 'Lambayeque', 'department_id' => 1],
            ['id' => 2, 'name' => 'Chiclayo', 'department_id' => 1],
            ['id' => 3, 'name' => 'Ferreñafe', 'department_id' => 1]
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
