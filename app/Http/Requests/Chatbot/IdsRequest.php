<?php

namespace App\Http\Requests\Chatbot;

class IdsRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'ids' => [...$this->listRule(), 'min:1'],
            'ids.*' => $this->idRule(),
        ];
    }
}
