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
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-09-01T00:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-09-01T00:00:00"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2021-09-01T00:00:00"),
 * )
 *
 */
class Permission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'permission';

    protected $fillable = [
        'name',
        'route',
    ];

    public function hasPermission()
    {
        return $this->hasMany(HasPermission::class, 'permission_id');
    }
}
