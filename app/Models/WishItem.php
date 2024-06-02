<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     title="WishItem",
 *     description="WishItem model",
 *     @OA\Property (property="id", type="integer", example="1"),
 *     @OA\Property (property="user_id", type="integer", example="1"),
 *     @OA\Property (property="product_id", type="integer", example="1"),
 *     @OA\Property (property="user", ref="#/components/schemas/User"),
 *     @OA\Property (property="product", ref="#/components/schemas/Product")
 * )
 *
 * @OA\Schema (
 *     schema="WishItemRequest",
 *     @OA\Property (property="product_id", type="integer", example="1"),
 *     @OA\Property (property="color_id", type="integer", example="1"),
 *     @OA\Property (property="size_id", type="integer", example="1"),
 *     @OA\Property (property="quantity", type="integer", example="1")
 * )
 *
 */
class WishItem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'product_details_id', 'quantity'];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function productDetails()
    {
        return $this->belongsTo(ProductDetails::class, 'product_details_id');
    }
}
