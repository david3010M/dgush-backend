<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    protected $model = User::class;

    public function run(): void
    {
        User::factory()->create([
            'names' => 'D Gush',
            'email' => 'dgush@gmail.com',
            'password' => Hash::make('12345678'),
            'typeuser_id' => 1,
            'remember_token' => Str::random(10),
        ]);
    }
}
