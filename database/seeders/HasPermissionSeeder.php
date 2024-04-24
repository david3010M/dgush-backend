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

    }
}
