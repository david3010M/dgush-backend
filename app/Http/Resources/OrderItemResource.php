<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'quantity' => $this->quantity,
            'price' => $this->price,
            'name' => $this->productDetail ? $this->productDetail->product->name : null,
            'color' => $this->productDetail->color->name,
            'size' => $this->productDetail->size->name,
//            PARA LOS PEDIDOS DE LA TIENDA
            'description' => $this->productDetail ? $this->productDetail->product->description : null,
            'image' => $this->productDetail ? $this->productDetail->product->image->url : null,
            'product_id' => $this->productDetail ? $this->productDetail->product->id : null,
        ];
    }
}
