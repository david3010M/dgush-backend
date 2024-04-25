<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    /**
     *
     * @OA\Get(
     *      path="/permissions",
     *      operationId="getPermissionsList",
     *      tags={"Permissions"},
     *      summary="Get list of permissions",
     *      description="Returns list of permissions",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Permission"))
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *      )
     * )
     *
     */
    public function index()
    {
        return Permission::all();
    }

    /**
     * @OA\Get(
     *      path="/permissions/{id}",
     *      operationId="getPermissionById",
     *      tags={"Permissions"},
     *      summary="Get permission information",
     *      description="Returns permission data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Permission ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Permission")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated")
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Permission not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Permission not found")
     *          )
     *      )
     * )
     *
     */
    public function show(int $id)
    {
//        FIND PERMISSION
        $permission = Permission::find($id);

//        ERROR MESSAGE
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }
        return $permission;

    }
}
