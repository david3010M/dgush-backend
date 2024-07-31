<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'detailweb' => $this->detailweb,
            'price1' => $this->price1,
            'price2' => $this->price2,
            'score' => $this->score,
            'status' => $this->status,
            'liquidacion' => $this->liquidacion,
            'subcategory_id' => $this->subcategory_id,
            'image' => (new ImageResource($this->image))->url
        ];
    }
}
