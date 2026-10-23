<?php

namespace App\Http\Requests\Chatbot;

class CiudadRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'ciudad' => $this->textRule(),
        ];
    }
}
