<?php

namespace App\Http\Requests\Chatbot;

class MultifiltroRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'subcategoria' => $this->textRule(),
            'color' => ['nullable', 'string', 'max:100'],
            'talla' => ['nullable', 'string', 'max:50'],
        ];
    }
}
