<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     title="GroupMenu",
 *     type="object",
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Admin"),
 *     @OA\Property(property="icon", type="string", example="fas fa-user"),
 *     @OA\Property(property="order", type="number", example="1"),
 * )
 */
class GroupMenu extends Model
{
    use HasFactory;

    protected $table = 'groupmenu';

    protected $fillable = [
        'name',
        'icon',
        'order',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function optionMenus(): HasMany
    {
        return $this->hasMany(OptionMenu::class, 'groupmenu_id');
    }
}
