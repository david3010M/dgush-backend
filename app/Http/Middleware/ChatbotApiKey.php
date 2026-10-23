<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autentica al agente de WhatsApp contra CHATBOT_API_KEY (header X-API-Key).
 * Sin key configurada rechaza todo: nunca queda abierta por olvido.
 */
class ChatbotApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('chatbot.api_key');
        $given = (string) $request->header('X-API-Key');

        if ($expected === '' || ! hash_equals($expected, $given)) {
            return response()->json(['error' => 'no_autorizado'], 401);
        }

        return $next($request);
    }
}
