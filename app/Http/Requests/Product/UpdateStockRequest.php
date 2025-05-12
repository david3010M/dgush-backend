<?php
namespace App\Http\Requests\Product;

use App\Http\Requests\UpdateRequest;

/**
 * @OA\Schema(
 *     schema="UpdateStockRequest",
 *     type="object",
 *     required={"items"},
 *     @OA\Property(
 *         property="items",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             required={"product_id","color_id", "size_id", "stock"},
 *             @OA\Property(property="product_id", type="integer", description="ID del producto"),
 *             @OA\Property(property="color_id", type="integer", description="ID del color"),
 *             @OA\Property(property="size_id", type="integer", description="ID de la talla"),
 *             @OA\Property(property="stock", type="integer", description="Cantidad de stock")
 *         )
 *     )
 * )
 */
class UpdateStockRequest extends UpdateRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'items'              => 'required|array',
            'items.*.product_id' => 'required|exists:product,server_id',
            'items.*.color_id'   => 'required|exists:color,server_id',
            'items.*.size_id'    => 'required|exists:size,server_id',
            'items.*.stock'      => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'items.required'              => 'Debe proporcionar al menos un ítem.',
            'items.array'                 => 'El campo items debe ser un arreglo.',

            'items.*.product_id.required' => 'El campo product_id es obligatorio.',
            'items.*.product_id.exists'   => 'El producto especificado no existe.',

            'items.*.color_id.required'   => 'El campo color_id es obligatorio.',
            'items.*.color_id.exists'     => 'El color especificado no existe.',

            'items.*.size_id.required'    => 'El campo size_id es obligatorio.',
            'items.*.size_id.exists'      => 'La talla especificada no existe.',

            'items.*.stock.required'      => 'El campo stock es obligatorio.',
            'items.*.stock.integer'       => 'El stock debe ser un número entero.',
            'items.*.stock.min'           => 'El stock no puede ser negativo.',
        ];
    }

}
