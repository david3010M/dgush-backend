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
            'recojoTiendaProceso' => 3,
            'recojoTiendaListo' => 4,
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
            'image' => $this->orderItems[0] ? $this->orderItems[0]->productDetail->product->image : null,
            'date' => $this->date,
            'shippingDate' => Carbon::parse($this->date)->format('Y-m-d'), // change to shippingDate
            'deliveryDate' => Carbon::parse($this->date)->format('Y-m-d'), // change to deliveryDate
            'coupon_id' => $this->coupon_id,
            'order_items' => OrderItemResource::collection($this->orderItems),
            'coupon' => new CouponResource($this->coupon),
            'send_information' => new SendInformationResource($this->sendInformation),
            'user' => new UserResource($this->user),
        ];
    }
}
