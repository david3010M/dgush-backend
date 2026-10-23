<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Requests\Chatbot\ProductColorRequest;
use App\Http\Requests\Chatbot\ProductIdRequest;
use App\Http\Requests\Chatbot\ProductSizeRequest;
use App\Http\Requests\Chatbot\VarianteRequest;
use App\Http\Requests\Chatbot\VerificarStockRequest;
use App\Services\Chatbot\DisponibilidadService;
use Illuminate\Http\JsonResponse;

class DisponibilidadController extends ChatbotController
{
    public function __construct(private DisponibilidadService $service)
    {
    }

    public function verificar(VarianteRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond($this->service->verificar($data['product_id'], $data['color_id'], $data['size_id']));
    }

    public function tallasProducto(ProductIdRequest $request): JsonResponse
    {
        return $this->respond(['tallas' => $this->service->tallasProducto($request->validated('product_id'))]);
    }

    public function tallasDeColor(ProductColorRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond(['tallas' => $this->service->tallasDeColor($data['product_id'], $data['color_id'])]);
    }

    public function coloresDeTalla(ProductSizeRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond(['colores' => $this->service->coloresDeTalla($data['product_id'], $data['size_id'])]);
    }

    /** #22 /carrito/verificar-stock: misma tabla, se sirve desde aquí. */
    public function verificarStock(VerificarStockRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond($this->service->verificarStock(
            $data['product_id'],
            $data['color_id'],
            $data['size_id'],
            $data['quantity']
        ));
    }
}
