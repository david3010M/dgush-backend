<?php

namespace App\Http\Requests\Chatbot;

class LiberarStockRequest extends StockRequest
{
    public function rules(): array
    {
        return [
            ...$this->baseRules(),
            // Sin items se libera toda la referencia.
            'items' => $this->listRule(required: false),
            ...$this->varianteRules('items.*'),
            // convertida: el pedido ya se registró en 360Sys, que descuenta el stock real.
            'motivo' => ['nullable', 'in:liberada,convertida'],
        ];
    }
}
