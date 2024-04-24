<?php

namespace App\Http\Controllers;

use App\Models\OptionMenu;
use App\Models\TypeUser;
use Illuminate\Http\Request;

class TypeUserController extends Controller
{
    public function index()
    {
        return TypeUser::all();
    }

    public function store(Request $request)
    {
//        Validate data
        $request->validate([
            'name' => 'required|string|unique:typeuser',
        ]);

//        Create a new Type User
        return TypeUser::create($request->all());
    }

    public function show(int $id)
    {
//        Find Type User
        $typeUser = TypeUser::find($id);

        if (!$typeUser) {
            return response()->json(
                ['message' => 'Type User not found'], 404
            );
        }

        return $typeUser->load('access', 'hasPermission');
    }


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

//        Delete Type User
        $typeUser->delete();
        return response()->json(
            ['message' => 'Type User deleted successfully']
        );
    }
}
