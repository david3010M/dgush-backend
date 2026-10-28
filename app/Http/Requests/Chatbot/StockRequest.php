<?php

namespace App\Http\Requests\Chatbot;

/**
 * Base de D2/D3. El header Idempotency-Key se valida como un campo más:
 * sin él responde 400 "falta Idempotency-Key".
 */
abstract class StockRequest extends ChatbotRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['Idempotency-Key' => $this->header('Idempotency-Key')]);
    }

    protected function baseRules(): array
    {
        return [
            'Idempotency-Key' => $this->textRule(),
            'referencia' => $this->textRule(),
        ];
    }

    protected function varianteRules(string $prefix): array
    {
        return [
            "{$prefix}.product_id" => $this->idRule(),
            "{$prefix}.color_id" => $this->idRule(),
            "{$prefix}.size_id" => $this->idRule(),
        ];
    }
}
