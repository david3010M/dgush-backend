<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Color",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Red"),
 *     @OA\Property(property="hex", type="string", example="#FF0000")
 * )
 */
class Color extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'color';

    protected $fillable = [
        'name',
        'hex',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function productColors()
    {
        return $this->hasMany(ProductColor::class);
    }
}
