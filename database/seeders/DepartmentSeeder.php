<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    protected $model = Department::class;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $array = [
            ['id' => 1, 'name' => 'Amazonas'],
            ['id' => 2, 'name' => 'Áncash'],
            ['id' => 3, 'name' => 'Apurímac'],
            ['id' => 4, 'name' => 'Arequipa'],
            ['id' => 5, 'name' => 'Ayacucho'],
            ['id' => 6, 'name' => 'Cajamarca'],
            ['id' => 7, 'name' => 'Callao'],
            ['id' => 8, 'name' => 'Cusco'],
            ['id' => 9, 'name' => 'Huancavelica'],
            ['id' => 10, 'name' => 'Huánuco'],
            ['id' => 11, 'name' => 'Ica'],
            ['id' => 12, 'name' => 'Junín'],
            ['id' => 13, 'name' => 'La Libertad'],
            ['id' => 14, 'name' => 'Lambayeque'],
            ['id' => 15, 'name' => 'Lima'],
            ['id' => 16, 'name' => 'Loreto'],
            ['id' => 17, 'name' => 'Madre de Dios'],
            ['id' => 18, 'name' => 'Moquegua'],
            ['id' => 19, 'name' => 'Pasco'],
            ['id' => 20, 'name' => 'Piura'],
            ['id' => 21, 'name' => 'Puno'],
            ['id' => 22, 'name' => 'San Martín'],
            ['id' => 23, 'name' => 'Tacna'],
            ['id' => 24, 'name' => 'Tumbes'],
            ['id' => 25, 'name' => 'Ucayali'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }

    }
}
