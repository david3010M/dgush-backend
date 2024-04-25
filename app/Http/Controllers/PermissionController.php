<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{

    public function index()
    {
        return Permission::all();
    }

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
//        FIND PERMISSION
        $permission = Permission::find($id);

//        ERROR MESSAGE
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

//        VALIDATE DATA
        $request->validate([
            'name' => 'required|string|unique:permission,name,' . $id,
            'route' => 'required|string|unique:permission,route,' . $id,
        ]);

//        UPDATE PERMISSION
        $permission->update($request->all());
        return $permission;
    }

    public function destroy(int $id)
    {
//        FIND PERMISSION
        $permission = Permission::find($id);

//        ERROR MESSAGE
        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

//        DELETE PERMISSION
        $permission->delete();
        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
