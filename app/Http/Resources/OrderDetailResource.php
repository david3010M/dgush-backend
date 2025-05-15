<?php
namespace App\Http\Resources;

use App\Models\Image;
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
