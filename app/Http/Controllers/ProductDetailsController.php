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
     *     path="/dgush-backend/public/api/productdetails",
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
     *     path="/dgush-backend/public/api/productdetails",
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

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/productdetails/{id}",
     *     summary="Get a product details",
     *     tags={"Product Details"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product details ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get a product details",
     *         @OA\JsonContent(ref="#/components/schemas/ProductDetailsResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product details not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $productDetails = ProductDetails::with('product', 'color', 'size')->find($id);
        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }
        return response()->json($productDetails);
    }

    /**
     * @OA\Put(
     *     path="/dgush-backend/public/api/productdetails/{id}",
     *     summary="Update a product details",
     *     tags={"Product Details"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product details ID",
     *         @OA\Schema(type="integer")
     *     ),
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
     *         description="Update a product details",
     *         @OA\JsonContent(ref="#/components/schemas/ProductDetailsResource")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product details not found")
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
    public function update(Request $request, int $id)
    {
        $productDetails = ProductDetails::withTrashed()->find($id);
        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }

        $validator = validator()->make($request->all(), [
            'stock' => 'nullable|integer',
            'color_id' => 'nullable|integer|exists:color,id',
            'size_id' => 'nullable|integer|exists:size,id',
            'product_id' => [
                'nullable',
                'integer',
                'exists:product,id',
                Rule::unique('product_details')
                    ->where('color_id', $request->color_id)
                    ->where('size_id', $request->size_id)
                    ->whereNull('deleted_at')
                    ->ignore($productDetails->id)
            ],
            'status' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $data = [
            'stock' => $request->input('stock') ?? $productDetails->stock,
            'product_id' => $request->input('product_id') ?? $productDetails->product_id,
            'color_id' => $request->input('color_id') ?? $productDetails->color_id,
            'size_id' => $request->input('size_id') ?? $productDetails->size_id,
            'status' => $request->input('status') ?? $productDetails->status,
        ];

        $productDetails->update($data);
        $productDetails = ProductDetails::with('product', 'color', 'size')->find($productDetails->id);

        return response()->json($productDetails);
    }

    /**
     * @OA\Delete(
     *     path="/dgush-backend/public/api/productdetails/{id}",
     *     summary="Delete a product details",
     *     tags={"Product Details"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product details ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product details deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product details not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product details not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Product details already deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Product details already deleted")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $productDetails = ProductDetails::withTrashed()->find($id);

        if (!$productDetails) {
            return response()->json(['error' => 'Product details not found'], 404);
        }

        if ($productDetails->trashed()) {
            return response()->json(['error' => 'Product details already deleted'], 409);
        }

        if ($productDetails->orderItems()->exists()) {
            return response()->json(['error' => 'Product details cannot be deleted because it has order items'], 409);
        }

        $productDetails->delete();
        return response()->json(['message' => 'Product details deleted']);
    }

    /**
     * @OA\Post(
     *     path="/dgush-backend/public/api/productdetails/search",
     *     summary="Search product details",
     *     tags={"Product Details"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="product", type="array",
     *                 @OA\Items(type="string", example="Product 1")
     *             ),
     *             @OA\Property(property="color", type="array",
     *                 @OA\Items(type="integer", example="1")
     *             ),
     *             @OA\Property(property="size", type="array",
     *                 @OA\Items(type="integer", example="1")
     *             ),
     *             @OA\Property(property="category", type="array",
     *                 @OA\Items(type="integer", example="1")
     *             ),
     *             @OA\Property(property="sort", type="string", example="score"),
     *             @OA\Property(property="direction", type="string", example="asc")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search product details",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductDetailsResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="The given data was invalid",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="The product.0 must be a string.")
     *         )
     *     )
     * )
     */
    public function search(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'product' => 'nullable|array',
            'product.*' => 'integer|exists:product,id',
            'color' => 'nullable|array',
            'color.*' => 'string|exists:color,value',
            'size' => 'nullable|array',
            'size.*' => 'string|exists:size,value',
            'category' => 'nullable|array',
            'category.*' => 'string|exists:subcategory,value',
            'sort' => 'nullable|string',
            'direction' => 'nullable|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $productDetails = ProductDetails::search(
            $request->input('product'),
            $request->input('color'),
            $request->input('size'),
            $request->input('category'),
            $request->input('sort') ?? 'id',
            $request->input('direction') ?? 'desc',
            null,
            null
        );
        $productDetails = ProductDetailsResource::collection($productDetails);
        return response()->json($productDetails);
    }

    public function searchPaginate(Request $request)
    {
        $validator = validator()->make($request->all(), [
            'product' => 'nullable|array',
            'product.*' => 'integer|exists:product,id',
            'color' => 'nullable|array',
            'color.*' => 'string|exists:color,value',
            'size' => 'nullable|array',
            'size.*' => 'string|exists:size,value',
            'category' => 'nullable|array',
            'category.*' => 'string|exists:subcategory,value',
            'sort' => 'nullable|string',
            'direction' => 'nullable|string|in:asc,desc',
            'per_page' => 'nullable|integer',
            'page' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $per_page = $request->input('per_page', 10);

        $productDetails = ProductDetails::search(
            $request->input('product'),
            $request->input('color'),
            $request->input('size'),
            $request->input('category'),
            $request->input('sort') ?? 'id',
            $request->input('direction') ?? 'desc',
            $per_page,
            $request->input('page', 1)
        );

        ProductDetailsResource::collection($productDetails);
        return response()->json($productDetails);
    }
}
