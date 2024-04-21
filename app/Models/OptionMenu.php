<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function groupMenu(): BelongsTo
    {
        return $this->belongsTo(GroupMenu::class, 'groupmenu_id');
    }

    public function accesses(): HasMany
    {
        return $this->hasMany(Access::class, 'optionmenu_id');
    }
}
