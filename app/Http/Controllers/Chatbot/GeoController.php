<?php

namespace App\Http\Controllers\Chatbot;

use App\Services\Chatbot\GeoService;
use Illuminate\Http\JsonResponse;

class GeoController extends ChatbotController
{
    public function __construct(private GeoService $service)
    {
    }

    public function departamentos(): JsonResponse
    {
        return $this->respond(['departamentos' => $this->service->departamentos()]);
    }

    public function provincias(): JsonResponse
    {
        return $this->respond(['provincias' => $this->service->provincias()]);
    }

    public function distritos(): JsonResponse
    {
        return $this->respond(['distritos' => $this->service->distritos()]);
    }

    public function zonas(): JsonResponse
    {
        return $this->respond(['zonas' => $this->service->zonas()]);
    }

    public function sedes(): JsonResponse
    {
        return $this->respond(['sedes' => $this->service->sedes()]);
    }
}
