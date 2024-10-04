<?php

namespace App\Http\Controllers;

use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ColorController extends Controller
{
    /**
     * SHOW ALL COLORS
     * @OA\Get(
     *      path="/dgush-backend/public/api/color",
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
     *      path="/dgush-backend/public/api/color",
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
        $validator = validator()->make($request->all(), [
            'name' => [
                'required',
                'string',
                Rule::unique('color', 'name')->whereNull('deleted_at')
            ],
            'hex' => [
                'required',
                'string',
                Rule::unique('color', 'hex')->whereNull('deleted_at')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $name = $request->input('name');
        $value = strtolower(str_replace(' ', '-', $name));

        $data = [
            'name' => $name,
            'value' => $value,
            'hex' => $request->input('hex')
        ];

        $color = Color::create($data);
        $color = Color::find($color->id);

        return response()->json($color);
    }

    /**
     * SHOW A COLOR
     * @OA\Get(
     *      path="/dgush-backend/public/api/color/{id}",
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
     *      path="/dgush-backend/public/api/color/{id}",
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
            $validator = validator()->make($request->all(), [
                'name' => [
                    'required',
                    'string',
                    Rule::unique('color', 'name')->ignore($color->id)->whereNull('deleted_at')
                ],
                'hex' => [
                    'required',
                    'string',
                    Rule::unique('color', 'hex')->ignore($color->id)->whereNull('deleted_at')
                ]
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            $name = $request->input('name');
            $value = strtolower(str_replace(' ', '-', $name));

            $data = [
                'name' => $name,
                'value' => $value,
                'hex' => $request->input('hex')
            ];

            $color->update($data);
            $color = Color::find($color->id);

            return response()->json($color);
        } else {
            return response()->json(['message' => 'Color not found'], 404);
        }
    }

    /**
     * DELETE A COLOR
     * @OA\Delete(
     *      path="/dgush-backend/public/api/color/{id}",
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
            if ($color->productDetails()->count() > 0) {
                return response()->json(['message' => 'Color is associated with products'], 409);
            }

            $color->delete();
            return response()->json(['message' => 'Color deleted successfully']);
        } else {
            return response()->json(['message' => 'Color not found'], 404);
        }
    }
}
