<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeUser extends Model
{
    use HasFactory;

    protected $table = 'typeuser';

    protected $fillable = [
        'name',
    ];

    public function access()
    {
        return $this->hasMany(Access::class, 'typeuser_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'typeuser_id');
    }
}
