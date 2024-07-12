<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="OrderResource",
 *     title="OrderResource",
 *     description="Order resource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="subtotal", type="decimal", example="100.00"),
 *     @OA\Property(property="discount", type="decimal", example="10.00"),
 *     @OA\Property(property="sendCost", type="decimal", example="5.00"),
 *     @OA\Property(property="total", type="decimal", example="90.00"),
 *     @OA\Property(property="status", type="string", example="pending"),
 *     @OA\Property(property="description", type="string", example="description"),
 *     @OA\Property(property="date", type="timestamp", example="2024-05-26 14:40:02"),
 *     @OA\Property(property="order_items", type="array", @OA\Items(ref="#/components/schemas/OrderItem")),
 *     @OA\Property(property="send_information", type="object", ref="#/components/schemas/SendInformation"),
 *     @OA\Property(property="user", type="object", ref="#/components/schemas/User")
 *   )
 */
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
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'sendCost' => $this->sendCost,
            'total' => $this->total,
            'status' => $this->status,
            'description' => $this->description ?? '-',
//            'quantity' => $this->quantity,
            'date' => $this->date,
//            'user_id' => $this->user_id,
//            'coupon_id' => $this->coupon_id,
            'order_items' => $this->orderItems,
//            'coupon' => new CouponResource($this->whenLoaded('coupon')),
            'send_information' => new SendInformationResource($this->sendInformation),
            'user' => new UserResource($this->user),
        ];
    }
}
