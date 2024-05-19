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

    public static function search($search, $status, $score, $subcategory, $price, $color, $size, $sort, $direction)
    {
//        dd($search, $status, $score, $subcategory, $price, $color, $size, $sort, $direction);

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
            $query->where('score', $score);
        }

        if ($subcategory) {
//            SUBACATEGORY IS ARRAY OF STRING WITH THE VALUES OF THE SUBCATEGORIES
            $subcategories = Subcategory::whereIn('value', $subcategory)->pluck('id');
            $query->whereIn('subcategory_id', $subcategories);
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

//        dd($query->toSql(), $query->getBindings());

        return $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->simplePaginate(12);
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

    public function comments($id)
    {
        return Comment::where('product_id', $id)->orderBy('score', 'desc')->get();
    }

    public function images($id)
    {
        return Image::where('product_id', $id)->get();
    }

    public function image()
    {
        return $this->belongsToMany(Image::class, 'product_image', 'product_id', 'image_id')->gir;
    }
}
