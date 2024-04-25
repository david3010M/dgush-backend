<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="HasPermission",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="typeuser_id", type="integer", example="1"),
 *     @OA\Property(property="permission_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2021-09-01T00:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2021-09-01T00:00:00"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2021-09-01T00:00:00")
 * )
 */
class HasPermission extends Model
{
    use HasFactory;

    protected $table = 'has_permission';

    protected $fillable = [
        'typeuser_id',
        'permission_id',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
