<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="SendInformation",
 *     title="SendInformation",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="names", type="string", example="John Doe"),
 *     @OA\Property(property="dni", type="string", example="12345678"),
 *     @OA\Property(property="email", type="string", example="mail@mail.com"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="address", type="string", example="Some address"),
 *     @OA\Property(property="reference", type="string", example="Some reference"),
 *     @OA\Property(property="comment", type="string", example="Some comment"),
 *     @OA\Property(property="method", type="string", example="Some method"),
 *     @OA\Property(property="province_id", type="integer", example="1"),
 *     @OA\Property(property="order_id", type="integer", example="1")
 * )
 *
 * @OA\Schema (
 *     schema="SendInformationRequest",
 *     title="SendInformationRequest",
 *     required={"names", "dni", "email", "phone", "address", "reference", "comment", "method", "province_id", "order_id"},
 *     @OA\Property(property="names", type="string", example="John Doe"),
 *     @OA\Property(property="dni", type="string", example="12345678"),
 *     @OA\Property(property="email", type="string", example="mail@mail.com"),
 *     @OA\Property(property="phone", type="string", example="987654321"),
 *     @OA\Property(property="address", type="string", example="Some address"),
 *     @OA\Property(property="reference", type="string", example="Some reference"),
 *     @OA\Property(property="comment", type="string", example="Some comment"),
 *     @OA\Property(property="method", type="string", example="Some method"),
 *     @OA\Property(property="province_id", type="integer", example="1"),
 *     @OA\Property(property="order_id", type="integer", example="1")
 * )
 *
 */
class SendInformation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'names',
        'dni',
        'email',
        'phone',
        'address',
        'reference',
        'comment',
        'method',
        'tracking',
        'voucher',
        'voucherUrl',
        'voucherFileName',
        'district_id',
        'sede_id',
        'zone_id',
        'order_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }


}
