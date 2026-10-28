<?php

namespace App\Services\Chatbot;

use App\Models\District;

/** D4–D8: volcados completos para el caché diario de geografía del agente (id local + server_id). */
class GeoService extends ChatbotService
{
    private const IDS = ['id' => 'int', 'server_id' => 'server_id'];

    /** D4 */
    public function departamentos(): array
    {
        $rows = $this->select("SELECT id, server_id, name, location_code
            FROM department WHERE deleted_at IS NULL ORDER BY id");

        return $this->cast($rows, self::IDS);
    }

    /** D5 */
    public function provincias(): array
    {
        $rows = $this->select("SELECT p.id, p.server_id, p.name, p.location_code,
                p.department_id AS departamento_id, dep.server_id AS departamento_server_id
            FROM province p
            JOIN department dep ON p.department_id = dep.id AND dep.deleted_at IS NULL
            WHERE p.deleted_at IS NULL
            ORDER BY p.id");

        return $this->cast($rows, self::IDS + ['departamento_id' => 'int', 'departamento_server_id' => 'server_id']);
    }

    /**
     * D6. En district el ubigeo está en la columna ubigeo (no location_code).
     * available = cobertura real (status 1 y sendCost > 0), igual que /api/district.
     * Con SHIPPING_DISTRICT_ENABLED=false los costos salen en 0 (como el checkout):
     * send_cost 0 deja de significar "sin cobertura"; para eso está available.
     */
    public function distritos(): array
    {
        $rows = $this->select("SELECT d.id, d.server_id, d.name, d.ubigeo AS location_code,
                d.sendCost AS send_cost, COALESCE(d.excess, 0) AS excess,
                COALESCE(d.excessFactor, 0) AS excess_factor,
                CASE WHEN d.status = '1' THEN 1 ELSE 0 END AS status,
                CASE WHEN d.status = '1' AND d.sendCost > 0 THEN 1 ELSE 0 END AS available,
                p.id AS provincia_id, p.server_id AS provincia_server_id, p.name AS provincia_name,
                dep.id AS departamento_id, dep.server_id AS departamento_server_id, dep.name AS departamento_name
            FROM district d
            JOIN province p ON d.province_id = p.id AND p.deleted_at IS NULL
            JOIN department dep ON p.department_id = dep.id AND dep.deleted_at IS NULL
            WHERE d.deleted_at IS NULL
            ORDER BY d.id");

        $rows = $this->cast($rows, self::IDS + [
            'send_cost' => 'float', 'excess' => 'float', 'excess_factor' => 'int', 'status' => 'int', 'available' => 'int',
            'provincia_id' => 'int', 'provincia_server_id' => 'server_id',
            'departamento_id' => 'int', 'departamento_server_id' => 'server_id',
        ]);

        if (District::shippingEnabled()) {
            return $rows;
        }

        return array_map(fn (array $row) => array_merge($row, ['send_cost' => 0.0, 'excess' => 0.0, 'excess_factor' => 0]), $rows);
    }

    /** D7. status es string '1' en BD: se devuelve entero. */
    public function zonas(): array
    {
        $rows = $this->select("SELECT id, server_id, name, sendCost AS price,
                CASE WHEN status = '1' THEN 1 ELSE 0 END AS status
            FROM zones WHERE deleted_at IS NULL ORDER BY id");

        return $this->cast($rows, self::IDS + ['price' => 'float', 'status' => 'int']);
    }

    /** D8. Recojo sin costo; server_id = branch_id de 360Sys. */
    public function sedes(): array
    {
        $rows = $this->select("SELECT id, server_id, brand_name, name, ruc, address, district_id
            FROM sedes WHERE deleted_at IS NULL ORDER BY id");

        return $this->cast($rows, self::IDS + ['district_id' => 'int']);
    }
}
