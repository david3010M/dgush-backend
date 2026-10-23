<?php

namespace App\Http\Requests\Chatbot;

class VerificarStockRequest extends VarianteRequest
{
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'quantity' => $this->idRule(),
        ];
    }
}
