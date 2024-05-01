<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * SHOW ALL CATEGORIES
     * @OA\Get(
     *     path="/api/category",
     *     tags={"Category"},
     *     summary="Show all categories",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Show all categories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="name", type="string", example="Category 1"),
     *                  @OA\Property(property="order", type="integer", example="1"),
     *                  @OA\Property(property="subcategories", type="array", @OA\Items(ref="#/components/schemas/Subcategory"))
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        return Category::all()->load('subcategories');
    }

    /**
     * CREATE A NEW CATEGORY
     * @OA\Post(
     *     path="/api/category",
     *     tags={"Category"},
     *     summary="Create a new category",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Category 1"),
     *             @OA\Property(property="order", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category created",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
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
     *              @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string:unique:category',
            'order' => 'required|integer|unique:category',
        ]);

        return Category::create($request->all());
    }


    /**
     * SHOW A CATEGORY
     * @OA\Get(
     *     path="/api/category/{id}",
     *     tags={"Category"},
     *     summary="Show a category",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show a category",
     *         @OA\JsonContent(
     *              @OA\Property(property="id", type="integer", example="1"),
     *              @OA\Property(property="name", type="string", example="Category 1"),
     *              @OA\Property(property="order", type="integer", example="1"),
     *              @OA\Property(property="subcategories", type="array", @OA\Items(ref="#/components/schemas/Subcategory"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return $category;
    }

    /**
     * UPDATE A CATEGORY
     * @OA\Put(
     *     path="/api/category/{id}",
     *     tags={"Category"},
     *     summary="Update a category",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Category 1"),
     *             @OA\Property(property="order", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated",
     *         @OA\JsonContent(ref="#/components/schemas/Category")
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
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
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
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|unique:category,name,' . $id,
            'order' => 'required|integer|unique:category,order,' . $id,
        ]);

        $category->update($request->all());

        return $category;
    }

    /**
     * DELETE A CATEGORY
     * @OA\Delete(
     *     path="/api/category/{id}",
     *     tags={"Category"},
     *     summary="Delete a category",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Category has subcategories",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category has subcategories")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

//        VALIDATE IF CATEGORY HAS SUBCATEGORIES
        if ($category->subcategories()->count() > 0) {
            return response()->json(['message' => 'Category has subcategories'], 409);
        }

        $category->delete();
        return response()->json(['message' => 'Category deleted']);
    }
}
