<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
 *     @OA\Property(property="status", type="'onsale'|'new'", example="onsale"),
 *     @OA\Property(property="subcategory_id", type="integer", example="1"),
 *     @OA\Property(property="image", type="string", example="image.jpg"),
 * )
 *
 * @OA\Schema (
 *     schema="ProductRequest",
 *     required={"name", "description", "detailweb", "price1", "price2", "subcategory_id", "product_details[]", "images[]"},
 *     @OA\Property(property="name", type="string", example="Product 1"),
 *     @OA\Property(property="description", type="string", example="Description of product 1"),
 *     @OA\Property(property="detailweb", type="string", example="Detail of product 1"),
 *     @OA\Property(property="price1", type="number", example="100.00"),
 *     @OA\Property(property="price2", type="number", example="90.00"),
 *     @OA\Property(property="status", type="'onsale'|'new'", example="onsale"),
 *     @OA\Property(property="subcategory_id", type="integer", example="1"),
 *     @OA\Property(property="product_details[]", type="array",
 *         @OA\Items(
 *             @OA\Property(property="color_id", type="integer", example="1"),
 *             @OA\Property(property="size_id", type="integer", example="1"),
 *             @OA\Property(property="stock", type="integer", example="10"),
 *         )
 *     ),
 *     @OA\Property(property="images[]", type="array",
 *         @OA\Items(type="file", format="binary"),
 *     )
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
        'status',
        'subcategory_id',
        'image',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot'
    ];

    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            $model->validate();
        });

        static::saved(function ($model) {
            Subcategory::find($model->subcategory_id)->update([
                'score' => round(Product::where('subcategory_id', $model->subcategory_id)->avg('score'), 1),
            ]);
        });
    }

    /**
     * @throws ValidationException
     */
    public function validate()
    {
        $validator = Validator::make($this->attributes, [
            'status' => [
                Rule::in(['onsale', 'new', ''])
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }


    public static function withImage()
    {
        $query = Product::query();
        $query->addSelect(['image' => Image::select('url')
            ->whereColumn('product_id', 'product.id')
            ->orderBy('id')
            ->limit(1)]);

        return $query->orderBy('id', 'desc')->simplePaginate(12);
    }

    public static function search($search, $status, $score, $subcategory, $price, $color, $size, $sort, $direction)
    {
        $query = Product::query();
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%')
                    ->orWhere('detailweb', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($score) {
            $query->where('score', '>=', $score);
            $sort = 'score';
            $direction = 'desc';
        }

        if ($subcategory) {
            $subcategory = Subcategory::whereIn('value', $subcategory)->pluck('id');
            $query->whereIn('subcategory_id', $subcategory);
        }

        if ($price !== null && $price[1] > 0) {
            $query->where(function ($query) use ($price) {
                $query->whereBetween('price1', $price)
                    ->orWhereBetween('price2', $price);
            });
        }

        //        COLOR
        if ($color) {
            $color = Color::whereIn('value', $color)->pluck('id');
            $query->whereHas('productColors', function ($query) use ($color) {
                $query->whereIn('color_id', $color);
            });
        }

        //        SIZE
        if ($size) {
            $size = Size::whereIn('value', $size)->pluck('id');
            $query->whereHas('productSizes', function ($query) use ($size) {
                $query->whereIn('size_id', $size);
            });
        }

        //        ADD COLUMN IMAGE WITCH IS THE FIRST IMAGE OF THE PRODUCT IN TABLE IMAGE
        $query->addSelect(['image' => Image::select('url')
            ->whereColumn('product_id', 'product.id')
            ->orderBy('id')
            ->limit(1)]);

//        SORT: none, price-asc, price-desc
        if ($sort == 'price-asc') {
            $sort = 'price1';
            $direction = 'asc';
        } elseif ($sort == 'price-desc') {
            $sort = 'price1';
            $direction = 'desc';
        }

        return $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->get();
    }

    public static function getColorsByProduct($id)
    {
        return Product::join('product_details', 'product.id', '=', 'product_details.product_id')
            ->join('color', 'product_details.color_id', '=', 'color.id')
            ->where('product.id', $id)
            ->select('color.id', 'color.name', 'color.value', 'color.hex')
            ->distinct()
            ->get();
    }

    public static function getSizesByProduct($id)
    {
        return Product::join('product_details', 'product.id', '=', 'product_details.product_id')
            ->join('size', 'product_details.size_id', '=', 'size.id')
            ->where('product.id', $id)
            ->select('size.id', 'size.name', 'size.value')
            ->orderBy('size.id')
            ->distinct()
            ->get();
    }

    public static function getProductDetails($id)
    {
        return Product::join('product_details', 'product.id', '=', 'product_details.product_id')
            ->join('color', 'product_details.color_id', '=', 'color.id')
            ->join('size', 'product_details.size_id', '=', 'size.id')
            ->where('product.id', $id)
            ->select('product_details.id', 'product_details.stock', 'color.id as color_id', 'color.name as color_name', 'color.value as color_value', 'size.id as size_id', 'size.name as size_name', 'size.value as size_value')
            ->orderBy('color.id')
            ->get();
    }

    public function productDetails()
    {
        return $this->hasMany(ProductDetails::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function productColors()
    {
        return $this->belongsToMany(Color::class, 'product_details', 'product_id', 'color_id');
    }

    public function productSizes()
    {
        return $this->belongsToMany(Size::class, 'product_details', 'product_id', 'size_id');
    }

    public function comments($id)
    {
        return Comment::where('product_id', $id)->orderBy('score', 'desc')->get();
    }

    public function images($id)
    {
        return Image::where('product_id', $id)->get();
    }

    public function imagesProduct()
    {
        return $this->hasMany(Image::class);
    }

    public function image()
    {
        return $this->hasOne(Image::class);
    }

    public static function getRelatedProducts($id)
    {
        $product = Product::find($id);
        return Product::where('subcategory_id', $product->subcategory_id)
            ->where('id', '!=', $id)
            ->addSelect(['image' => Image::select('url')
                ->whereColumn('product_id', 'product.id')
                ->orderBy('id')
                ->limit(1)])
            ->orderBy('score', 'desc')
            ->limit(6)
            ->get();

    }
}
