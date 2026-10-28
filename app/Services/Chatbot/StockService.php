<?php

namespace App\Services\Chatbot;

use App\Services\StockReservationService;
use Illuminate\Support\Facades\DB;

/**
 * D2/D3: reservas de stock del chatbot, solo a nivel de tienda virtual.
 * Nunca escriben product_details.stock (lo controla 360Sys): solo stock_reservations.
 */
class StockService
{
    public function __construct(
        private ChatbotIdempotency $idempotency,
        private StockReservationService $reservations,
    ) {
    }

    /** D2: todo o nada. Sin stock suficiente es 200 con reservado false, no un error. */
    public function reservar(string $key, string $referencia, array $items): array
    {
        return $this->idempotency->run($key, 'stock/reservar', compact('referencia', 'items'),
            fn () => $this->aplicarReserva($referencia, $this->agrupar($items)));
    }

    /** D3: cierra las reservas activas de la referencia (o solo de las variantes indicadas). */
    public function liberar(string $key, string $referencia, array $items, string $motivo): array
    {
        return $this->idempotency->run($key, 'stock/liberar', compact('referencia', 'items', 'motivo'),
            fn () => $this->aplicarLiberacion($referencia, $items, $motivo));
    }

    private function aplicarReserva(string $referencia, array $items): array
    {
        // Bloquea las variantes en orden fijo (evita deadlocks): dos reservas de la
        // misma variante se serializan y la segunda ve la reserva de la primera.
        $ordenados = collect($items)->sortBy(fn ($i) => [$i['product_id'], $i['color_id'], $i['size_id']]);
        $disponibles = [];
        foreach ($ordenados as $key => $item) {
            $disponibles[$key] = $this->disponible($item, bloquear: true);
        }

        $suficiente = collect($items)->every(fn ($item, $key) => $disponibles[$key] >= $item['quantity']);

        if (! $suficiente) {
            return ['reservado' => false, 'referencia' => $referencia, 'items' => array_values(array_map(
                fn ($item, $key) => $item + ['stock_disponible' => $disponibles[$key], 'suficiente' => $disponibles[$key] >= $item['quantity']],
                $items, array_keys($items)
            ))];
        }

        $expira = now()->utc()->addHours(config('chatbot.reserva_horas'));
        DB::table('stock_reservations')->insert(array_map(fn ($item) => $item + [
            'reference' => $referencia,
            'status' => 'activa',
            'expires_at_utc' => $expira,
            'created_at' => now(),
            'updated_at' => now(),
        ], array_values($items)));

        return [
            'reservado' => true,
            'referencia' => $referencia,
            'expira_en' => $expira->copy()->setTimezone(config('app.timezone'))->toDateTimeString(),
            'items' => array_values(array_map(
                fn ($item, $key) => $item + ['stock_restante' => $disponibles[$key] - $item['quantity']],
                $items, array_keys($items)
            )),
        ];
    }

    /** Stock disponible de una variante activa; $bloquear deja la fila de product_details con FOR UPDATE. */
    private function disponible(array $variante, bool $bloquear = false): int
    {
        $query = DB::table('product_details')
            ->where([
                'product_id' => $variante['product_id'],
                'color_id' => $variante['color_id'],
                'size_id' => $variante['size_id'],
                'status' => 1,
            ])
            ->whereNull('deleted_at')
            ->orderByDesc('stock');

        $detail = ($bloquear ? $query->lockForUpdate() : $query)->first();

        return $detail ? (int) $this->reservations->availableFor($detail) : 0;
    }

    private function aplicarLiberacion(string $referencia, array $items, string $motivo): array
    {
        $query = DB::table('stock_reservations')->where(['reference' => $referencia, 'status' => 'activa']);

        if ($items) {
            $query->where(function ($q) use ($items) {
                foreach ($items as $item) {
                    $q->orWhere(fn ($v) => $v->where([
                        'product_id' => $item['product_id'], 'color_id' => $item['color_id'], 'size_id' => $item['size_id'],
                    ]));
                }
            });
        }

        $filas = $query->lockForUpdate()->get(['id', 'product_id', 'color_id', 'size_id', 'quantity']);

        DB::table('stock_reservations')->whereIn('id', $filas->pluck('id'))
            ->update(['status' => $motivo, 'closed_at' => now(), 'updated_at' => now()]);

        return [
            'liberado' => $filas->isNotEmpty(),
            'referencia' => $referencia,
            'motivo' => $motivo,
            'items' => $filas->map(fn ($fila) => [
                'product_id' => $fila->product_id,
                'color_id' => $fila->color_id,
                'size_id' => $fila->size_id,
                'quantity' => $fila->quantity,
                'stock_restante' => $this->disponible((array) $fila),
            ])->values()->all(),
        ];
    }

    /** Suma cantidades si el carrito repite la misma variante; conserva el orden. */
    private function agrupar(array $items): array
    {
        $agrupados = [];
        foreach ($items as $item) {
            $key = "{$item['product_id']}:{$item['color_id']}:{$item['size_id']}";
            $agrupados[$key] ??= [
                'product_id' => (int) $item['product_id'],
                'color_id' => (int) $item['color_id'],
                'size_id' => (int) $item['size_id'],
                'quantity' => 0,
            ];
            $agrupados[$key]['quantity'] += (int) $item['quantity'];
        }

        return $agrupados;
    }
}
