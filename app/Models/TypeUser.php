<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema(
 *     schema="TypeUser",
 *     type="object",
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Admin")
 * )
 */
class TypeUser extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'typeuser';

    protected $fillable = [
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function getAccess($id)
    {
        $accesses = Access::where('typeuser_id', $id)->get();
        $access = "";
        foreach ($accesses as $acc) {
            $access .= $acc->optionmenu_id . ",";
        }
//        DELETE LAST COMMA
        $access = substr($access, 0, -1);

        return $access;
    }

    public static function getHasPermission($id)
    {
        $hasPermissions = HasPermission::where('typeuser_id', $id)->get();
        $hasPermission = "";
        foreach ($hasPermissions as $hasPerm) {
            $hasPermission .= $hasPerm->permission_id . ",";
        }
//        DELETE LAST COMMA
        $hasPermission = substr($hasPermission, 0, -1);

        return $hasPermission;
    }

    public function access()
    {
        return $this->hasMany(Access::class, 'typeuser_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'typeuser_id');
    }

    public function hasPermission()
    {
        return $this->hasMany(HasPermission::class, 'typeuser_id');
    }
}
