<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
     * SEARCH SUBCATEGORIES
     * @OA\Get(
     *     path="/dgush-backend/public/api/subcategory/search",
     *     summary="Search subcategories",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Search subcategories",
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
    public function search()
    {
        $subcategories = Subcategory::search(
            request('search'),
            request('sort', 'score'),
            request('direction', 'desc')
        );
        return response()->json($subcategories);
    }

    /**
     * CREATE A NEW SUBCATEGORY
     * @OA\Post(
     *     path="/dgush-backend/public/api/subcategory",
     *     summary="Create a new subcategory",
     *     tags={"Subcategory"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(ref="#/components/schemas/SubcategoryRequest")
     *          )
     *      ),
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
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('subcategory', 'name')->whereNull('deleted_at')
            ],
            'category_id' => 'required|integer|exists:category,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $name = $request->input('name');
        $value = strtolower(str_replace(' ', '-', $name));

        $data = [
            'name' => $name,
            'value' => $value,
            'category_id' => $request->category_id
        ];

        $color = Subcategory::create($data);
        $color = Subcategory::find($color->id);

        return response()->json($color);
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
     *         @OA\JsonContent(ref="#/components/schemas/SubcategoryRequest")
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
            $validator = validator()->make($request->all(), [
                'name' => [
                    'nullable',
                    'string',
                    Rule::unique('subcategory', 'name')->ignore($subcategory->id)->whereNull('deleted_at')
                ],
                'category_id' => 'nullable|integer|exists:category,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            $name = $request->input('name') ?? $subcategory->name;
            $value = strtolower(str_replace(' ', '-', $name));

            $data = [
                'name' => $name,
                'value' => $value,
                'category_id' => $request->input('category_id') ?? $subcategory->category_id
            ];

            $subcategory->update($data);
            $subcategory = Subcategory::find($subcategory->id);

            return response()->json($subcategory);
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
            return response()->json(['message' => 'Subcategory deleted']);
        } else {
            return response()->json(['message' => 'Subcategory not found'], 404);
        }
    }

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/subcategoryMostPopular",
     *     summary="Most popular subcategories",
     *     tags={"Subcategory"},
     *     @OA\Response(
     *          response=200,
     *          description="List of most popular subcategories",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Subcategory")
     *          )
     *     )
     * )
     */
    public function mostPopular()
    {
        $subcategories = Subcategory::orderBy('score', 'desc')
            ->limit(5)
            ->get();

        return $subcategories;
    }
}
