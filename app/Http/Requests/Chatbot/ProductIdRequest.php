<?php

namespace App\Http\Requests\Chatbot;

class ProductIdRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'product_id' => $this->idRule(),
        ];
    }
}
