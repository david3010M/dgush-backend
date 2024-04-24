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
    public function index()
    {
        return HasPermission::all();
    }

    public function store(Request $request)
    {
//        VALIDATE DATA
        $validation = $this->validateHasPermission($request);

        if ($validation->getStatusCode() !== 200) {
            return $validation;
        }

//        TYPEUSER ID
        $typeuser_id = $request->input('typeuser_id');

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

    public function show(int $id)
    {
//        FIND PERMISSION
        $hasPermission = HasPermission::find($id);

        if (!$hasPermission) {
            return response()->json(['message' => 'HasPermission not found'], 404);
        }

        return $hasPermission;
    }

    public function update(Request $request, int $id)
    {
////        FIND PERMISSION
//        $hasPermission = HasPermission::find($id);
//
////        ERROR MESSAGE
//        if (!$hasPermission) {
//            return response()->json(['message' => 'HasPermission not found'], 404);
//        }


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
