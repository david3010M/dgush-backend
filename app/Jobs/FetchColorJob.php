<?php

namespace App\Jobs;

use App\Services\Api360Service;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable; // 🔹 Importar Batchable
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchColorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable; // 🔹 Agregar Batchable

    protected $api360Service;

    public function __construct()
    {
        $this->api360Service = app(Api360Service::class);
    }

    public function handle()
    {
        $this->api360Service->fetch_color();
    }
}
