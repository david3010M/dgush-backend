<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Product",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Product 1"),
 *     @OA\Property(property="description", type="string", example="Description of product 1"),
 *     @OA\Property(property="detailweb", type="string", example="Detail of product 1"),
 *     @OA\Property(property="price1", type="number", example="100.00"),
 *     @OA\Property(property="price2", type="number", example="90.00"),
 *     @OA\Property(property="score", type="integer", example="5"),
 *     @OA\Property(property="image", type="string", example="image.jpg"),
 *     @OA\Property(property="status", type="'onsale'|'new'", example="onsale"),
 *     @OA\Property(property="subcategory_id", type="integer", example="1"),
 * )
 */
class Product extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'product';

    protected $fillable = [
        'name',
        'description',
        'detailweb',
        'price1',
        'price2',
        'score',
        'image',
        'status',
        'subcategory_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot'
    ];

    public static function search($search, $category, $subcategory, $price, $colors, $sizes, $sort, $direction)
    {
        $colors = explode(',', $colors);
        $sizes = explode(',', $sizes);

        $query = Product::query();
        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($category) {
            $query->whereHas('subcategory', function ($q) use ($category) {
                $q->where('category_id', $category);
            });
        }

        if ($subcategory) {
            $query->where('subcategory_id', $subcategory);
        }

        if ($price !== null && $price > 0) {
            $query->where('price1', '<=', $price);
        }

        if ($colors[0] != null) {
            $query->whereHas('productColors', function ($q) use ($colors) {
                $q->whereIn('color_id', $colors);
            });
        }

        if ($sizes[0] != null) {
            $query->whereHas('productSizes', function ($q) use ($sizes) {
                $q->whereIn('size_id', $sizes);
            });
        }

//        ADD IMAGES FROM TABLE IMAGE
        $query->with('images');

        return $query->orderBy($sort, $direction)->simplePaginate(12);
    }

    public static function getColors($id)
    {
        return Color::join('product_color', 'color.id', '=', 'product_color.color_id')
            ->where('product_color.product_id', $id)
            ->select('color.id', 'color.name', 'color.hex')
            ->get();
    }

    public static function getSizes($id)
    {
        return Size::join('product_size', 'size.id', '=', 'product_size.size_id')
            ->where('product_size.product_id', $id)
            ->select('size.id', 'size.name')
            ->get();
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function productColors()
    {
        return $this->belongsToMany(Color::class, 'product_color', 'product_id', 'color_id');
    }

    public function productSizes()
    {
        return $this->belongsToMany(Size::class, 'product_size', 'product_id', 'size_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function images($id)
    {
        return Image::where('product_id', $id)->get();
    }
}
