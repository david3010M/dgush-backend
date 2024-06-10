<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TypeUserAccessesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'optionmenu_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/^\d+(,\d+)*$/', $value)) {
                        $fail('optionmenu_id must be a comma-separated list of integers.');
                        return;
                    }

                    $numbers = explode(',', $value);
                    if (count($numbers) !== count(array_unique($numbers))) {
                        $fail('optionmenu_id contains duplicate values.');
                    }
                },
            ],
            'typeuser_id' => [
                'required',
                'not_in:1',
            ]
        ];
    }

    public function messages()
    {
        return [
            'optionmenu_id.required' => 'The optionmenu_id field is required.',
            'typeuser_id.not_in' => 'Update accesses of the administrator type is not allowed.',
        ];
    }
}
