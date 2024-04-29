<?php

namespace App\Http\Controllers;

use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * SHOW ALL SIZES
     * @OA\Get(
     *     path="/api/size",
     *     operationId="getSizes",
     *     tags={"Size"},
     *     summary="Get list of all sizes",
     *     description="Returns list of all sizes",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Size")
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
        return Size::all();
    }

    /**
     * CREATE A NEW SIZE
     * @OA\Post(
     *     path="/api/size",
     *     operationId="storeSize",
     *     tags={"Size"},
     *     summary="Store new size",
     *     description="Store new size",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Small")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Size created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Size")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
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
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:size'
        ]);

        return Size::create($request->all());
    }

    /**
     * SHOW A SIZE
     * @OA\Get(
     *     path="/api/size/{id}",
     *     operationId="getSize",
     *     tags={"Size"},
     *     summary="Get size by id",
     *     description="Returns size by id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of size to return",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Size")
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
     *         description="Size not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Size not found")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
        $size = Size::find($id);
        if ($size) {
            return $size;
        } else {
            return response()->json(['message' => 'Size not found'], 404);
        }
    }

    /**
     * UPDATE A SIZE
     * @OA\Put(
     *     path="/api/size/{id}",
     *     operationId="updateSize",
     *     tags={"Size"},
     *     summary="Update size by id",
     *     description="Update size by id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of size to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Medium")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Size updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Size")
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
     *         description="Size not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Size not found")
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
        $size = Size::find($id);
        if ($size) {
            $request->validate([
                'name' => 'required|string|unique:size,name,' . $id,
            ]);

            $size->update($request->all());
            return $size;
        } else {
            return response()->json(['message' => 'Size not found'], 404);
        }
    }

    /**
     * DELETE A SIZE
     * @OA\Delete(
     *     path="/api/size/{id}",
     *     operationId="deleteSize",
     *     tags={"Size"},
     *     summary="Delete size by id",
     *     description="Delete size by id",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of size to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Size deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Size deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *        )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Size not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Size not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Size has products",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Size is associated with products")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $size = Size::find($id);
        if ($size) {
//            VERIFY IF SIZE HAS SIZE PRODUCTS
            if ($size->products->count() > 0) {
                return response()->json(['message' => 'Size is associated with products'], 409);
            }

            $size->delete();
            return response()->json(['message' => 'Size deleted successfully']);
        } else {
            return response()->json(['message' => 'Size not found'], 404);
        }
    }
}
