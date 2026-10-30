<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema(
 *     schema="Agency",
 *     title="Agency",
 *     description="Agencia de envío interdepartamental (modo ENVIO), sincronizada desde 360Sys",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="server_id", type="integer", example=1, description="ID en 360Sys: es el agency_id que se envía al crear el pedido"),
 *     @OA\Property(property="name", type="string", example="Shalom"),
 *     @OA\Property(property="description", type="string", example="Lo dejamos en agencia en menos de 24h, el envío lo pagas en destino."),
 *     @OA\Property(property="shipping_cost", type="boolean", example=false, description="true: se cobra envío + exceso del distrito. false: envío en 0"),
 *     @OA\Property(property="status", type="boolean", example=true),
 *     @OA\Property(property="sort_order", type="integer", example=1)
 * )
 */
class Agency extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'shipping_cost',
        'status',
        'sort_order',
        'server_id',
    ];

    const getfields360 = [
        'name'          => 'name',
        'description'   => 'description',
        'shipping_cost' => 'shipping_cost',
        'status'        => 'status',
        'sort_order'    => 'order',
    ];

    protected $casts = [
        'shipping_cost' => 'boolean',
        'status'        => 'boolean',
        'sort_order'    => 'integer',
        'server_id'     => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /** Agencias que el checkout puede ofrecer, en el orden definido en 360Sys. */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Si la agencia cobra envío + exceso del distrito. Sin dato desde 360Sys
     * (NULL) se cobra: es el comportamiento previo a las agencias.
     */
    public function chargesShipping(): bool
    {
        return (bool) ($this->shipping_cost ?? true);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
