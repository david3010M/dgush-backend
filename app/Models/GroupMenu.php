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
 *     @OA\Property(
 *         property="id",
 *         type="number",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Admin"
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         example="fas fa-user"
 *     ),
 *     @OA\Property(
 *         property="order",
 *         type="number",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         example="2024-02-23T00:09:16.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         example="2024-02-23T12:13:45.000000Z"
 *     ),
 *     @OA\Property(
 *         property="deleted_at",
 *         type="string",
 *         example="null"
 *     )
 * )
 */
class GroupMenu extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'groupmenu';

    protected $fillable = [
        'name',
        'icon',
        'order',
    ];

    public function optionMenus(): HasMany
    {
        return $this->hasMany(OptionMenu::class, 'groupmenu_id');
    }
}
