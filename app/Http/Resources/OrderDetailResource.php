<?php
namespace App\Http\Resources;

use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 * @OA\Schema (
 *     schema="OrderDetailResource",
 *     title="OrderDetailResource",
 *     description="Order detail resource",
 *     @OA\Property(property="id", type="integer", example="1"),
 *     @OA\Property(property="order_id", type="integer", example="1"),
 *     @OA\Property(property="product", type="object", ref="#/components/schemas/Product"),
 *     @OA\Property(property="color", type="object", ref="#/components/schemas/Color"),
 *     @OA\Property(property="size", type="object", ref="#/components/schemas/Size"),
 *     @OA\Property(property="quantity", type="integer", example="2"),
 *     @OA\Property(property="price", type="decimal", example="100.00"),
 *     @OA\Property(property="image", type="string", example="https://cdn.example.com/p.jpg"),
 *     @OA\Property(property="note", type="string", example="sin nota"),
 *     @OA\Property(property="created_at", type="string", example="2024-05-26 14:40:02")
 *   )
 */
class OrderDetailResource extends JsonResource
{
    public function toArray($request)
    {

        $image = Image::where('product_id', $this->product_id)->where('color_id', $this->color_id)->first();
        if ($image) {
            $imageUrl = $image->url;
        } else {
            $product = $this->product;
            $imageUrl = $product->image ? $product->image->url : null;
        }

        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'product' => new ProductResource($this->product),
            'color' => new ColorResource($this->color),
            'size' => new SizeResource($this->size),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'image' => $imageUrl,
            'note' => $this->note,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
