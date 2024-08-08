<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="OrderItem",
 *     title="OrderItem",
 *     description="OrderItem model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="product_detail_id", type="integer", example="1"),
 *     @OA\Property(property="order_id", type="integer", example="1"),
 *     @OA\Property(property="productDetail", type="object", ref="#/components/schemas/ProductDetails")
 * )
 */
class OrderItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'quantity',
        'price',
        'product_detail_id',
        'order_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function productDetail()
    {
        return $this->belongsTo(ProductDetails::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
