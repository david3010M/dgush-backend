<?php

namespace Database\Seeders;

use App\Models\HasPermission;
use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HasPermissionSeeder extends Seeder
{
    protected $model = HasPermission::class;

    public function run(): void
    {
//        ADMIN PERMISSION
        Permission::all()->each(function ($permission) {
            HasPermission::create([
                'permission_id' => $permission->id,
                'typeuser_id' => 1,
            ]);
        });

//        CLIENT PERMISSION
        $userPermissions = [
            1, 2, 6, 12
        ];
//        foreach ($userPermissions as $userPermission) {
        Permission::all()->each(function ($permission) {
            HasPermission::create([
                'permission_id' => $permission->id,
                'typeuser_id' => 2,
            ]);
        });
//        }

//        GUEST PERMISSION
        $guestPermissions = [
            36, 37, 42, 43, 47, 48
        ];
        foreach ($guestPermissions as $guestPermission) {
            HasPermission::create([
                'permission_id' => $guestPermission,
                'typeuser_id' => 3,
            ]);
        }

    }
}
