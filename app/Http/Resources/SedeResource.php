<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SedeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "address" => $this->address,
            "phone" => $this->phone,
            "email" => $this->email,
            "district" => (new DistrictResource($this->district))->name,
        ];
    }
}
