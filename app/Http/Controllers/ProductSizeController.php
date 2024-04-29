<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Http\Request;

class ProductSizeController extends Controller
{
    /**
     * SHOW ALL PRODUCT SIZES
     * @OA\Get(
     *     path="/api/productsize",
     *     operationId="getProductSizes",
     *     tags={"Product Size"},
     *     summary="Get list of all product sizes",
     *     description="Returns list of all product sizes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/ProductSize")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return ProductSize::all();
    }

    /**
     * CREATE A NEW PRODUCT SIZE
     * @OA\Post(
     *     path="/api/productsize",
     *     operationId="storeProductSize",
     *     tags={"Product Size"},
     *     summary="Store new product size",
     *     description="Store new product size",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "size_id"},
     *             @OA\Property(property="product_id", type="integer", example="1"),
     *             @OA\Property(property="size_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product size created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductSize")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product or Size not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Product size already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product size already exists")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"product_id": {"The product id field is required."}})
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'size_id' => 'required|integer'
        ]);

//        VALIDATE IF PRODUCT AND SIZE EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $size = Size::find($request->size_id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH
        $productSize = ProductSize::where('product_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->first();
        if ($productSize) {
            return response()->json(['message' => 'Product size already exists'], 409);
        }

//        CREATE PRODUCT SIZE
        $productSize = ProductSize::create([
            'product_id' => $request->product_id,
            'size_id' => $request->size_id
        ]);

        return response()->json($productSize);

    }

    /**
     * SHOW A PRODUCT SIZE
     * @OA\Get(
     *     path="/api/productsize/{id}",
     *     operationId="showProductSize",
     *     tags={"Product Size"},
     *     summary="Get product size by id",
     *     description="Returns product size by id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of product size to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/ProductSize")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product size not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product size not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $productSize = ProductSize::find($id);
        if ($productSize) {
            return $productSize;
        } else {
            return response()->json(['message' => 'Product size not found'], 404);
        }
    }

    /**
     * UPDATE A PRODUCT SIZE
     * @OA\Put(
     *     path="/api/productsize/{id}",
     *     operationId="updateProductSize",
     *     tags={"Product Size"},
     *     summary="Update product size by id",
     *     description="Update product size by id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of product size to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_id", "size_id"},
     *             @OA\Property(property="product_id", type="integer", example="1"),
     *             @OA\Property(property="size_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product size updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/ProductSize")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product or Size not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product size not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Product size already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product size already exists")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $productSize = ProductSize::find($id);
        if (!$productSize) {
            return response()->json(['message' => 'Product size not found'], 404);
        }

//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'size_id' => 'required|integer'
        ]);

//        VALIDATE IF PRODUCT AND SIZE EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $size = Size::find($request->size_id);
        if (!$size) {
            return response()->json(['message' => 'Size not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH AND NOT THE SAME
        $productSize = ProductSize::where('product_id', $request->product_id)
            ->where('size_id', $request->size_id)
            ->first();
        if ($productSize && $productSize->id != $id) {
            return response()->json(['message' => 'Product size already exists'], 409);
        }

//        UPDATE PRODUCT SIZE
        $productSize->update([
            'product_id' => $request->product_id,
            'size_id' => $request->size_id
        ]);

        return response()->json($productSize);

    }


    /**
     * DELETE A PRODUCT SIZE
     * @OA\Delete(
     *     path="/api/productsize/{id}",
     *     operationId="destroyProductSize",
     *     tags={"Product Size"},
     *     summary="Delete product size by id",
     *     description="Delete product size by id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of product size to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product size deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product size deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product size not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product size not found")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $productSize = ProductSize::find($id);
        if ($productSize) {
            $productSize->delete();
            return response()->json(['message' => 'Product size deleted successfully']);
        } else {
            return response()->json(['message' => 'Product size not found'], 404);
        }
    }
}
