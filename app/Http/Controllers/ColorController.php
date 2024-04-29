<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * SHOW ALL COLORS
     * @OA\Get(
     *      path="/api/color",
     *      operationId="getColors",
     *      tags={"Color"},
     *      summary="Get list of all colors",
     *      description="Returns list of all colors",
     *      security={{"bearerAuth": {}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Color")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *     )
     * )
     */
    public function index()
    {
        return Color::all();
    }

    /**
     * CREATE A NEW COLOR
     * @OA\Post(
     *      path="/api/color",
     *      operationId="storeColor",
     *      tags={"Color"},
     *      summary="Store new color",
     *      description="Store new color",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "hex"},
     *              @OA\Property(property="name", type="string", example="Red"),
     *              @OA\Property(property="hex", type="string", example="#FF0000")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Color created successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Color")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *          )
     *     )
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:color',
            'hex' => 'required|string|unique:color',
        ]);

        return Color::create($request->all());
    }

    /**
     * SHOW A COLOR
     * @OA\Get(
     *      path="/api/color/{id}",
     *      operationId="getColor",
     *      tags={"Color"},
     *      summary="Get color by id",
     *      description="Returns color by id",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Color id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Color")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Color not found")
     *          )
     *     )
     * )
     */
    public function show(int $id)
    {
        $color = Color::find($id);
        if ($color) {
            return $color;
        } else {
            return response()->json(['message' => 'Color not found'], 404);
        }
    }


    /**
     * UPDATE A COLOR
     * @OA\Put(
     *      path="/api/color/{id}",
     *      operationId="updateColor",
     *      tags={"Color"},
     *      summary="Update color",
     *      description="Update color",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Color id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "hex"},
     *              @OA\Property(property="name", type="string", example="Red"),
     *              @OA\Property(property="hex", type="string", example="#FF0000")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Color updated successfully",
     *          @OA\JsonContent(ref="#/components/schemas/Color")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Color not found")
     *          )
     *     ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}})
     *          )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
        $color = Color::find($id);
        if ($color) {
            $request->validate([
                'name' => 'required|string|unique:color,name,' . $id,
                'hex' => 'required|string|unique:color,hex,' . $id,
            ]);

            $color->update($request->all());
            return $color;
        } else {
            return response()->json(['message' => 'Color not found'], 404);
        }
    }

    /**
     * DELETE A COLOR
     * @OA\Delete(
     *      path="/api/color/{id}",
     *      operationId="deleteColor",
     *      tags={"Color"},
     *      summary="Delete color",
     *      description="Delete color",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Color id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Color deleted successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Color deleted successfully")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *      @OA\Response(
     *          response=404,
     *          description="Color not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Color not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=409,
     *          description="Conflict",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Color is associated with products")
     *          )
     *     )
     * )
     */
    public function destroy(int $id)
    {
        $color = Color::find($id);
        if ($color) {
//            VERIFY IF COLOR HAS COLOR PRODUCTS
            if ($color->productColors()->count() > 0) {
                return response()->json(['message' => 'Color is associated with products'], 409);
            }

            $color->delete();
            return response()->json(['message' => 'Color deleted successfully']);
        } else {
            return response()->json(['message' => 'Color not found'], 404);
        }
    }
}
