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
        $this->call(GroupMenuSeeder::class);
        $this->call(TypeUserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(OptionMenuSeeder::class);
        $this->call(HasPermissionSeeder::class);
        $this->call(AccessSeeder::class);
        $this->call(UserSeeder::class);
    }
}
