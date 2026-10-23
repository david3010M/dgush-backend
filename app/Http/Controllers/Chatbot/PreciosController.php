<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Requests\Chatbot\CuponRequest;
use App\Http\Requests\Chatbot\IdsRequest;
use App\Http\Requests\Chatbot\ProductIdRequest;
use App\Services\Chatbot\PreciosService;
use Illuminate\Http\JsonResponse;

/** También sirve /negocio/cupones y /negocio/validar-cupon (alias del contrato). */
class PreciosController extends ChatbotController
{
    public function __construct(private PreciosService $service)
    {
    }

    public function producto(ProductIdRequest $request): JsonResponse
    {
        return $this->respond(['producto' => $this->service->producto($request->validated('product_id'))]);
    }

    public function carrito(IdsRequest $request): JsonResponse
    {
        return $this->respond(['productos' => $this->service->carrito($request->validated('ids'))]);
    }

    public function validarCupon(CuponRequest $request): JsonResponse
    {
        return $this->respond(['cupon' => $this->service->validarCupon($request->validated('codigo'))]);
    }

    public function cuponesActivos(): JsonResponse
    {
        return $this->respond(['cupones' => $this->service->cuponesActivos()]);
    }
}
