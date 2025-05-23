<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="District",
 *     title="District",
 *     description="District model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Kota Bandung"),
 *     @OA\Property(property="province_id", type="integer", example="1")
 * )
 *
 */
class District extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'district';

    protected $fillable = [
        'name',
        'province_id',
        'sendCost',
        'ubigeo',

        'status',
        'server_id',
        'region',
        'province',
        'district',
    ];

    const getfields360 = [
        'name'      => 'name',
        'sendCost'  => 'price',
        'status'    => 'status',
        'ubigeo'    => 'location_code',

        'region' => 'region',
        'province'  => 'province',
        'district'  => 'district',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function send_information()
    {
        return $this->hasOne(SendInformation::class);
    }
}
