<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CulquiLog extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'action',
        'request',
        'response',
        'ip_address',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    
    const filters = [
        'action'     => 'like',
        'request'    => 'like',
        'response'   => 'like',
        'ip_address' => 'like',
        'created_at' => 'between',
    ];

    /**
     * Campos de ordenación disponibles.
     */
    const sorts = [
        'id' => 'desc',
    ];
}
