<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * SHOW ALL PRODUCTS
     * @OA\Get(
     *     path="/dgush-backend/public/api/product",
     *     tags={"Product"},
     *     summary="Show all products",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Show all products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *        )
     *     )
     * )
     */
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function getAllProducts()
    {
//        ALL PRODUCTS WITH TRASHED
        $allProducts = Product::withTrashed()->get();
        return response()->json($allProducts);
    }

    /**
     * CREATE PRODUCT
     * @OA\Post(
     *     path="/dgush-backend/public/api/product",
     *     tags={"Product"},
     *     summary="Create product",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name", "description", "detailweb", "price1", "price2", "score", "subcategory_id"},
     *              @OA\Property(property="name", type="string", example="Product 1"),
     *              @OA\Property(property="description", type="string", example="Description of product 1"),
     *              @OA\Property(property="detailweb", type="string", example="Detail of product 1"),
     *              @OA\Property(property="price1", type="number", example="100.00"),
     *              @OA\Property(property="price2", type="number", example="90.00"),
     *              @OA\Property(property="score", type="integer", example="5"),
     *              @OA\Property(property="subcategory_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product created",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
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
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory not found")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
//        VALIDATE DATA
        $request->validate(
            [
                'name' => 'required|string|unique:product',
                'description' => 'required|string',
                'detailweb' => 'required|string',
                'price1' => 'required|numeric',
                'price2' => 'required|numeric',
                'score' => 'required|integer',
                'image' => 'required|string',
                'status' => 'string|in:onsale,new',
                'subcategory_id' => 'required|integer',
            ]
        );

//        VALIDATE SUBCATEGORY
        $subcategory = Subcategory::find($request->subcategory_id);
        if (!$subcategory) {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }

//        CREATE PRODUCT
        $product = Product::create($request->all());
        return response()->json($product);
    }


    /**
     * SHOW PRODUCT
     * @OA\Get(
     *     path="/dgush-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     summary="Show product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show product",
     *         @OA\JsonContent(
     *             @OA\Property(property="product", ref="#/components/schemas/Product"),
     *             @OA\Property(property="colors", type="array", @OA\Items(
     *                 @OA\Property(property="id", type="integer", example="1"),
     *                 @OA\Property(property="name", type="string", example="Red"),
     *                 @OA\Property(property="hex", type="string", example="#FF0000")
     *             )),
     *             @OA\Property(property="sizes", type="array", @OA\Items(ref="#/components/schemas/Size")),
     *             @OA\Property(property="comments", type="array", @OA\Items(ref="#/components/schemas/Comment")),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
//        FIND PRODUCT
        $product = Product::find($id);
        if ($product) {
//            GET COMMENTS
            $colors = $product->getColors($id);
            $sizes = $product->getSizes($id);
            $comments = $product->comments();
            return response()->json([
                'product' => $product,
                'colors' => $colors,
                'sizes' => $sizes,
                'comments' => $comments

            ]);
        } else {
//            PRODUCT NOT FOUND
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * UPDATE PRODUCT
     * @OA\Put(
     *     path="/dgush-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     summary="Update product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"name", "description", "detailweb", "price1", "price2", "score", "subcategory_id"},
     *              @OA\Property(property="name", type="string", example="Product 1"),
     *              @OA\Property(property="description", type="string", example="Description of product 1"),
     *              @OA\Property(property="detailweb", type="string", example="Detail of product 1"),
     *              @OA\Property(property="price1", type="number", example="100.00"),
     *              @OA\Property(property="price2", type="number", example="90.00"),
     *              @OA\Property(property="score", type="integer", example="5"),
     *              @OA\Property(property="subcategory_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
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
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
//        FIND PRODUCT
        $product = Product::find($id);
        if ($product) {
//            VALIDATE DATA
            $request->validate(
                [
                    'name' => 'required|string|unique:product,name,' . $id,
                    'description' => 'required|string',
                    'detailweb' => 'required|string',
                    'price1' => 'required|numeric',
                    'price2' => 'required|numeric',
                    'score' => 'required|integer',
                    'subcategory_id' => 'required|integer',
                ]
            );

//            VALIDATE CATEGORY
            $category = Subcategory::find($request->subcategory_id);
            if (!$category) {
                return response()->json(['message' => 'Subcategory not found'], 404);
            }

//            UPDATE PRODUCT
            $product->update($request->all());
            return response()->json($product);
        } else {
//            PRODUCT NOT FOUND
            return response()->json(['message' => 'Product not found'], 404);
        }
    }

    /**
     * DELETE PRODUCT
     * @OA\Delete(
     *     path="/dgush-backend/public/api/product/{id}",
     *     tags={"Product"},
     *     summary="Delete product",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Product has relations",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product has relations")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
//        FIND PRODUCT
        $product = Product::find($id);
        if ($product) {
//            VALIDATE PRODUCT NOT HAS COMMENTS
            if ($product->comments()->count() > 0) {
                return response()->json(['message' => 'Product has comments'], 409);
            }

//            VALIDATE PRODUCT NOT HAS PRODUCT SIZE
//            if ($product->productSize()->count() > 0) {
//                return response()->json(['message' => 'Product has product size'], 409);
//            }

//            VALIDATE PRODUCT NOT HAS PRODUCT COLOR
//            if ($product->productColor()->count() > 0) {
//                return response()->json(['message' => 'Product has product color'], 409);
//            }

//            DELETE PRODUCT
            $product->delete();
            return response()->json(['message' => 'Product deleted']);
        } else {
//            PRODUCT NOT FOUND
            return response()->json(['message' => 'Product not found'], 404);
        }
    }
}
