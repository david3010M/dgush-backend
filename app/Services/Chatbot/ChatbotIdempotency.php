<?php

namespace App\Services\Chatbot;

use Closure;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Header Idempotency-Key de las escrituras del chatbot: la misma key devuelve la
 * respuesta guardada sin volver a aplicar el cambio.
 *
 * La key se inserta en la MISMA transacción que la escritura. Con dos reintentos
 * simultáneos, el UNIQUE hace esperar al segundo hasta que el primero confirme;
 * entonces choca y devuelve la respuesta ya guardada. Si el primero falla y hace
 * rollback, la key no queda registrada y el segundo procesa normal.
 */
class ChatbotIdempotency
{
    private const TABLE = 'chatbot_idempotency_keys';

    /** @throws ConflictHttpException misma key con otro body (409 conflicto_idempotencia). */
    public function run(string $key, string $endpoint, array $payload, Closure $action): array
    {
        $hash = hash('sha256', json_encode($payload));

        try {
            return DB::transaction(function () use ($key, $endpoint, $hash, $action) {
                DB::table(self::TABLE)->insert([
                    'idempotency_key' => $key,
                    'endpoint' => $endpoint,
                    'request_hash' => $hash,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $response = $action();

                DB::table(self::TABLE)
                    ->where(['idempotency_key' => $key, 'endpoint' => $endpoint])
                    ->update(['response' => json_encode($response, JSON_PRESERVE_ZERO_FRACTION), 'updated_at' => now()]);

                return $response;
            });
        } catch (UniqueConstraintViolationException) {
            $stored = DB::table(self::TABLE)->where(['idempotency_key' => $key, 'endpoint' => $endpoint])->first();

            if ($stored->request_hash !== $hash) {
                throw new ConflictHttpException('Idempotency-Key usada con otro body');
            }

            return json_decode($stored->response, true);
        }
    }
}
