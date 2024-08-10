<?php

namespace App\Http\Requests;


use App\Models\Person;

class IndexPersonRequest extends IndexRequest
{
    public function rules(): array
    {
        $sorts = Person::sorts;
        return [
            'dni' => 'nullable|string',
            'names' => 'nullable|string',
            'fatherSurname' => 'nullable|string',
            'motherSurname' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|string',
            'address' => 'nullable|string',
            'reference' => 'nullable|string',
            'district_id' => 'nullable|integer',
            'sort' => 'nullable|string|in:' . implode(',', $sorts),
        ];
    }
}
