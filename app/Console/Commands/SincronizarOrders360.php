<?php
namespace App\Console\Commands;

use App\Services\OrderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SincronizarOrders360 extends Command
{
    protected $signature = 'sincronizar:ordenes360 
                            {uuid : Identificador único de la app} 
                            {--start= : Fecha de inicio en formato YYYY-MM-DD} 
                            {--end= : Fecha de fin en formato YYYY-MM-DD}';

    protected $description = 'Sincroniza las órdenes desde la plataforma 360.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
  
        // Obtener el UUID y fechas desde la entrada
        $uuid  = $this->argument('uuid');
        $start = $this->option('start') ?? now()->subDay()->toDateString(); // ayer
        $end   = $this->option('end') ?? now()->toDateString();             // hoy

        // Mostrar información al usuario en consola
        $this->info("🚀 Iniciando sincronización de órdenes desde 360");
        $this->info("🔑 UUID: {$uuid}");
        $this->info("📅 Rango: desde {$start} hasta {$end}");

        // Registrar en logs
        Log::info('Sincronización de órdenes 360 iniciada.', compact('uuid', 'start', 'end'));

        try {
            // Ejecutar el servicio de sincronización
            $orderService = app(OrderService::class);
            $orderService->sincronizarOrders360($uuid, $start, $end);

            $this->info('✅ Sincronización completada exitosamente.');
            Log::info('Sincronización de órdenes 360 completada.', ['uuid' => $uuid]);
        } catch (Throwable $e) {
            $this->error('❌ Error en la sincronización: ' . $e->getMessage());
            Log::error('Error durante la sincronización de órdenes 360.', [
                'exception' => $e,
                'uuid'      => $uuid,
            ]);
        }
    }

}
