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
 *     @OA\Property(property="subcategory_id", type="integer", example="1"),
 * )
 */
class Product extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'product';

    protected $fillable = [
        'name',
        'description',
        'detailweb',
        'price1',
        'price2',
        'score',
        'subcategory_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function search($search)
    {
        return empty($search) ? static::query()
            : static::where('name', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('detailweb', 'like', '%' . $search . '%');
    }

    public static function getColors($id)
    {
        return Color::join('product_color', 'color.id', '=', 'product_color.color_id')
            ->where('product_color.product_id', $id)
            ->select('color.id', 'color.name')
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

    public function productColor()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function productSize()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->get();
    }
}
