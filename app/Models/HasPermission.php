<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="HasPermission",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="typeuser_id", type="integer", example="1"),
 *     @OA\Property(property="permission_id", type="integer", example="1"),
 * )
 */
class HasPermission extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'has_permission';

    protected $fillable = [
        'typeuser_id',
        'permission_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
