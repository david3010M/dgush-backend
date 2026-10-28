<?php

namespace App\Services\Chatbot;

/** stock en todas las respuestas = disponible (stock de 360Sys - reservas vigentes). */
class DisponibilidadService extends ChatbotService
{
    private const VARIANTE = "FROM product_details pd
        WHERE pd.product_id = ? AND pd.color_id = ? AND pd.size_id = ? AND pd.deleted_at IS NULL";

    /** #9 ¿La combinación exacta tiene stock? */
    public function verificar(int $productId, int $colorId, int $sizeId): array
    {
        $row = $this->select("SELECT {$this->disponible()} AS stock,
                CASE WHEN {$this->disponible()} > 0 AND pd.status = 1 THEN 1 ELSE 0 END AS disponible
            " . self::VARIANTE . "
            ORDER BY disponible DESC, stock DESC
            LIMIT 1", [$productId, $colorId, $sizeId])[0] ?? null;

        return [
            'disponible' => (int) ($row['disponible'] ?? 0),
            'stock' => (int) ($row['stock'] ?? 0),
        ];
    }

    /** #22 ¿Alcanza el stock para la cantidad pedida? */
    public function verificarStock(int $productId, int $colorId, int $sizeId, int $quantity): array
    {
        $row = $this->select("SELECT {$this->disponible()} AS stock,
                CASE WHEN {$this->disponible()} >= ? AND pd.status = 1 THEN 1 ELSE 0 END AS suficiente
            " . self::VARIANTE . "
            ORDER BY suficiente DESC, stock DESC
            LIMIT 1", [$quantity, $productId, $colorId, $sizeId])[0] ?? null;

        return [
            'suficiente' => (int) ($row['suficiente'] ?? 0),
            'stock' => (int) ($row['stock'] ?? 0),
        ];
    }

    /** #10 Todas las tallas del producto con su stock disponible. */
    public function tallasProducto(int $productId): array
    {
        $rows = $this->select("SELECT sz.id AS size_id, sz.name AS talla,
                COALESCE(SUM(CASE WHEN pd.status = 1 THEN {$this->disponible()} ELSE 0 END), 0) AS stock_disponible,
                CASE WHEN SUM(CASE WHEN {$this->disponible()} > 0 AND pd.status = 1 THEN 1 ELSE 0 END) > 0 THEN 1 ELSE 0 END AS tiene_stock
            FROM product_details pd
            JOIN size sz ON pd.size_id = sz.id AND sz.deleted_at IS NULL
            WHERE pd.product_id = ? AND pd.deleted_at IS NULL
            GROUP BY sz.id, sz.name
            ORDER BY tiene_stock DESC, sz.name ASC", [$productId]);

        return $this->cast($rows, ['stock_disponible' => 'int', 'tiene_stock' => 'int']);
    }

    /** #11 Tallas de un color. */
    public function tallasDeColor(int $productId, int $colorId): array
    {
        $rows = $this->select("SELECT sz.id AS size_id, sz.name AS talla, {$this->disponible()} AS stock,
                CASE WHEN {$this->disponible()} > 0 AND pd.status = 1 THEN 1 ELSE 0 END AS disponible
            FROM product_details pd
            JOIN size sz ON pd.size_id = sz.id AND sz.deleted_at IS NULL
            WHERE pd.product_id = ? AND pd.color_id = ? AND pd.deleted_at IS NULL
            ORDER BY disponible DESC, sz.name ASC", [$productId, $colorId]);

        return $this->cast($rows, ['stock' => 'int', 'disponible' => 'int']);
    }

    /** #12 Colores de una talla. */
    public function coloresDeTalla(int $productId, int $sizeId): array
    {
        $rows = $this->select("SELECT col.id AS color_id, col.name AS color, col.hex, {$this->disponible()} AS stock,
                CASE WHEN {$this->disponible()} > 0 AND pd.status = 1 THEN 1 ELSE 0 END AS disponible
            FROM product_details pd
            JOIN color col ON pd.color_id = col.id AND col.deleted_at IS NULL
            WHERE pd.product_id = ? AND pd.size_id = ? AND pd.deleted_at IS NULL
            ORDER BY disponible DESC, col.name ASC", [$productId, $sizeId]);

        return $this->cast($rows, ['stock' => 'int', 'disponible' => 'int']);
    }
}
