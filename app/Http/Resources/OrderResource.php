<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema (
 *     schema="OrderResource",
 *     title="OrderResource",
 *     description="Order resource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="number", type="string", example="00000001"),
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
        $statusDictionary = [
            'verificado' => 0,
            'confirmado' => 1,
            'enviado' => 2,
            'recojotiendaproceso' => 3, //recojotiendaproceso
            'recojotiendalisto' => 4, //recojotiendalisto
            'entregado' => 5,
            'cancelado' => 6,
        ];

        return [
            'id' => $this->id,
            'number' => $this->number,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'sendCost' => $this->sendCost,
            'total' => $this->total,
            'status' => $this->status,
            'statusNumber' => $statusDictionary[$this->status],
            'description' => $this->description ?? '-',
            'image' => $this->orderItems[0] ? $this->orderItems[0]->productDetail ? $this->orderItems[0]->productDetail->product->image : null : null,
            'date' => $this->date,
            'shippingDate' => $this->shippingDate ? Carbon::parse($this->shippingDate)->format('Y-m-d') : null,
            'deliveryDate' => $this->deliveryDate ? Carbon::parse($this->deliveryDate)->format('Y-m-d') : null,
            'coupon_id' => $this->coupon_id,
            'order_items' => $this->orderItems->count() > 0 ? OrderItemResource::collection($this->orderItems) : null,
            'coupon' => $this->coupon ? new CouponResource($this->coupon) : null,
            'send_information' => $this->sendInformation ? new SendInformationResource($this->sendInformation) : null,
            'user' => $this->user ? new UserResource($this->user) : null,
        ];
    }
}
