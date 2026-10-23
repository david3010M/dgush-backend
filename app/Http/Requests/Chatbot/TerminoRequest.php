<?php

namespace App\Http\Requests\Chatbot;

class TerminoRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'termino' => $this->textRule(),
        ];
    }
}
