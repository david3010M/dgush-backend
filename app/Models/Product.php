<?php

namespace App\Models;

use App\Http\Resources\ProductResource;
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
 *     @OA\Property(property="status", type="string", enum={"onsale", "new", "preventa", "none"}, example="onsale"),
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
        'price12',
        'priceOferta',
        'priceLiquidacion',
        'percentageDiscount',
        'score',
        'status',
        'liquidacion',
        'subcategory_id',
        'image',

        'status_server',
        'currency',
        'server_id',
    ];

    protected $casts = [
        'server_id' => 'integer',
        'liquidacion' => 'boolean',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot',
    ];

    const getfields360 = [
        "name" => 'name',
        "description" => "description",
        "detailweb" => "description",

        "price1" => "price",
        "price2" => "price",
        "priceOferta" => "promo_price",
        "currency" => "currency",
        "status_server" => "status",
        "created_at" => 'created_at',
        "server_id" => 'id',

        "priceLiquidacion" => "clearance_price",
        "liquidacion" => "on_clearance",

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
                Rule::in(['onsale', 'new', 'preventa', '']),
            ],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public static function withImage()
    {
        $query = Product::query();
        $query->addSelect([
            'image' => Image::select('url')
                ->whereColumn('product_id', 'product.id')
                ->orderBy('id')
                ->limit(1)
        ]);

        return $query->orderBy('id', 'desc')->simplePaginate(12);
    }

    public function SizeGuide($id)
    {
        return SizeGuide::where('product_id', $id)->first();
    }

    public function guideSize()
    {
        return $this->hasOne(SizeGuide::class);
    }

    public static function search($search, $status, $liquidacion, $score, $subcategory, $price, $color, $size, $sort, $direction, $per_page, $page)
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

        if ($liquidacion) {
            $query->where('liquidacion', $liquidacion);
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
                    ->orWhereBetween('priceLiquidacion', $price)
                    ->orWhereBetween('priceOferta', $price);
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

        if ($sort == 'price-asc') {
            $sort = 'price1';
            $direction = 'asc';
        } elseif ($sort == 'price-desc') {
            $sort = 'price1';
            $direction = 'desc';
        }

        if ($per_page && $page) {
            $data = $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->paginate($per_page, ['*'], 'page', $page);
            ProductResource::collection($data);
            return $data;
        } else {
            $data = $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->get();
            return ProductResource::collection($data);
        }
    }

    public static function searchNoLiquidacion($search, $status, $liquidacion, $score, $subcategory, $price, $color, $size, $sort, $direction, $per_page, $page)
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

        $query->where('liquidacion', false);

        if ($score) {
            $query->where('score', '>=', $score);
            $sort = 'score';
            $direction = 'desc';
        }

        if ($subcategory) {
            $subcategory = Subcategory::whereIn('id', $subcategory)->pluck('id');
            $query->whereIn('subcategory_id', $subcategory);
        }

        if ($price !== null && $price[1] > 0) {
            $query->where(function ($query) use ($price) {
                $query->whereBetween('price1', $price)
                    ->orWhereBetween('priceLiquidacion', $price)
                    ->orWhereBetween('priceOferta', $price);
            });
        }

        //        COLOR
        if ($color) {
            $color = Color::whereIn('id', $color)->pluck('id');
            $query->whereHas('productColors', function ($query) use ($color) {
                $query->whereIn('color_id', $color);
            });
        }

        //        SIZE
        if ($size) {
            $size = Size::whereIn('id', $size)->pluck('id');
            $query->whereHas('productSizes', function ($query) use ($size) {
                $query->whereIn('size_id', $size);
            });
        }

        if ($sort == 'price-asc') {
            $sort = 'price1';
            $direction = 'asc';
        } elseif ($sort == 'price-desc') {
            $sort = 'price1';
            $direction = 'desc';
        }

        if ($per_page && $page) {
            $data = $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->paginate($per_page, ['*'], 'page', $page);
            ProductResource::collection($data);
            return $data;
        } else {
            $data = $query->orderBy($sort == 'none' ? 'id' : $sort, $direction)->get();
            return ProductResource::collection($data);
        }
    }

    public static function getColorsByProduct($id)
    {
        return Product::join('product_details', 'product.id', '=', 'product_details.product_id')
            ->join('color', 'product_details.color_id', '=', 'color.id')
            ->where('product.id', $id)
            ->whereNull('product_details.deleted_at')
            ->select('color.id', 'color.name', 'color.value', 'color.hex')
            ->distinct()
            ->get();
    }

    public static function getSizesByProduct($id)
    {
        return Product::join('product_details', 'product.id', '=', 'product_details.product_id')
            ->join('size', 'product_details.size_id', '=', 'size.id')
            ->where('product.id', $id)
            ->whereNull('product_details.deleted_at')
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
            ->whereNull('product_details.deleted_at')
            ->select(
                'product_details.id',
                'product_details.stock',
                'color.id as color_id',
                'color.name as color_name',
                'color.value as color_value',
                'size.id as size_id',
                'size.name as size_name',
                'size.value as size_value'
            )
            ->orderBy('color.id')
            ->get()
            ->map(function ($item) {
                $item->id = (int) $item->id;
                $item->color_id = (int) $item->color_id;
                $item->size_id = (int) $item->size_id;
                $item->stock = round($item->stock, 2);
                return $item;
            });
    }

    public static function getProductDetailsWithSizes($id)
    {
        // Consulta para obtener colores y tallas
        $productDetails = Product::join('product_details', 'product.id', '=', 'product_details.product_id')
            ->join('color', 'product_details.color_id', '=', 'color.id')
            ->join('size', 'product_details.size_id', '=', 'size.id')
            ->where('product.id', $id)
            ->whereNull('product_details.deleted_at')
            ->select(
                'color.id as color_id',
                'color.name as color_name',
                'color.value as color_value',
                'color.hex as color_hex',
                'size.id as size_id',
                'size.name as size_name',
                'size.value as size_value',
                'product_details.stock',
                'color.server_id as color_server_id',
                'size.server_id as size_server_id',

            )
            ->orderBy('color.id')
            ->orderBy('size.id')
            ->get();

        // Agrupar resultados por color
        $groupedDetails = $productDetails->groupBy('color_id')->map(function ($items) {
            $color = [
                'id' => $items->first()->color_id,
                'name' => $items->first()->color_name,
                'value' => $items->first()->color_value,
                'hex' => $items->first()->color_hex,
                'server_id' => (int) $items->first()->color_server_id,
                'sizes' => $items->map(function ($item) {
                    return [
                        'id' => $item->size_id,
                        'name' => $item->size_name,
                        'value' => $item->size_value,
                        'stock' => round($item->stock, 2),
                        'server_id' => (int) $item->size_server_id,
                    ];
                })->toArray(),
            ];
            return $color;
        })->values();

        return $groupedDetails;
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

        // Obtener el id de la categoría a partir de la subcategoría del producto
        $categoryId = Subcategory::find($product->subcategory_id)->category_id;

        return Product::whereHas('subcategory', function ($query) use ($categoryId) {
            $query->where('category_id', $categoryId);
        })
            ->where('id', '!=', $id)
            ->addSelect([
                'image' => Image::select('url')
                    ->whereColumn('product_id', 'product.id')
                    ->orderBy('id')
                    ->limit(1)
            ])
            ->orderBy('score', 'desc')
            ->limit(4)
            ->get()->transform(function ($item) {
                $item->image = $item->image ?? url('images/placeholder.svg');
                return $item;
            });
    }

    public static function setProductDetails($id, $productDetails)
    {
        $product = Product::find($id);
        $product->productDetails()->delete();
        foreach ($productDetails as $productDetail) {
            $product->productDetails()->create([
                'color_id' => $productDetail['color_id'],
                'size_id' => $productDetail['size_id'],
                'stock' => $productDetail['stock'],
            ]);
        }
    }
}
