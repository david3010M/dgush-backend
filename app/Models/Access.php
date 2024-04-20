<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Access extends Model
{
    use HasFactory;

    protected $table = 'access';

    public function optionMenu()
    {
        return $this->belongsTo(OptionMenu::class, 'optionmenu_id');
    }

    public function typeUser()
    {
        return $this->belongsTo(TypeUser::class, 'typeuser_id');
    }
}
