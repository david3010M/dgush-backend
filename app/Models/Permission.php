<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema(
 *     schema="Permission",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="permission"),
 *     @OA\Property(property="route", type="string", example="permission"),
 * )
 */
class Permission extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'permission';

    protected $fillable = [
        'name',
        'route',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function hasPermission()
    {
        return $this->hasMany(HasPermission::class, 'permission_id');
    }
}
