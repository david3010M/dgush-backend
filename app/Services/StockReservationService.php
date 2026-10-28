<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Stock disponible = stock de 360Sys - reservas vigentes del chatbot, nunca negativo.
 * Lo usan la API del chatbot y la web (detalle de producto y checkout).
 * Tabla stock_reservations: ver su migración.
 */
class StockReservationService
{
    /** SQL: cantidad reservada vigente de la variante de la fila $alias (product_details). */
    public static function reservedSql(string $alias): string
    {
        return "(SELECT COALESCE(SUM(sr.quantity), 0) FROM stock_reservations sr
            WHERE sr.product_id = {$alias}.product_id AND sr.color_id = {$alias}.color_id
              AND sr.size_id = {$alias}.size_id
              AND sr.status = 'activa' AND sr.expires_at_utc > UTC_TIMESTAMP())";
    }

    /** SQL: stock disponible de la fila $alias (product_details). */
    public static function availableSql(string $alias): string
    {
        return "GREATEST({$alias}.stock - " . self::reservedSql($alias) . ", 0)";
    }

    public function reservedFor(int $productId, int $colorId, int $sizeId): float
    {
        return (float) DB::table('stock_reservations')
            ->where(['product_id' => $productId, 'color_id' => $colorId, 'size_id' => $sizeId, 'status' => 'activa'])
            ->whereRaw('expires_at_utc > UTC_TIMESTAMP()')
            ->sum('quantity');
    }

    /** $detail: fila de product_details (modelo u objeto con product_id, color_id, size_id, stock). */
    public function availableFor(object $detail): float
    {
        $reserved = $this->reservedFor($detail->product_id, $detail->color_id, $detail->size_id);

        return max((float) $detail->stock - $reserved, 0);
    }
}
