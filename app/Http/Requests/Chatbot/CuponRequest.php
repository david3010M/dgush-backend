<?php

namespace App\Http\Requests\Chatbot;

class CuponRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'codigo' => $this->textRule(),
        ];
    }
}
