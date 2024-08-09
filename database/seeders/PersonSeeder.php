<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Person::factory()->create([
            'dni' => '12345678',
            'names' => 'D Gush',
            'fatherSurname' => 'Admin',
            'motherSurname' => 'Root',
            'email' => 'dgush@gmail.com',
            'phone' => '123456789',
            'address' => 'Calle 123',
            'reference' => 'Calle 123',
            'district_id' => 1,
        ]);
    }
}
