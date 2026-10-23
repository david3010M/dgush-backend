<?php

namespace App\Http\Requests\Chatbot;

class PalabrasRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'palabras' => [...$this->listRule(), 'min:1'],
            'palabras.*' => $this->textRule(),
        ];
    }
}
