<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasPermission extends Model
{
    use HasFactory;

    protected $table = 'has_permission';

    protected $fillable = [
        'typeuser_id',
        'permission_id',
    ];

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
