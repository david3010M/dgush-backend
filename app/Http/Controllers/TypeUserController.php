<?php

namespace App\Http\Controllers;

use App\Models\OptionMenu;
use App\Models\TypeUser;
use Illuminate\Http\Request;

class TypeUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TypeUser::all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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
     * Display the specified resource.
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

        return $typeUser->load('access');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TypeUser $typeUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
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

//        Delete Type User
        $typeUser->delete();
        return response()->json(
            ['message' => 'Type User deleted successfully'], 204
        );
    }
}
