<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoMenu extends Model
{
    use HasFactory;

    protected $table = 'grupomenu';

    protected $fillable = [
        'name',
        'icon',
        'order',
    ];

//    protected $hidden = [
//        'password',
//        'remember_token',
//    ];

    public function optionMenus()
    {
        return $this->hasMany(OptionMenu::class, 'grupomenu_id');
    }
}
