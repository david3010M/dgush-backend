<?php

namespace App\Http\Requests\Chatbot;

class ServerIdsRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'items' => [...$this->listRule(), 'min:1'],
            'items.*.product_id' => $this->idRule(),
            'items.*.color_id' => $this->idRule(),
            'items.*.size_id' => $this->idRule(),
        ];
    }
}
