<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Size",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Small"),
 *     @OA\Property(property="value", type="string", example="S")
 * )
 */
class Size extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'size';

    protected $fillable = [
        'name',
        'value'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function productDetails()
    {
        return $this->hasMany(ProductDetails::class);
    }

}
