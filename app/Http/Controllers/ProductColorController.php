<?php

namespace App\Http\Controllers;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class ProductColorController extends Controller
{
    /**
     * SHOW ALL PRODUCT COLORS
     * @OA\Get(
     *      path="/api/productcolor",
     *      operationId="index",
     *      tags={"Product Colors"},
     *      summary="Get all product colors",
     *      description="Returns all product colors",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ProductColor")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product color not found")
     *          )
     *      )
     * )
     */
    public function index()
    {
        return ProductColor::all();
    }

    /**
     * CREATE PRODUCT COLOR
     * @OA\Post(
     *      path="/api/productcolor",
     *      operationId="store",
     *      tags={"Product Colors"},
     *      summary="Create product color",
     *      description="Create a new product color",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"product_id", "color_id"},
     *              @OA\Property(property="product_id", type="integer", example="1"),
     *              @OA\Property(property="color_id", type="integer", example="1")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product color created",
     *          @OA\JsonContent(ref="#/components/schemas/ProductColor")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product or Color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Product color already exists",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product color already exists")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'color_id' => 'required|integer',
        ]);

//        VALIDATE IF PRODUCT AND COLOR EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $color = Color::find($request->color_id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH
        $productColor = ProductColor::where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->first();
        if ($productColor) {
            return response()->json(['message' => 'Product color already exists'], 409);
        }

//        CREATE PRODUCT COLOR
        $newProductColor = ProductColor::create([
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
        ]);

        return response()->json($newProductColor);

    }

    /**
     * SHOW A PRODUCT COLOR
     * @OA\Get(
     *      path="/api/productcolor/{id}",
     *      operationId="show",
     *      tags={"Product Colors"},
     *      summary="Get product color by id",
     *      description="Returns product color by id",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Product color id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ProductColor")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product color not found")
     *          )
     *      )
     * )
     */
    public function show(int $id)
    {
        $productColor = ProductColor::find($id);
        if (!$productColor) {
            return response()->json(['message' => 'Product color not found'], 404);
        }

        return $productColor;
    }

    /**
     * UPDATE PRODUCT COLOR
     * @OA\Put(
     *      path="/api/productcolor/{id}",
     *      operationId="update",
     *      tags={"Product Colors"},
     *      summary="Update product color",
     *      description="Update product color",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Product color id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"product_id", "color_id"},
     *              @OA\Property(property="product_id", type="integer", example="1"),
     *              @OA\Property(property="color_id", type="integer", example="1")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product color updated",
     *          @OA\JsonContent(ref="#/components/schemas/ProductColor")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product or Color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product not found")
     *          )
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Product color already exists",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product color already exists")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *          )
     *      )
     * )
     */
    public function update(Request $request, int $id)
    {
        $productColor = ProductColor::find($id);
        if (!$productColor) {
            return response()->json(['message' => 'Product color not found'], 404);
        }

//        VALIDATE DATA
        $request->validate([
            'product_id' => 'required|integer',
            'color_id' => 'required|integer',
        ]);

//        VALIDATE IF PRODUCT AND COLOR EXISTS
        $product = Product::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $color = Color::find($request->color_id);
        if (!$color) {
            return response()->json(['message' => 'Color not found'], 404);
        }

//        VALIDATE IF PRODUCT AND COLOR EXISTS BOTH AND NOT THE SAME
        $productColorExists = ProductColor::where('product_id', $request->product_id)
            ->where('color_id', $request->color_id)
            ->first();
        if ($productColorExists && $productColorExists->id !== $id) {
            return response()->json(['message' => 'Product color already exists'], 409);
        }

//        UPDATE PRODUCT COLOR
        $productColor->update([
            'product_id' => $request->product_id,
            'color_id' => $request->color_id,
        ]);

        return response()->json($productColor);
    }

    /**
     * DELETE PRODUCT COLOR
     * @OA\Delete(
     *      path="/api/productcolor/{id}",
     *      operationId="destroy",
     *      tags={"Product Colors"},
     *      summary="Delete product color",
     *      description="Delete product color",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Product color id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Product color deleted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product color deleted")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Product color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Product color not found")
     *          )
     *      )
     * )
     */
    public function destroy(int $id)
    {
        $productColor = ProductColor::find($id);
        if (!$productColor) {
            return response()->json(['message' => 'Product color not found'], 404);
        }

        $productColor->delete();
        return response()->json(['message' => 'Product color deleted']);
    }
}
