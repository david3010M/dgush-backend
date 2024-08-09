<?php

namespace App\Http\Requests;

class StorePersonRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'names' => 'required|string',
            'fatherSurname' => 'required|string',
            'motherSurname' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip' => 'required|string',
            'country' => 'required|string',
        ];
    }
}
