<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="OptionMenu",
 *     type="object",
 *     title="OptionMenu",
 *     @OA\Property(property="id", type="number", example="1"),
 *     @OA\Property(property="name", type="string", example="Dashboard"),
 *     @OA\Property(property="route", type="string", example="dashboard"),
 *     @OA\Property(property="order", type="number", example="1"),
 *     @OA\Property(property="icon", type="string", example="fas fa-tachometer-alt"),
 *     @OA\Property(property="groupmenu_id", type="number", example="1"),
 * )
 */
class OptionMenu extends Model
{
    use HasFactory;

    protected $table = 'optionmenu';

    protected $fillable = [
        'name',
        'route',
        'order',
        'icon',
        'groupmenu_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function groupMenu(): BelongsTo
    {
        return $this->belongsTo(GroupMenu::class, 'groupmenu_id');
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class, 'optionmenu_id');
    }
}
