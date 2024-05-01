<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * @OA\Schema (
 *     schema="ProductSize",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="product_id", type="integer", example="1"),
 *     @OA\Property(property="size_id", type="integer", example="1")
 * )
 */
class ProductSize extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'product_size';

    protected $fillable = [
        'product_id',
        'size_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
