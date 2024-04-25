<?php

namespace App\Http\Controllers;

use App\Models\OptionMenu;
use App\Models\TypeUser;
use Illuminate\Http\Request;

class TypeUserController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/typeuser",
     *     tags={"Type User"},
     *     summary="List Type User",
     *     operationId="index",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of Type User",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/TypeUser")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         ),
     *     ),
     * )
     */
    public function index()
    {
        return TypeUser::all();
    }

    /**
     * @OA\Post(
     *     path="/api/typeuser",
     *     tags={"Type User"},
     *     summary="Create Type User",
     *     operationId="store",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Admin"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Type User created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TypeUser")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="The given data was invalid",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="name",
     *                      type="array",
     *                      @OA\Items(type="string", example="The name field is required.")
     *                  ),
     *              ),
     *         ),
     *     ),
     * )
     */
    public function store(Request $request)
    {
//        Validate data
        $request->validate([
            'name' => 'required|string|unique:typeuser',
        ]);

//        Create a new Type User
        return TypeUser::create($request->all());
    }


    /**
     * @OA\Get(
     *     path="/api/typeuser/{id}",
     *     tags={"Type User"},
     *     summary="Show Type User",
     *     operationId="show",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Type User ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Show Type User",
     *         @OA\JsonContent(ref="#/components/schemas/TypeUser")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Type User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type User not found")
     *         ),
     *     ),
     * )
     */
    public function show(int $id)
    {
//        Find Type User
        $typeUser = TypeUser::find($id);

        if (!$typeUser) {
            return response()->json(
                ['message' => 'Type User not found'], 404
            );
        }

//        LOAD ACCESS AND PERMISSIONS
        $access = $typeUser->getAccess($id);
        $hasPermission = $typeUser->getHasPermission($id);

        $typeUser = $typeUser->toArray();
        $typeUser['access'] = $access;
        $typeUser['hasPermission'] = $hasPermission;

        return $typeUser;
    }


    /**
     * @OA\Put(
     *     path="/api/typeuser/{id}",
     *     tags={"Type User"},
     *     summary="Update Type User",
     *     operationId="update",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *     name="id",
     *     in="path",
     *     required=true,
     *     description="Type User ID",
     *     @OA\Schema(
     *         type="integer",
     *         format="int64"
     *     ),
     *     example=1
     * ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Admin"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type User updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/TypeUser")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Type User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type User not found")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="The given data was invalid",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="object",
     *                  @OA\Property(
     *                      property="name",
     *                      type="array",
     *                      @OA\Items(type="string", example="The name field is required.")
     *                  ),
     *              ),
     *         ),
     *     ),
     * )
     *
     */
    public function update(Request $request, int $id)
    {
//        Find Type User
        $typeUser = TypeUser::find($id);

//        Error if not found
        if (!$typeUser) {
            return response()->json(
                ['message' => 'Type User not found'], 404
            );
        }

//        Validate data
        $request->validate([
            'name' => 'required|string|unique:typeuser,name,' . $id,
        ]);

//        Update Type User
        $typeUser->update($request->all());
        return $typeUser;

    }

    /**
     * @OA\Delete(
     *     path="/api/typeuser/{id}",
     *     tags={"Type User"},
     *     summary="Delete Type User",
     *     operationId="destroy",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Type User ID",
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Type User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type User deleted successfully")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Type User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type User not found")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Type User has accesses or permissions associated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Type User has accesses associated")
     *         ),
     *     )
     * )
     *
     */
    public function destroy(int $id)
    {
//        Find Type User
        $typeUser = TypeUser::find($id);

//        Error if not found
        if (!$typeUser) {
            return response()->json(
                ['message' => 'Type User not found'], 404
            );
        }

//        VALIDATE IF TYPEUSER HAS ANY ACCESSES ASSOCIATED
        if ($typeUser->access->count() > 0) {
            return response()->json(
                ['message' => 'Type User has accesses associated'], 409
            );
        }

//        VALIDATE IF TYPEUSER HAS ANY PERMISSIONS ASSOCIATED
        if ($typeUser->hasPermission->count() > 0) {
            return response()->json(
                ['message' => 'Type User has permissions associated'], 409
            );
        }

//        Delete Type User
        $typeUser->delete();
        return response()->json(
            ['message' => 'Type User deleted successfully']
        );
    }
}
