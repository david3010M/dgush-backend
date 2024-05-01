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
 *     @OA\Property(property="typeuser_id", type="integer", example="1")
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

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
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
