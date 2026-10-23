<?php

namespace App\Http\Requests\Chatbot;

class SubcategoriaRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'subcategoria' => $this->textRule(),
        ];
    }
}
