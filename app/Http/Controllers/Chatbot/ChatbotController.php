<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ChatbotController extends Controller
{
    /** JSON UTF-8 con tildes y ñ legibles, URLs sin escapar y precios como decimal (65.0). */
    protected function respond(array $data): JsonResponse
    {
        return response()->json(
            $data,
            200,
            ['Content-Type' => 'application/json; charset=utf-8'],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION
        );
    }
}
