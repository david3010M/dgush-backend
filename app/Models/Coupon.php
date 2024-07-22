<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Validation\Rule;


/**
 * @OA\Schema(
 *     schema="Coupon",
 *     @OA\Property(property="id",type="integer", example="1"),
 *     @OA\Property(property="name",type="string", example="10% Off"),
 *     @OA\Property(property="description",type="string", example="10% discount on all purchases."),
 *     @OA\Property(property="code",type="string", example="10OFF"),
 *     @OA\Property(property="type",type="string", example="percentage"),
 *     @OA\Property(property="indicator", type="string", example="subtotal"),
 *     @OA\Property(property="value",type="number", example="10"),
 *     @OA\Property(property="expires_at",type="string", format="date-time", example="2022-05-26 14:39:51"),
 * )
 *
 * @OA\Schema(
 *     schema="CouponRequest",
 *     required={"name", "description", "code", "type", "value", "expires_at"},
 *     @OA\Property(property="name",type="string", example="10% Off"),
 *     @OA\Property(property="description",type="string", example="10% discount on all purchases."),
 *     @OA\Property(property="code",type="string", example="10OFF"),
 *     @OA\Property(property="type", enum={"percentage", "discount"}, type="string", example="percentage"),
 *     @OA\Property(property="indicator", enum={"subtotal", "total", "sendCost"}, type="string", example="subtotal"),
 *     @OA\Property(property="value",type="number", example="10"),
 *     @OA\Property(property="expires_at",type="string", format="date-time", example="2022-05-26 14:39:51"),
 * )
 */
class Coupon extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'coupon';

    protected $fillable = [
        'name',
        'description',
        'code',
        'type',
        'indicator',
        'value',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

}
