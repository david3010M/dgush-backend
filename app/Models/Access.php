<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Access",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="optionmenu_id", type="integer", example="1"),
 *     @OA\Property(property="typeuser_id", type="integer", example="1"),
 *     @OA\Property(property="created_at", type="string", example="2021-08-25T20:00:00"),
 *     @OA\Property(property="updated_at", type="string", example="2021-08-25T20:00:00"),
 *     @OA\Property(property="deleted_at", type="string", example="2021-08-25T20:00:00"), *
 * )
 */
class Access extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'access';

    protected $fillable = [
        'optionmenu_id',
        'typeuser_id',
    ];

    public function optionMenu()
    {
        return $this->belongsTo(OptionMenu::class, 'optionmenu_id');
    }

    public function typeUser()
    {
        return $this->belongsTo(TypeUser::class, 'typeuser_id');
    }
}
