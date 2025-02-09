<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\Api360Service;

class SincronizarDatos360 extends Command
{
    protected $signature = 'sincronizar:datos360';
    protected $description = 'Sincroniza los datos 360 de la plataforma.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Sincronización 360 iniciando...');
        Log::info('Sincronización 360 iniciada.');

        try {
            // Obtiene la instancia del servicio sin inyectarlo en el constructor
            $api360Service = app(Api360Service::class);
            $api360Service->sincronizarDatos360();

            $this->info('Sincronización 360 completada correctamente.');
            Log::info('Sincronización 360 completada.');
        } catch (Throwable $e) {
            $this->error('Error en la sincronización: ' . $e->getMessage());
            Log::error('Error en la sincronización 360', ['exception' => $e]);
        }
    }
}
