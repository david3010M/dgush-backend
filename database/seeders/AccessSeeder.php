<?php

namespace Database\Seeders;

use App\Models\Access;
use App\Models\OptionMenu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccessSeeder extends Seeder
{
    protected $model = Access::class;

    public function run(): void
    {
//        ADMIN ACCESS - 1
        OptionMenu::all()->each(function ($optionMenu) {
            Access::create([
                'optionmenu_id' => $optionMenu->id,
                'typeuser_id' => 1,
            ]);
        });

//        USER ACCESS - 2
        $userAccesses = [
            1, 2
        ];
        foreach ($userAccesses as $userAccess) {
            Access::create([
                'optionmenu_id' => $userAccess,
                'typeuser_id' => 2,
            ]);
        }

//        GUEST ACCESS - 3
        $guestAccesses = [
            1, 2
        ];
        foreach ($guestAccesses as $guestAccess) {
            Access::create([
                'optionmenu_id' => $guestAccess,
                'typeuser_id' => 3,
            ]);
        }
    }
}
