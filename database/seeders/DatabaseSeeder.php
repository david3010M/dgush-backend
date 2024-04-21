<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Access;
use App\Models\GroupMenu;
use App\Models\OptionMenu;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        GroupMenu::factory(10)->create();
        TypeUser::factory(10)->create();
        User::factory(10)->create();
        OptionMenu::factory(10)->create();
        Access::factory(10)->create();


        User::factory()->create([
            'names' => 'D Gush',
            'email' => 'dgush@gmail.com',
            'password' => Hash::make('12345678'),
            'typeuser_id' => fake()->numberBetween(1, 10),
            'remember_token' => Str::random(10),
        ]);
    }
}
