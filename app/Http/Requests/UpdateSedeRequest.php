<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateSedeRequest extends FormRequest
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
                Rule::unique("sedes", "name")
                    ->whereNull("deleted_at")
                    ->ignore($this->route("sede")),
            ],
            "address" => "nullable|string",
            "phone" => "nullable|string",
            "email" => "nullable|string",
            "district_id" => "nullable|integer|exists:district,id",
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
