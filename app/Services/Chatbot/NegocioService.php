<?php

namespace App\Services\Chatbot;

use App\Models\District;

class NegocioService extends ChatbotService
{
    /** #17 Sedes físicas. */
    public function sedes(): array
    {
        return $this->select("SELECT s.name, s.address, s.phone, s.email, s.brand_name, d.name AS distrito
            FROM sedes s
            LEFT JOIN district d ON s.district_id = d.id AND d.deleted_at IS NULL
            WHERE s.deleted_at IS NULL
            ORDER BY s.id ASC");
    }

    /** #18 Zonas de delivery local (status es string '1' en BD). */
    public function zonasDelivery(): array
    {
        $rows = $this->select("SELECT name AS zona, sendCost AS costo_envio
            FROM zones
            WHERE deleted_at IS NULL AND status = '1'
            ORDER BY name ASC");

        return $this->cast($rows, ['costo_envio' => 'float']);
    }

    /**
     * #19 Costo de envío: zonas locales y luego distritos nacionales.
     *
     * Igual que el checkout: solo distritos con cobertura (status 1 y sendCost > 0,
     * ver District::toArray 'available') y, con SHIPPING_DISTRICT_ENABLED=false,
     * costo y exceso en 0. exceso / exceso_factor: se suma `exceso` por cada
     * `exceso_factor` prendas a partir de la primera (District::excessUnitsFor).
     */
    public function costoEnvio(string $ciudad): array
    {
        $rows = $this->select("SELECT 'zona_local' AS tipo, name AS lugar, sendCost AS costo,
                0 AS exceso, 0 AS exceso_factor
            FROM zones
            WHERE name LIKE CONCAT('%', ?, '%') AND deleted_at IS NULL AND status = '1'
            UNION ALL
            SELECT 'envio_nacional', CONCAT(d.name, ', ', p.name, ', ', dep.name), d.sendCost,
                COALESCE(d.excess, 0), COALESCE(d.excessFactor, 0)
            FROM district d
            JOIN province p ON d.province_id = p.id AND p.deleted_at IS NULL
            JOIN department dep ON p.department_id = dep.id AND dep.deleted_at IS NULL
            WHERE d.name LIKE CONCAT('%', ?, '%') AND d.deleted_at IS NULL
              AND d.status = '1' AND d.sendCost > 0
            ORDER BY costo ASC
            LIMIT 5", [$ciudad, $ciudad]);

        $rows = $this->cast($rows, ['costo' => 'float', 'exceso' => 'float', 'exceso_factor' => 'int']);

        if (District::shippingEnabled()) {
            return $rows;
        }

        return array_map(fn (array $row) => $row['tipo'] === 'envio_nacional'
            ? array_merge($row, ['costo' => 0.0, 'exceso' => 0.0, 'exceso_factor' => 0])
            : $row, $rows);
    }
}
