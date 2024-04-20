<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionMenu extends Model
{
    use HasFactory;

    protected $table = 'optionmenu';

    public function grupoMenu()
    {
        return $this->belongsTo(GrupoMenu::class, 'grupomenu_id');
    }

    public function access()
    {
        return $this->hasMany(Access::class, 'optionmenu_id');
    }
}
