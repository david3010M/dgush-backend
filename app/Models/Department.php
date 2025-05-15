<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Department",
 *     title="Department",
 *     description="Department model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Pendidikan"),
 *     @OA\Property(property="provinces", type="array", @OA\Items(ref="#/components/schemas/Province"))
 * )
 */
class Department extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'department';

    protected $fillable = ['name', 'location_code', 'server_id'];
    const getfields360 = [
        'name' => 'name',
        'location_code' => 'location_code',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function provinces()
    {
        return $this->hasMany(Province::class);
    }

}
