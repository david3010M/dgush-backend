<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="ProductDetails",
 *     title="ProductDetails",
 *     description="ProductDetails model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="stock", type="integer", example="10"),
 *     @OA\Property(property="product_id", type="integer", example="1"),
 *     @OA\Property(property="color_id", type="integer", example="1"),
 *     @OA\Property(property="size_id", type="integer", example="1"),
 *     @OA\Property(property="product", type="object", ref="#/components/schemas/Product"),
 *     @OA\Property(property="color", type="object", ref="#/components/schemas/Color"),
 *     @OA\Property(property="size", type="object", ref="#/components/schemas/Size")
 * )
 */
class ProductDetails extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'stock',
        'product_id',
        'color_id',
        'size_id',
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
