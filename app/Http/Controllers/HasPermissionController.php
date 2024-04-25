<?php

namespace App\Http\Controllers;

use App\Models\Access;
use App\Models\HasPermission;
use App\Models\OptionMenu;
use App\Models\Permission;
use App\Models\TypeUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HasPermissionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/haspermission",
     *     summary="Get all haspermission",
     *     tags={"HasPermission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Get all haspermission",
     *         @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/HasPermission")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        return HasPermission::all();
    }

    /**
     * @OA\Post(
     *     path="/api/haspermission",
     *     summary="Store haspermission",
     *     tags={"Permission TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"permission_id", "typeuser_id"},
     *              @OA\Property(property="permission_id", type="string", example="1,2,3"),
     *              @OA\Property(property="typeuser_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions successfully added to the typeuser",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permissions 1, 2, 3 successfully added to the typeuser")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="The permission already exists",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The permission already exists.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Typeuser or Permission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Typeuser not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
//        VALIDATE DATA
        $validation = $this->validateHasPermission($request);

//        VALIDATE THE TYPEUSER_ID AND PERMISSION_ID ARE UNIQUE BOTH
        if (HasPermission::where('typeuser_id', $request->typeuser_id)
            ->where('permission_id', $request->permission_id)
            ->exists()) {
            return response()->json(['message' => 'The permission already exists.'], 400);
        }

        if ($validation->getStatusCode() !== 200) {
            return $validation;
        }

//        TYPEUSER ID FROM REQUEST
        $typeuser_id = $request->input('typeuser_id');

//        FIND TYPEUSER
        $typeuser = TypeUser::find($typeuser_id);
        if (!$typeuser) {
            return response()->json(['message' => 'Typeuser not found'], 404);
        }


//        CREATE PERMISSION FROM A STRING OF PERMISSIONS WITH COMMA
        $permissions = explode(',', $request->input('permission_id'));

        foreach ($permissions as $permission) {
            $hasPermission = new HasPermission();
            $hasPermission->typeuser_id = $typeuser_id;
            $hasPermission->permission_id = $permission;
            $hasPermission->save();
        }

        $permissionsAdded = implode(', ', $permissions);

        return response()->json(['message' => 'Permissions ' . $permissionsAdded . ' successfully added to the typeuser']);
    }


    /**
     * @OA\Get(
     *     path="/api/haspermission/{id}",
     *     summary="Get permission from a typeuser",
     *     tags={"Permission TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="HasPermission ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         example=1
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get permission by typeuser",
     *         @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Permissions 1, 2, 3 successfully added to the typeuser")
     *          )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Typeuser or Permission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Typeuser not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function show(int $id)
    {
//        FIND TYPEUSER
        $typeuser = TypeUser::find($id);

//        ERROR MESSAGE
        if (!$typeuser) {
            return response()->json(['message' => 'Typeuser not found'], 404);
        }

//        RETURN PERMISSIONS
        return response()->json(
            ['permissions' => $typeuser->getHasPermission($id)]
        );
    }

    /**
     * @OA\Put(
     *     path="/api/haspermission",
     *     summary="Update haspermission",
     *     tags={"Permission TypeUser"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *              required={"permission_id", "typeuser_id"},
     *              @OA\Property(property="permission_id", type="string", example="1,2,3"),
     *              @OA\Property(property="typeuser_id", type="integer", example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Permissions successfully updated to the typeuser",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Permissions 1, 2, 3 successfully updated to the typeuser")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Typeuser or Permission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Typeuser not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function update(Request $request, int $id)
    {
//        NOT UPDATE ADMIN PERMISSION
        if ($id === 1) {
            return response()->json(['message' => 'You cannot update the admin permission'], 400);
        }

//        VALIDATE DATA
        $validation = $this->validateHasPermission($request);

        if ($validation->getStatusCode() !== 200) {
            return $validation;
        }

//        TYPEUSER ID
        $typeuser_id = $id;

//        DELETE PERMISSION
        HasPermission::where('typeuser_id', $typeuser_id)->delete();

//        UPDATE PERMISSION FROM A STRING OF PERMISSIONS WITH COMMA
        $permissions = explode(',', $request->input('permission_id'));

        foreach ($permissions as $permission) {
            $hasPermission = new HasPermission();
            $hasPermission->typeuser_id = $typeuser_id;
            $hasPermission->permission_id = $permission;
            $hasPermission->save();
        }

        $permissionsAdded = implode(', ', $permissions);

        return response()->json(['message' => 'Permissions ' . $permissionsAdded . ' successfully updated to the typeuser']);
    }

    /**
     * @OA\Delete(
     *     path="/api/haspermission/{id}",
     *     summary="Delete haspermission",
     *     tags={"HasPermission"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="HasPermission ID",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         example=1
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="HasPermission deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="HasPermission deleted")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="You cannot delete the admin permission",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You cannot delete the admin permission")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="HasPermission not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="HasPermission not found")
     *         )
     *     )
     * )
     */
    public function destroy(int $id)
    {
//        FIND PERMISSION
        $hasPermission = HasPermission::find($id);

//        ERROR MESSAGE
        if (!$hasPermission) {
            return response()->json(['message' => 'HasPermission not found'], 404);
        }

//        NOT DELETE ADMIN PERMISSION
        if ($hasPermission->typeuser_id === 1) {
            return response()->json(['message' => 'You cannot delete the admin permission'], 400);
        }

//        DELETE PERMISSION
        $hasPermission->delete();
        return response()->json(['message' => 'HasPermission deleted']);
    }

    function validateHasPermission(Request $request): JsonResponse
    {
//        VALIDAR QUE LA VARIABLE PERMISSION_ID NO TENGA COMA AL FINAL
        if (substr($request->input('permission_id'), -1) === ',') {
            return response()->json(['message' => 'The permission_id cannot end with a comma'], 400);
        }

//        VALIDAR QUE LA VAIRABLE PERMISSION_ID NO TENGA COMA AL PRINCIPIO
        if (substr($request->input('permission_id'), 0, 1) === ',') {
            return response()->json(['message' => 'The permission_id cannot start with a comma'], 400);
        }

//        VALIDATE DATA
        $request->validate([
            'permission_id' => 'required|string',
            'typeuser_id' => 'required|integer',
        ]);

//        VALIDATE EACH PERMISSION_ID IN THA VARIABLE WHICH IS A STRING OF PERMISSIONS SEPARATED BY COMMA
        $permissions = explode(',', $request->input('permission_id'));

        foreach ($permissions as $permission) {
            $validationPermission = $this->validatePermission($permission);
            if ($validationPermission->getStatusCode() !== 200) {
                return $validationPermission;
            }
        }

//        FIND THE TYPEUSER_ID
        $typeuser = TypeUser::find($request->typeuser_id);
        if (!$typeuser) {
            return response()->json(['message' => 'Typeuser not found'], 404);
        }

        return response()->json(1);
    }

    function validatePermission($permission): JsonResponse
    {
//        VALIDATE  THE VARIABLE PERMISSION IS INTEGER
        if (!is_numeric($permission)) {
            return response()->json(['error' => 'Permission ' . $permission . ' is not a number'], 404);
        }

//        VALIDATE THE VARIABLE PERMISSION IS REQUIRED
        if (!$permission) {
            return response()->json(['error' => 'Permission is required'], 404);
        }

//        VALIDATE PERMISSION REQUIRED AND INTEGER IF NOT RETURN 404 WITH THE PERMISSION ENTRY
        $permission_id = Permission::find($permission);
        if (!$permission_id) {
            return response()->json(['error' => 'Permission ' . $permission . ' not found'], 404);
        }

        return response()->json(1);
    }
}
