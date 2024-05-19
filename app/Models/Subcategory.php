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
 *     @OA\Property(property="order", type="integer", example="1"),
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
        'order',
        'score',
        'category_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

//    SEARCH
    public static function search($search, $sort, $direction)
    {
        $query = Subcategory::query();

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
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
