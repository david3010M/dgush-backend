<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     schema="UpdateZoneRequest",
 *     @OA\Property(property="name", type="string", maxLength=255, example="Zona 1"),
 *     @OA\Property(property="sendCost", type="number", example=15)
 * )
 */
class UpdateZoneRequest extends UpdateRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zones', 'name')
                    ->whereNull('deleted_at')
                    ->ignore($this->route('zone')),
            ],
            'sendCost' => 'required|numeric',
        ];
    }
}
