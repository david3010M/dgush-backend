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
        $orderItems = OrderItemResource::collection($this->orderItems);

        return [
            'id' => $this->id,
            'user' => new UserResource($this->user),
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'sendCost' => $this->sendCost,
            'total' => $this->total,
            'status' => $this->status,
            'description' => $this->description ?? '-',
//            'quantity' => $this->quantity,
//            'date' => $this->date,
//            'user_id' => $this->user_id,
//            'coupon_id' => $this->coupon_id,
            'order_items' => $this->orderItems,
//            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'send_information' => new SendInformationResource($this->sendInformation),
        ];
    }
}
