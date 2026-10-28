<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Requests\Chatbot\ServerIdsRequest;
use App\Services\Chatbot\DespachoService;
use Illuminate\Http\JsonResponse;

class DespachoController extends ChatbotController
{
    public function __construct(private DespachoService $service)
    {
    }

    public function serverIds(ServerIdsRequest $request): JsonResponse
    {
        return $this->respond(['items' => $this->service->serverIds($request->validated('items'))]);
    }
}
