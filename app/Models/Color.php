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

    public const ACTIVE_STATUS_VALUES = [true, 1, '1', 'true'];

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

    protected $casts = [
        'server_id' => 'integer',
        'status' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('status')
                ->orWhereIn('status', self::ACTIVE_STATUS_VALUES);
        });
    }

    public function productDetails()
    {
        return $this->hasMany(ProductDetails::class);
    }
}
