<?php

namespace App\Services\Chatbot;

class DespachoService extends ChatbotService
{
    /**
     * D1: ids locales de producto/color/talla → server_id de 360Sys.
     * Sin filtro de visibilidad: es traducción, el pedido ya fue armado.
     * Un id inexistente o sin server_id devuelve null y resuelto false; nunca se usa el id local.
     */
    public function serverIds(array $items): array
    {
        $mapas = [];
        foreach (['product' => 'product_id', 'color' => 'color_id', 'size' => 'size_id'] as $tabla => $campo) {
            $ids = array_values(array_unique(array_column($items, $campo)));
            $rows = $this->select("SELECT id, server_id FROM {$tabla}
                WHERE id IN ({$this->placeholders($ids)}) AND deleted_at IS NULL", $ids);
            $mapas[$campo] = array_column($this->cast($rows, ['server_id' => 'server_id']), 'server_id', 'id');
        }

        return array_map(function (array $item) use ($mapas) {
            $fila = [];
            foreach (['product_id', 'color_id', 'size_id'] as $campo) {
                $fila[$campo] = (int) $item[$campo];
                $fila[str_replace('_id', '_server_id', $campo)] = $mapas[$campo][$item[$campo]] ?? null;
            }
            $fila['resuelto'] = ! in_array(null, $fila, true);

            return $fila;
        }, $items);
    }
}
