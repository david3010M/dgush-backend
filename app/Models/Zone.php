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
        'status',
        'server_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'server_id' => 'integer',
    ];

    const getfields360 = [
        'name'=>'name',
        'sendCost'=>'price',
        'status'=>'status',
    ];

    const filters = [
        'name' => 'like',
        'sendCost' => 'like',
        'status' => '=',
        'server_id'=> '=',
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
