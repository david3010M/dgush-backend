<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Person extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'dni',
        'names',
        'fatherSurname',
        'motherSurname',
        'phone',
        'email',
        'address',
        'reference',
        'district_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const filters = [
        'dni' => 'like',
        'names' => 'like',
        'fatherSurname' => 'like',
        'motherSurname' => 'like',
        'phone' => 'like',
        'email' => 'like',
        'address' => 'like',
        'reference' => 'like',
        'district_id' => 'like',
    ];

    const sorts = [
        'id',
        'dni',
        'names',
        'fatherSurname',
        'motherSurname',
        'phone',
        'email',
        'address',
        'reference',
        'district_id',
    ];

    public function district()
    {
        return $this->belongsTo(District::class);
    }

}
