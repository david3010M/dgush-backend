<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\Api360Service;

class SincronizarDatos360 extends Command
{
     // 🔹 Agregar el argumento uuid en la firma
     protected $signature = 'sincronizar:datos360 {uuid}';
     protected $description = 'Sincroniza los datos 360 de la plataforma.';
 
     public function __construct()
     {
         parent::__construct();
     }
 
     public function handle()
     {
         $uuid = $this->argument('uuid'); // 🔹 Obtener el UUID
         $this->info("Sincronización 360 iniciando con UUID: {$uuid}");
         Log::info('Sincronización 360 iniciada.', ['uuid' => $uuid]);

         try {
             $api360Service = app(Api360Service::class);
             $api360Service->sincronizarDatos360($uuid); // 🔹 Pasar el UUID si es necesario
 
             $this->info('Sincronización 360 completada correctamente.');
             Log::info('Sincronización 360 completada.', ['uuid' => $uuid]);
         } catch (Throwable $e) {
             $this->error('Error en la sincronización: ' . $e->getMessage());
             Log::error('Error en la sincronización 360', [
                 'exception' => $e,
                 'uuid' => $uuid
             ]);
         }
     }
}
