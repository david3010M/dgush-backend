<?php

namespace App\Http\Requests;

class IndexZoneRequest extends IndexRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string',
            'sendCost' => 'numeric',
        ];
    }
}
