<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Requests\Chatbot\AlternativasRequest;
use App\Http\Requests\Chatbot\IdsRequest;
use App\Http\Requests\Chatbot\MultifiltroRequest;
use App\Http\Requests\Chatbot\PalabrasRequest;
use App\Http\Requests\Chatbot\ProductIdRequest;
use App\Http\Requests\Chatbot\SubcategoriaRequest;
use App\Http\Requests\Chatbot\TerminoRequest;
use App\Services\Chatbot\CatalogoService;
use Illuminate\Http\JsonResponse;

class CatalogoController extends ChatbotController
{
    public function __construct(private CatalogoService $service)
    {
    }

    public function buscarCategoria(SubcategoriaRequest $request): JsonResponse
    {
        return $this->respond(['productos' => $this->service->buscarCategoria($request->validated('subcategoria'))]);
    }

    public function buscarNombre(TerminoRequest $request): JsonResponse
    {
        return $this->respond(['productos' => $this->service->buscarNombre($request->validated('termino'))]);
    }

    public function buscarPalabras(PalabrasRequest $request): JsonResponse
    {
        return $this->respond(['posibles_coincidencias' => $this->service->buscarPalabras($request->validated('palabras'))]);
    }

    public function variantes(IdsRequest $request): JsonResponse
    {
        return $this->respond(['variantes' => $this->service->variantes($request->validated('ids'))]);
    }

    public function multifiltro(MultifiltroRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond(['productos' => $this->service->multifiltro(
            $data['subcategoria'],
            $data['color'] ?? null,
            $data['talla'] ?? null
        )]);
    }

    public function liquidacion(): JsonResponse
    {
        return $this->respond(['productos' => $this->service->liquidacion()]);
    }

    public function fotos(ProductIdRequest $request): JsonResponse
    {
        return $this->respond(['fotos_adicionales' => $this->service->fotos($request->validated('product_id'))]);
    }

    public function alternativas(AlternativasRequest $request): JsonResponse
    {
        $data = $request->validated();

        return $this->respond(['alternativas' => $this->service->alternativas(
            $data['subcats_grupo'],
            $data['subcat_original'],
            $data['ids_excluir'] ?? []
        )]);
    }
}
