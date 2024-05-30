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
            ['id' => 1, 'name' => 'Lambayeque'],
            ['id' => 2, 'name' => 'Piura'],
            ['id' => 3, 'name' => 'La Libertad'],
            ['id' => 4, 'name' => 'Ancash'],
            ['id' => 5, 'name' => 'Lima'],
            ['id' => 6, 'name' => 'Ica'],
            ['id' => 7, 'name' => 'Arequipa'],
            ['id' => 8, 'name' => 'Cusco'],
            ['id' => 9, 'name' => 'Puno'],
            ['id' => 10, 'name' => 'Madre de Dios'],
            ['id' => 11, 'name' => 'Ucayali'],
            ['id' => 12, 'name' => 'San Martín'],
            ['id' => 13, 'name' => 'Huánuco'],
            ['id' => 14, 'name' => 'Pasco'],
            ['id' => 15, 'name' => 'Junín'],
            ['id' => 16, 'name' => 'Huancavelica'],
            ['id' => 17, 'name' => 'Ayacucho'],
            ['id' => 18, 'name' => 'Apurímac'],
            ['id' => 19, 'name' => 'Cajamarca'],
            ['id' => 20, 'name' => 'Amazonas'],
            ['id' => 21, 'name' => 'Loreto'],
            ['id' => 22, 'name' => 'Tumbes'],
            ['id' => 23, 'name' => 'Callao'],
            ['id' => 24, 'name' => 'Moquegua'],
            ['id' => 25, 'name' => 'Tacna'],
        ];

        foreach ($array as $item) {
            $this->model::create($item);
        }

    }
}
