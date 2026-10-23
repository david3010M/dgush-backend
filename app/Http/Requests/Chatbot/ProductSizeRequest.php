<?php

namespace App\Http\Requests\Chatbot;

class ProductSizeRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'product_id' => $this->idRule(),
            'size_id' => $this->idRule(),
        ];
    }
}
