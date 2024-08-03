<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sede extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'district_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'name' => 'like',
        'address' => 'like',
        'phone' => 'like',
        'email' => 'like',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }


}
