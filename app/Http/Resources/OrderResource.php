<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        'subtotal',
//        'discount',
//        'sendCost',
//        'total',
//        'quantity',
//        'date',
//        'status',
//        'user_id',
//        'coupon_id'

        $orderItems = OrderItemResource::collection($this->orderItems);

        return [
            'id' => $this->id,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'sendCost' => $this->sendCost,
            'total' => $this->total,
            'quantity' => $this->quantity,
            'date' => $this->date,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'coupon_id' => $this->coupon_id,
            'items' => $orderItems,
//            'coupon' => new CouponResource($this->whenLoaded('coupon')),
//            'sendInformation' => new SendInformationResource($this->whenLoaded('sendInformation')),
        ];
    }
}
