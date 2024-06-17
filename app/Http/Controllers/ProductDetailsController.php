<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductDetailsResource;
use App\Models\ProductDetails;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductDetailsController extends Controller
{
    /**
     * @OA\Get(
     *     path="dgush-backend/public/api/product-details",
     *     summary="Get all product details",
     *     tags={"Product Details"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Get all product details",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductDetailsResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $productDetails = ProductDetails::all();
        return response()->json(ProductDetailsResource::collection($productDetails));
    }

    /**
     * @OA\Post(
     *     path="dgush-backend/public/api/product-details",
     *     summary="Create a product details",
     *     tags={"Product Details"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"stock", "product_id", "color_id", "size_id"},
     *             @OA\Property(property="stock", type="integer", example="10"),
     *             @OA\Property(property="product_id", type="integer", example="1"),
     *             @OA\Property(property="color_id", type="integer", example="1"),
     *             @OA\Property(property="size_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Create a product details",
     *         @OA\JsonContent(ref="#/components/schemas/ProductDetailsResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="The given data was invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The stock field is required.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'stock' => 'required|integer',
            'color_id' => 'required|integer|exists:color,id',
            'size_id' => 'required|integer|exists:size,id',
            'product_id' => [
                'required',
                'integer',
                'exists:product,id',
                Rule::unique('product_details')
                    ->where('color_id', $request->color_id)
                    ->where('size_id', $request->size_id)
                    ->whereNull('deleted_at')
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'stock' => $request->input('stock'),
            'product_id' => $request->input('product_id'),
            'color_id' => $request->input('color_id'),
            'size_id' => $request->input('size_id'),
        ];

        $productDetails = ProductDetails::create($data);
        $productDetails = ProductDetails::find($productDetails->id);

        return response()->json($productDetails);


    }

    public function show(int $id)
    {
        $productDetails = ProductDetails::withTrashed()->find($id);
        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }
        return new ProductDetailsResource($productDetails);
    }

    public function update(Request $request, int $id)
    {
        $productDetails = ProductDetails::withTrashed()->find($id);
        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'stock' => 'required|integer',
            'color_id' => 'required|integer|exists:color,id',
            'size_id' => 'required|integer|exists:size,id',
            'product_id' => [
                'required',
                'integer',
                'exists:product,id',
                Rule::unique('product_details')
                    ->where('color_id', $request->color_id)
                    ->where('size_id', $request->size_id)
                    ->whereNull('deleted_at')
                    ->ignore($productDetails->id)
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'stock' => $request->input('stock'),
            'product_id' => $request->input('product_id'),
            'color_id' => $request->input('color_id'),
            'size_id' => $request->input('size_id'),
        ];

        $productDetails->update($data);
        $productDetails = ProductDetails::find($productDetails->id);

        return response()->json($productDetails);
    }

    public function destroy(int $id)
    {
        $productDetails = ProductDetails::withTrashed()->find($id);
        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }

        if ($productDetails->trashed()) {
            return response()->json(['error' => 'Product details already deleted'], 404);
        }

        $productDetails->delete();
        return response()->json(['message' => 'Product details deleted']);
    }
}
