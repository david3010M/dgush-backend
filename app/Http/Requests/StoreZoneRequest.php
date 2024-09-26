<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

/**
 * @OA\Schema (
 *     schema="StoreZoneRequest",
 *     required={"name", "sendCost"},
 *     @OA\Property(property="name", type="string", maxLength=255, example="Zona 1"),
 *     @OA\Property(property="sendCost", type="number", example=14)
 * )
 */
class StoreZoneRequest extends StoreRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('zones', 'name')
                    ->whereNull('deleted_at'),
            ],
            'sendCost' => 'required|numeric',
        ];
    }
}
