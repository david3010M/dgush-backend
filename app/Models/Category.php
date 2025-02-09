<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Category 1")
 * )
 */
class Category extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $table = 'category';

    protected $fillable = [
        'name',
        'value',
        
        'status',
        'server_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const getfields360 = [
        'name'=>'name',
        'status'=>'status',
        'server_id'=>'id',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    public function sizeGuides()
    {
        return $this->hasOne(SizeGuide::class);
    }
}
