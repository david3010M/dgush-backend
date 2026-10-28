<?php

namespace App\Http\Requests\Chatbot;

class ReservarStockRequest extends StockRequest
{
    public function rules(): array
    {
        return [
            ...$this->baseRules(),
            'items' => [...$this->listRule(), 'min:1'],
            ...$this->varianteRules('items.*'),
            'items.*.quantity' => $this->idRule(),
        ];
    }
}
