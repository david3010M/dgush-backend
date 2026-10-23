<?php

namespace App\Http\Requests\Chatbot;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validación del chatbot. Al fallar, ChatbotErrorRenderer responde 400
 * {"error": "parametro_invalido", "detalle": "falta product_id"}.
 */
abstract class ChatbotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // la autenticación va en el middleware chatbot.key
    }

    protected function idRule(): array
    {
        return ['required', 'integer', 'min:1'];
    }

    protected function textRule(int $max = 100): array
    {
        return ['required', 'string', "max:{$max}"];
    }

    protected function listRule(bool $required = true): array
    {
        return [$required ? 'required' : 'nullable', 'array', 'max:' . config('chatbot.max_items')];
    }
}
