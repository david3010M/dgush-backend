<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePersonRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'names' => 'nullable|string',
            'fatherSurname' => 'nullable|string',
            'motherSurname' => 'nullable|string',
            'email' => [
//                nullable|email|unique:people,email|unique:users,email
                'nullable',
                'email',
                Rule::unique('people', 'email')->ignore(auth()->user()->id),
                Rule::unique('users', 'email')->ignore(auth()->user()->id),
            ],
            'phone' => 'nullable|string|min:9|max:9',
            'address' => 'nullable|string',
            'reference' => 'nullable|string',
            'district_id' => 'nullable|integer|exists:district,id',
        ];
    }
}
