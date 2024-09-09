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
        'status'
    ];

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'status' => 'boolean'
    ];

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
        return $this->hasMany(OrderItem::class, 'product_detail_id');
    }

    public static function search($product, $color, $size, $subcategory, $sort, $direction, $per_page, $page)
    {
        $query = ProductDetails::query();

        if ($product) {
            $query->whereHas('product', function ($query) use ($product) {
                $query->whereIn('product_id', $product);
            });
        }

        if ($subcategory) {
            $subcategory = Subcategory::whereIn('value', $subcategory)->pluck('id');
            $query->whereHas('product', function ($query) use ($subcategory) {
                $query->whereIn('subcategory_id', $subcategory);
            });
        }

        if ($color) {
            $color = Color::whereIn('value', $color)->pluck('id');
            $query->whereIn('color_id', $color);
        }

        if ($size) {
            $size = Size::whereIn('value', $size)->pluck('id');
            $query->whereIn('size_id', $size);
        }

        if ($sort == 'price-asc') {
            $sort = 'price1';
            $direction = 'asc';
        } elseif ($sort == 'price-desc') {
            $sort = 'price1';
            $direction = 'desc';
        }

        if ($per_page && $page) {
            return $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->paginate($per_page, ['*'], 'page', $page);
        } else {
            return $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->get();
        }
    }
}
