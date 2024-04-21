<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMenu extends Model
{
    use HasFactory;

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
