<?php

namespace App\Services\Chatbot;

use App\Services\StockReservationService;
use Illuminate\Support\Facades\DB;

/**
 * Base de las consultas del chatbot (AGENTS/DGush_API_Especificacion_Tecnica.md).
 *
 * Solo SELECT y siempre con bindings. El SQL se mantiene cercano al del
 * contrato para que el cliente pueda auditarlo; los ajustes acordados son:
 * - Productos: solo status_server = 1, igual que el catálogo de la tienda.
 * - Cupones: beneficio_display según los tipos reales (percentage/discount).
 * - Envío: cobertura y SHIPPING_DISTRICT_ENABLED, igual que el checkout.
 * - Stock: siempre el disponible (descuenta reservas del chatbot, ver StockReservationService).
 */
abstract class ChatbotService
{
    /** Primera imagen vigente del producto (la principal). */
    protected const IMAGEN_PRINCIPAL = "(SELECT i.url FROM image i WHERE i.product_id = p.id AND i.deleted_at IS NULL
        ORDER BY i.id ASC LIMIT 1)";

    /** Stock disponible de la variante pd: stock de 360Sys menos reservas vigentes. */
    protected function disponible(): string
    {
        return StockReservationService::availableSql('pd');
    }

    /** El producto p tiene alguna variante disponible: con stock, activa y vigente. */
    protected function conStock(): string
    {
        return "EXISTS (SELECT 1 FROM product_details pd
            WHERE pd.product_id = p.id AND pd.status = 1 AND pd.deleted_at IS NULL
              AND {$this->disponible()} > 0)";
    }

    /** Producto visible en la tienda. */
    protected const PRODUCTO_VISIBLE = "p.deleted_at IS NULL AND p.status_server = 1";

    protected const PRECIOS = "p.price1, p.price2, p.price12, p.priceLiquidacion, p.liquidacion";

    protected const CAST_PRECIOS = [
        'price1' => 'float',
        'price2' => 'float',
        'price12' => 'float',
        'priceOferta' => 'float',
        'priceLiquidacion' => 'float',
        'liquidacion' => 'int',
    ];

    protected function select(string $sql, array $bindings = []): array
    {
        return array_map(fn ($row) => (array) $row, DB::select($sql, $bindings));
    }

    /** "?,?,?" para un IN (...) parametrizado. */
    protected function placeholders(array $values): string
    {
        return implode(',', array_fill(0, count($values), '?'));
    }

    /**
     * PDO trae DECIMAL como string; el contrato pide números. Se respeta null.
     * Las claves de $casts ausentes en la fila se ignoran.
     */
    protected function cast(array $rows, array $casts): array
    {
        return array_map(function (array $row) use ($casts) {
            foreach (array_intersect_key($casts, $row) as $key => $type) {
                if ($row[$key] !== null) {
                    $row[$key] = $type === 'float' ? (float) $row[$key] : (int) $row[$key];
                }
            }

            return $row;
        }, $rows);
    }

    protected function without(array $rows, string ...$keys): array
    {
        return array_map(fn (array $row) => array_diff_key($row, array_flip($keys)), $rows);
    }
}
