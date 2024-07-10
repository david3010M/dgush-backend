<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Category 1")
 * )
 */
class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'category';

    protected $fillable = [
        'name',
        'value'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
