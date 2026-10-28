<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Errores de /api/chatbot: el agente siempre recibe JSON, nunca HTML,
 * stack trace ni el mensaje de MySQL. El error se sigue reportando al log.
 */
class ChatbotErrorRenderer
{
    public static function render(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => 'parametro_invalido',
                'detalle' => self::detalle($e),
            ], 400);
        }

        if ($e instanceof HttpExceptionInterface) {
            $error = match ($e->getStatusCode()) {
                401 => 'no_autorizado',
                404 => 'no_encontrado',
                405 => 'metodo_no_permitido',
                409 => 'conflicto_idempotencia',
                429 => 'demasiadas_solicitudes',
                default => 'error_http',
            };

            return response()->json(['error' => $error], $e->getStatusCode(), $e->getHeaders());
        }

        return response()->json(['error' => 'error_interno'], 500);
    }

    /** Primer campo que falló: "falta product_id" o "ids.0 invalido". */
    private static function detalle(ValidationException $e): string
    {
        $failed = $e->validator->failed();
        $field = array_key_first($failed);

        if ($field === null) {
            return 'parametros invalidos';
        }

        return array_key_exists('Required', $failed[$field])
            ? "falta {$field}"
            : "{$field} invalido";
    }
}
