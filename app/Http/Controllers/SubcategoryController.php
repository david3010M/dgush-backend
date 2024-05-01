<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{

    /**
     * SHOW ALL SUBCATEGORIES
     * @OA\Get(
     *     path="/dgush-backend/public/api/subcategory",
     *     summary="Get all subcategories",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of all subcategories",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Subcategory")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *        )
     *     )
     * )
     */
    public function index()
    {
        return Subcategory::all();
    }

    /**
     * CREATE A NEW SUBCATEGORY
     * @OA\Post(
     *     path="/dgush-backend/public/api/subcategory",
     *     summary="Create a new subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Subcategory 1"),
     *             @OA\Property(property="order", type="integer", example="1"),
     *             @OA\Property(property="category_id", type="integer", example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory created",
     *         @OA\JsonContent(ref="#/components/schemas/Subcategory")
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
     *         description="Category not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category not found")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
//        VALIDATE DATA
        $request->validate([
            'name' => 'required|string|unique:subcategory',
            'order' => 'required|integer|unique:subcategory',
            'category_id' => 'required|integer'
        ]);

//        VERIFY IF CATEGORY EXISTS
        if (!Category::find($request->category_id)) {
            return response()->json(['message' => 'Category not found'], 404);
        }

//        CREATE NEW SUBCATEGORY
        return Subcategory::create($request->all());
    }

    /**
     * SHOW A SUBCATEGORY
     * @OA\Get(
     *     path="/dgush-backend/public/api/subcategory/{id}",
     *     summary="Get a subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the subcategory",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory found",
     *         @OA\JsonContent(ref="#/components/schemas/Subcategory")
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
     *         description="Subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $subcategory = Subcategory::find($id)->load('products');

        if ($subcategory) {
            return $subcategory;
        } else {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }
    }


    /**
     * UPDATE A SUBCATEGORY
     * @OA\Put(
     *     path="/dgush-backend/public/api/subcategory/{id}",
     *     summary="Update a subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the subcategory",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Subcategory 1"),
     *             @OA\Property(property="order", type="integer", example="1"),
     *             @OA\Property(property="category_id", type="integer", example="1"),
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory updated",
     *         @OA\JsonContent(ref="#/components/schemas/Subcategory")
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
     *
     */
    public function update(Request $request, int $id)
    {
        $subcategory = Subcategory::find($id);

        if ($subcategory) {
//            VALIDATE DATA
            $request->validate([
                'name' => 'required|string|unique:subcategory,name,' . $id,
                'order' => 'required|integer|unique:subcategory,order,' . $id,
                'category_id' => 'required|integer'
            ]);

//            VERIFY IF CATEGORY EXISTS
            if (!Category::find($request->category_id)) {
                return response()->json(['message' => 'Category not found'], 404);
            }

            $subcategory->update($request->all());
            return $subcategory;
        } else {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }
    }

    /**
     * DELETE A SUBCATEGORY
     * @OA\Delete(
     *     path="/dgush-backend/public/api/subcategory/{id}",
     *     summary="Delete a subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Subcategory ID",
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Subcategory deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory deleted")
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
     *         description="Subcategory not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Subcategory has products",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Subcategory has products")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $subcategory = Subcategory::find($id);

        if ($subcategory) {
//            VERIFY IF SUBCATEGORY HAS PRODUCTS
            if ($subcategory->products()->count() > 0) {
                return response()->json(['message' => 'Subcategory has products'], 409);
            }

            $subcategory->delete();
            return response()->json(['message' => 'Subcategory deleted'], 200);
        } else {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }
    }
}
