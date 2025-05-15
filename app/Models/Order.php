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
 *     @OA\Property(property="number", type="string", example="123456"),
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
        'id',
        'number',
        'subtotal',
        'discount',
        'sendCost',
        'total',
        'quantity',
        'date',
        'status',
        'deliveryDate',
        'shippingDate',
        'description',
        'user_id',
        'coupon_id',
        'invoices',
        'stage',
        'bill_number',
        'server_id',
        "scheduled_date",
        "end_date",
        "mode",
        "cellphone_number",
        "email_address",
        "address",
        "destiny",
        "zone_id",
        "district_id",
        "branch_id",
        "notes",
        "currency",
        "payment_date",
        "end_date",
        "mode",
        "cellphone_number",
        "email_address",
        "address",
        "destiny",
        "district_id",
        "branch_id",
        "notes",

        "customer",
        "payments",
        "products",
    ];

    const getfields360 = [
        "number" => 'number',
        "date" => 'date',
        "scheduled_date" => 'scheduled_date',
        "payment_date" => 'payment_date',
        "shipping_date" => 'shippingDate',
        "end_date" => 'end_date',
        "stage" => 'stage',
        "status" => 'status',
        "mode" => 'mode',
        "cellphone_number" => 'cellphone_number',
        "email_address" => 'email_address',
        "address" => 'address',
        "destiny" => 'destiny',
        "zone_id" => 'zone_id',
        "district_id" => 'district_id',
        "branch_id" => 'branch_id',
        "notes" => 'notes',
        "total" => 'total',
        "currency" => 'currency',
        "shipping_cost" => 'sendCost',
        "invoices" => 'invoices',

        "customer" => 'customer',
        "payments" => 'payments',
        "products" => 'products',

    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

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
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function sendInformation()
    {
        return $this->hasOne(SendInformation::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

}
