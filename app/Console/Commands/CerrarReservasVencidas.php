<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Mantenimiento de las reservas del chatbot. Una reserva vencida ya no descuenta
 * stock aunque este comando no corra (se filtra por expires_at_utc); aquí solo se
 * marca como 'vencida' y se purgan las Idempotency-Key viejas.
 */
class CerrarReservasVencidas extends Command
{
    protected $signature = 'chatbot:cerrar-reservas-vencidas {--dias-idempotencia=30}';
    protected $description = 'Marca como vencidas las reservas de stock del chatbot y purga Idempotency-Key antiguas.';

    public function handle(): int
    {
        $vencidas = DB::table('stock_reservations')
            ->where('status', 'activa')
            ->whereRaw('expires_at_utc <= UTC_TIMESTAMP()')
            ->update(['status' => 'vencida', 'closed_at' => now(), 'updated_at' => now()]);

        $purgadas = DB::table('chatbot_idempotency_keys')
            ->where('created_at', '<', now()->subDays((int) $this->option('dias-idempotencia')))
            ->delete();

        $this->info("Reservas vencidas: {$vencidas}. Idempotency-Key purgadas: {$purgadas}.");

        return self::SUCCESS;
    }
}
