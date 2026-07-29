<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @OA\Schema (
 *     schema="District",
 *     title="District",
 *     description="District model",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="name", type="string", example="Kota Bandung"),
 *     @OA\Property(property="province_id", type="integer", example="1"),
 *     @OA\Property(property="sendCost", type="string", example="15.00", description="Costo base de envío al distrito"),
 *     @OA\Property(property="excess", type="string", example="5.00", description="Monto adicional por exceso de prendas"),
 *     @OA\Property(property="excessFactor", type="integer", example="3", description="Cada cuántas prendas se vuelve a aplicar el excess")
 * )
 *
 */
class District extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'district';

    protected $fillable = [
        'name',
        'province_id',
        'sendCost',
        'excess',
        'excessFactor',
        'ubigeo',

        'status',
        'server_id',
        'region',
        'province',
        'district',
    ];

    const getfields360 = [
        'name'         => 'name',
        'sendCost'     => 'price',
        'excess'       => 'excess',
        'excessFactor' => 'excess_factor',
        'status'       => 'status',
        'ubigeo'       => 'location_code',

        'region'   => 'region',
        'province' => 'province',
        'district' => 'district',
    ];

    protected $casts = [
        'sendCost'     => 'decimal:2',
        'excess'       => 'decimal:2',
        'excessFactor' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Unidades de exceso aplicables para una cantidad de prendas.
     *
     * La primera "tanda" de excessFactor prendas no cobra exceso: con factor 3,
     * las prendas 1-3 van a 0, las 4-6 a x1, las 7-9 a x2.
     *
     * fetchDataAndSync escribe null cuando 360Sys omite la clave, así que se
     * normaliza con ?? 0 en vez de asumir que la columna trae su default.
     */
    public function excessUnitsFor(int $quantity): int
    {
        $factor = (int) ($this->excessFactor ?? 0);

        if ($quantity <= 0 || $factor <= 0 || (float) ($this->excess ?? 0) <= 0) {
            return 0;
        }

        return (int) floor(($quantity - 1) / $factor);
    }

    /** Monto de exceso a sumar al costo base de envío. */
    public function excessAmountFor(int $quantity): float
    {
        return round($this->excessUnitsFor($quantity) * (float) ($this->excess ?? 0), 2);
    }

    /** Costo de envío final: base + exceso. */
    public function shippingCostFor(int $quantity): float
    {
        return round((float) ($this->sendCost ?? 0) + $this->excessAmountFor($quantity), 2);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function send_information()
    {
        return $this->hasOne(SendInformation::class);
    }
}
