<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zone extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sendCost',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'name' => 'like',
        'sendCost' => 'like',
    ];

    const sorts = [
        'name',
        'sendCost',
    ];

    public function sendInformation()
    {
        return $this->hasMany(SendInformation::class);
    }


}
