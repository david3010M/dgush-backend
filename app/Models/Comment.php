<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Comment",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="description", type="string", example="This is a comment"),
 *     @OA\Property(property="score", type="integer", example="5"),
 *     @OA\Property(property="user_id", type="integer", example="1"),
 *     @OA\Property(property="product_id", type="integer", example="1"),
 * )
 */
class Comment extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'comment';

    protected $fillable = [
        'description',
        'score',
        'user_id',
        'product_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
