<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Province",
 *     title="Province",
 *     description="Province model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Jawa Barat"),
 *     @OA\Property(property="districts", type="array", @OA\Items(ref="#/components/schemas/District"))
 * )
 *
 */
class Province extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'province';

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function districts()
    {
        return $this->hasMany(District::class);
    }

}
