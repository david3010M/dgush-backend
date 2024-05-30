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
            ['name' => 'Lambayeque', 'province_id' => 1],
            ['name' => 'Jayanca', 'province_id' => 1],
            ['name' => 'Chiclayo', 'province_id' => 2],
            ['name' => 'Ferreñafe', 'province_id' => 3]
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }
    }
}
