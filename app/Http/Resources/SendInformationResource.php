<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SendInformationResource extends JsonResource
{
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
            'tracking' => $this->tracking,
            'voucher' => $this->voucher,
            'voucherUrl' => $this->voucherUrl,
            'voucherFileName' => $this->voucherFileName,
            'district_id' => $this->district_id,
            'district' => $this->district ? (new DistrictResource($this->district))->name : null,
            'sede_id' => $this->sede_id,
            'sede' => $this->sede ? (new SedeResource($this->sede))->name : null,
            'zone_id' => $this->zone_id,
            'zone' => $this->zone ? (new ZoneResource($this->zone))->name : null,
            'order_id' => $this->order_id,
        ];
    }
}
