<?php

namespace Database\Seeders;

use App\Models\TypeUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Nette\Utils\Type;

class TypeUserSeeder extends Seeder
{
    protected $model = TypeUser::class;

    public function run(): void
    {
        $array = [
            'Administrador',
            'Cliente',
            'Invitado',
        ];

        foreach ($array as $item) {
            $this->model::create([
                'name' => $item,
            ]);
        }
    }
}
