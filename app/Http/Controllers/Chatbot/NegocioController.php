<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Requests\Chatbot\CiudadRequest;
use App\Services\Chatbot\NegocioService;
use Illuminate\Http\JsonResponse;

class NegocioController extends ChatbotController
{
    public function __construct(private NegocioService $service)
    {
    }

    public function sedes(): JsonResponse
    {
        return $this->respond(['sedes' => $this->service->sedes()]);
    }

    public function zonasDelivery(): JsonResponse
    {
        return $this->respond(['zonas' => $this->service->zonasDelivery()]);
    }

    public function costoEnvio(CiudadRequest $request): JsonResponse
    {
        return $this->respond(['resultados' => $this->service->costoEnvio($request->validated('ciudad'))]);
    }
}
