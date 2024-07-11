<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SendInformationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
     * 'names',
     * 'dni',
     * 'email',
     * 'phone',
     * 'address',
     * 'reference',
     * 'comment',
     * 'method',
     * 'district_id',
     * 'order_id',
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'names' => $this->names,
            'dni' => $this->dni,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'reference' => $this->reference,
            'comment' => $this->comment,
            'method' => $this->method,
            'district_id' => $this->district_id,
            'district' => (new DistrictResource($this->district))->name,
            'order_id' => $this->order_id,
        ];
    }
}
