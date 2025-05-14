<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'id',
        'product_id',
        'color_id',
        'talla_id',
        'order_id',
        'quantity',
        'price',
        'note',
        'created_at',
    ];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];
    const filters = [
        'name' => 'like',
    ];

    const sorts = [
        'id' => 'desc',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function color()
    {
        return $this->belongsTo(Color::class,'color_id');
    }
    public function size()
    {
        return $this->belongsTo(Size::class,'talla_id');
    }
}
