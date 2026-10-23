<?php

namespace App\Services\Chatbot;

class PreciosService extends ChatbotService
{
    private const PRODUCTO = "SELECT p.id, p.name, p.price1, p.price2, p.price12, p.priceOferta,
            p.priceLiquidacion, p.liquidacion, p.currency
        FROM product p";

    private const CUPON = "name, code, type, indicator, value, description, expires_at";

    /** #13 Precios de un producto; null si no existe o no está visible. */
    public function producto(int $productId): ?array
    {
        $rows = $this->select(self::PRODUCTO . "
            WHERE p.id = ? AND " . self::PRODUCTO_VISIBLE, [$productId]);

        return $this->cast($rows, self::CAST_PRECIOS)[0] ?? null;
    }

    /** #14 Precios de varios productos. */
    public function carrito(array $ids): array
    {
        $rows = $this->select(self::PRODUCTO . "
            WHERE p.id IN ({$this->placeholders($ids)}) AND " . self::PRODUCTO_VISIBLE, array_values($ids));

        return $this->cast($rows, self::CAST_PRECIOS);
    }

    /** #15 / #21 Cupón por código; null si no existe. */
    public function validarCupon(string $codigo): ?array
    {
        // expires_at se guarda en la zona de la app: se compara contra now() de PHP, no NOW() de MySQL.
        $rows = $this->select("SELECT id, " . self::CUPON . ",
                CASE WHEN active = 1 AND expires_at > ? THEN 1 ELSE 0 END AS es_valido
            FROM coupon
            WHERE code = ? AND deleted_at IS NULL
            ORDER BY es_valido DESC, id DESC
            LIMIT 1", [now(), $codigo]);

        return $this->conBeneficio($this->cast($rows, ['id' => 'int', 'value' => 'float', 'es_valido' => 'int']))[0] ?? null;
    }

    /** #16 / #20 Cupones activos y no vencidos. */
    public function cuponesActivos(): array
    {
        $rows = $this->select("SELECT " . self::CUPON . "
            FROM coupon
            WHERE active = 1 AND expires_at > ? AND deleted_at IS NULL
            ORDER BY value DESC", [now()]);

        return $this->conBeneficio($this->cast($rows, ['value' => 'float']));
    }

    /**
     * Texto del beneficio según los tipos que usa OrderService:
     * percentage = % sobre indicator (subtotal/total/sendCost), discount = monto fijo en S/.
     */
    private function conBeneficio(array $cupones): array
    {
        return array_map(function (array $cupon) {
            $valor = fmod($cupon['value'], 1) == 0
                ? (string) (int) $cupon['value']
                : number_format($cupon['value'], 2, '.', '');

            $cupon['beneficio_display'] = match (true) {
                $cupon['type'] !== 'percentage' => "S/ {$valor} de descuento",
                $cupon['indicator'] !== 'sendCost' => "{$valor}% de descuento",
                $cupon['value'] >= 100 => 'Envío gratis',
                default => "{$valor}% de descuento en el envío",
            };

            return $cupon;
        }, $cupones);
    }
}
