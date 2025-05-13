<?php

namespace App\Jobs;

use App\Services\OrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable; // 🔹 Importar Batchable
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchSincronizarOrdenesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable; // 🔹 Agregar Batchable

    protected $ordenService;
    protected string $uuid;
    protected string $start;
    protected string $end;

    public function __construct(string $uuid, string $start, string $end)
    {
        $this->uuid = $uuid;
        $this->start = $start;
        $this->end = $end;
    }

    public function handle(): void
    {
        $ordenService = app(OrderService::class);
        $ordenService->callListarPedidos360($this->uuid, $this->start, $this->end);
    }
}
