<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    protected $model = District::class;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['name' => 'Lambayeque', 'province_id' => 1, 'sendCost' => 15],
            ['name' => 'Jayanca', 'province_id' => 1, 'sendCost' => 20],
            ['name' => 'Chiclayo', 'province_id' => 2, 'sendCost' => 10],
            ['name' => 'Ferreñafe', 'province_id' => 3, 'sendCost' => 15]
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
