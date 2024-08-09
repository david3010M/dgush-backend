<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PersonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dni' => $this->dni,
            'names' => $this->names,
            'fatherSurname' => $this->fatherSurname,
            'motherSurname' => $this->motherSurname,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'reference' => $this->reference,
            'district_id' => $this->district_id,
            'district' => (new DistrictResource($this->district))->name,
        ];
    }
}
