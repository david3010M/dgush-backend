<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductDetailsResource extends JsonResource
{

    /**
     * @OA\Schema (
     *     schema="ProductDetailsResource",
     *     title="ProductDetailsResource",
     *     description="ProductDetailsResource",
     *     @OA\Property(property="id", type="integer", example="1"),
     *     @OA\Property(property="name", type="string", example="Product Name"),
     *     @OA\Property(property="stock", type="integer", example="10"),
     *     @OA\Property(property="colorName", type="string", example="Red"),
     *     @OA\Property(property="sizeName", type="string", example="Large"),
     *     @OA\Property(property="status", type="string", example="active"),
     *     @OA\Property(property="product_id", type="integer", example="1"),
     *     @OA\Property(property="color_id", type="integer", example="1"),
     *     @OA\Property(property="size_id", type="integer", example="1")
     * )
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->product ? (new ProductResource($this->product))->name : null,
            'stock' => $this->stock,
            'colorName' => (new ColorResource($this->color))->name,
            'sizeName' => (new SizeResource($this->size))->name,
            'subcategory' => $this->product ? (new SubcategoryResource($this->product->subcategory))->name : null,
            'status' => $this->status,
            'product_id' => $this->product_id,
            'color_id' => $this->color_id,
            'size_id' => $this->size_id
        ];
    }
}
