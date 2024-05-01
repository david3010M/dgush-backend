<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="ProductColor",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="product_id", type="integer", example="1"),
 *     @OA\Property(property="color_id", type="integer", example="1")
 * )
 */
class ProductColor extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'product_color';

    protected $fillable = [
        'product_id',
        'color_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
