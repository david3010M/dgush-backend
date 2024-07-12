<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Order",
 *     title="Order",
 *     description="Order model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="subtotal", type="decimal", example="100.00"),
 *     @OA\Property(property="discount", type="decimal", example="10.00"),
 *     @OA\Property(property="sendCost", type="decimal", example="5.00"),
 *     @OA\Property(property="total", type="decimal", example="90.00"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="date", type="timestamp", example="2024-05-26 14:40:02"),
 *     @OA\Property(property="user_id", type="integer", example="1"),
 *     @OA\Property(property="coupon_id", type="integer", example="1"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *     @OA\Property(property="orderItems", type="array", @OA\Items(ref="#/components/schemas/OrderItem")),
 *     @OA\Property(property="coupon", type="object", ref="#/components/schemas/Coupon"),
 * )
 *
 *
 * @OA\Schema (
 *     schema="OrderRequest",
 *     title="OrderRequest",
 *     description="Order request model",
 *     @OA\Property(property="subtotal", type="decimal", example="100.00"),
 *     @OA\Property(property="total", type="decimal", example="90.00"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="date", type="timestamp", example="2024-05-26 14:40:02"),
 *     @OA\Property(property="user_id", type="integer", example="1"),
 *     @OA\Property(property="coupon_id", type="integer", example="1")
 * )
 *
 * @OA\Schema (
 *     schema="OrderConfirmation",
 *     title="OrderConfirmation",
 *     description="Order confirmation model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="subtotal", type="decimal", example="100.00"),
 *     @OA\Property(property="discount", type="decimal", example="10.00"),
 *     @OA\Property(property="sendCost", type="decimal", example="5.00"),
 *     @OA\Property(property="total", type="decimal", example="90.00"),
 *     @OA\Property(property="quantity", type="integer", example="1"),
 *     @OA\Property(property="date", type="timestamp", example="2024-05-26 14:40:02"),
 *     @OA\Property(property="user_id", type="integer", example="1"),
 *     @OA\Property(property="coupon_id", type="integer", example="1"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/User"),
 *     @OA\Property(property="orderItems", type="array", @OA\Items(ref="#/components/schemas/OrderItem")),
 *     @OA\Property(property="coupon", type="object", ref="#/components/schemas/Coupon"),
 *     @OA\Property(property="sendInformation", type="object", ref="#/components/schemas/SendInformation")
 * )
 *
 */
class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'order';

    protected $fillable = [
        'subtotal',
        'discount',
        'sendCost',
        'total',
        'quantity',
        'date',
        'status',
        'description',
        'user_id',
        'coupon_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
//
//    public static function boot()
//    {
//        parent::boot();
//
//        static::creating(function ($model) {
//            $existing = ProductDetails::withTrashed()
//                ->where('product_id', $model->product_id)
//                ->where('color_id', $model->color_id)
//                ->where('size_id', $model->size_id)
//                ->whereNull('deleted_at')
//                ->first();
//
//            if ($existing) {
//                return response()->json(['error' => 'A product detail with the same product_id, color_id, and size_id already exists.'], 422);
//            }
//        });
//
//        static::updating(function ($model) {
//            $existing = ProductDetails::withTrashed()
//                ->where('product_id', $model->product_id)
//                ->where('color_id', $model->color_id)
//                ->where('size_id', $model->size_id)
//                ->where('id', '!=', $model->id)
//                ->whereNull('deleted_at')
//                ->first();
//
//            if ($existing) {
//                return response()->json(['error' => 'A product detail with the same product_id, color_id, and size_id already exists.'], 422);
//            }
//        });
//    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function sendInformation()
    {
        return $this->hasOne(SendInformation::class);
    }

}
