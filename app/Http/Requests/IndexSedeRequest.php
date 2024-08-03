<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexSedeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'all' => 'nullable|string|in:true,false',
            'page' => 'nullable|integer',
            'per_page' => 'nullable|integer',
            'currencyFrom' => 'nullable|string|in:USD,PEN',
            'currencyTo' => 'nullable|string|in:USD,PEN',
            'date' => 'nullable|date',
        ];
    }
}
