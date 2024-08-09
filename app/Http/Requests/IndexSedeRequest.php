<?php

namespace App\Http\Requests;

class IndexSedeRequest extends IndexRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'currencyFrom' => 'nullable|string|in:USD,PEN',
            'currencyTo' => 'nullable|string|in:USD,PEN',
            'date' => 'nullable|date',
        ];
    }
}
