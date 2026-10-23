<?php

namespace App\Http\Requests\Chatbot;

class AlternativasRequest extends ChatbotRequest
{
    public function rules(): array
    {
        return [
            'subcats_grupo' => [...$this->listRule(), 'min:1'],
            'subcats_grupo.*' => $this->textRule(),
            'subcat_original' => $this->textRule(),
            'ids_excluir' => $this->listRule(required: false),
            'ids_excluir.*' => $this->idRule(),
        ];
    }
}
