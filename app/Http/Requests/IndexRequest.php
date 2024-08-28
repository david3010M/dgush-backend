<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class IndexRequest extends FormRequest
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
            'direction' => 'nullable|string|in:asc,desc',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json(['message' => $validator->errors()->first(),], 422);
        throw new ValidationException($validator, $response);
    }
}
