<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Color",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Red"),
 *     @OA\Property(property="value", type="string", example="red"),
 *     @OA\Property(property="hex", type="string", example="#FF0000")
 * )
 */
class Color extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'color';

    protected $fillable = [
        'name',
        'value',
        'hex',
        'status',
        'server_id',
    ];

    const getfields360 = [
        'name'=>'name',
        'hex'=>'code',
        'status'=>'status',
        'server_id'=>'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function productDetails()
    {
        return $this->hasMany(ProductDetails::class);
    }
}
