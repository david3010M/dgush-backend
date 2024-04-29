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
 *     @OA\Property(property="user_id", type="integer", example="1"),
 *     @OA\Property(property="post_id", type="integer", example="1"),
 *     @OA\Property(property="comment", type="string", example="This is a comment")
 * )
 */
class Comment extends Model
{
    use HasFactory;

//    use SoftDeletes;

    protected $table = 'comment';

    protected $fillable = [
        'user_id',
        'post_id',
        'comment',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
