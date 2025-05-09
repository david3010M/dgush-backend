<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SedeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id"         => $this->id,
            "name"       => $this->name,
            "address"    => $this->address,
            "phone"      => $this->phone,
            "email"      => $this->email,
            'ruc'        => $this->ruc,
            'brand_name' => $this->brand_name,
            'server_id'  => $this->server_id,
            // "district" => (new DistrictResource($this->district))->name,
            "district"   => $this->district ? (new DistrictResource($this->district))->name : null,

        ];
    }
}
