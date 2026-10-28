<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Requests\Chatbot\LiberarStockRequest;
use App\Http\Requests\Chatbot\ReservarStockRequest;
use App\Services\Chatbot\StockService;
use Illuminate\Http\JsonResponse;

class StockController extends ChatbotController
{
    public function __construct(private StockService $service)
    {
    }

    public function reservar(ReservarStockRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond($this->service->reservar($data['Idempotency-Key'], $data['referencia'], $data['items']));
    }

    public function liberar(LiberarStockRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond($this->service->liberar(
            $data['Idempotency-Key'],
            $data['referencia'],
            $data['items'] ?? [],
            $data['motivo'] ?? 'liberada'
        ));
    }
}
