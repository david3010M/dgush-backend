<?php

namespace App\Models;

use App\Utils\Constants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Subcategory",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="T-Shirts"),
 *     @OA\Property(property="value", type="string", example="t-shirts"),
 *     @OA\Property(property="score", type="number", example="5.0"),
 *     @OA\Property(property="image", type="string", example="t-shirts.jpg"),
 *     @OA\Property(property="isHome", type="boolean", example="false"),
 *     @OA\Property(property="category_id", type="integer", example="1")
 * )
 *
 * @OA\Schema (
 *     schema="SubcategoryRequest",
 *     type="object",
 *     @OA\Property(property="name", type="string", example="T-Shirts"),
 *     @OA\Property(property="category_id", type="integer", example="1")
 * )
 */
class Subcategory extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'subcategory';

    protected $fillable = [
        'name',
        'value',
        'score',
        'image',
        'isHome',
        'category_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'isHome' => 'boolean',
    ];

    public static function search($search, $sort, $direction, $all)
    {
        $query = Subcategory::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($sort == 'isHome') {
            return $query->orderBy($sort, $direction)->limit(6)->get();
        }

        if ($all == 'true') {
            return $query->orderBy($sort, $direction)->get();
        }

        return $query->orderBy($sort, $direction)->simplePaginate(12);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'subcategory_id');
    }
}
