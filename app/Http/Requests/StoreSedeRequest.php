<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreSedeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => [
                "required",
                "string",
                Rule::unique("sedes", "name")->whereNull("deleted_at"),
            ],
            "address" => "required|string",
            "phone" => "nullable|string",
            "email" => "required|string",
            "district_id" => "required|integer|exists:district,id",
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'message' => $validator->errors()->first(),
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
