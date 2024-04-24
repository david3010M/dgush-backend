<?php

namespace App\Http\Controllers;

use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return User::all();
    }

    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'names' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'typeuser_id' => 'required|integer'
        ]);

        // Validar typeuser_id
        if (!Typeuser::find($request->typeuser_id)) {
            return response()->json(
                ['message' => 'Typeuser not found'], 404
            );
        }

        // Cifrar la contraseña
        $hashedPassword = Hash::make($request->password);

        // Crear un nuevo usuario con la contraseña cifrada
        return User::create([
            'names' => $request->names,
            'email' => $request->email,
            'password' => $hashedPassword,
            'typeuser_id' => $request->typeuser_id
        ]);
    }

    public function show(string $id): User|JsonResponse
    {
//        Find a user by ID
        $user = User::find($id);

//        If the user is not found, return a 404 response
        if (!$user) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }

//        Return the user
        return $user;
    }

    public function update(Request $request, string $id): User|JsonResponse
    {
//        Find a user by ID
        $user = User::find($id);

//        If the user is not found, return a 404 response
        if (!$user) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }

//        Validate data
        $request->validate([
            'names' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required|string',
            'typeuser_id' => 'required|integer'
        ]);

        // Cifrar la contraseña
        $hashedPassword = Hash::make($request->password);

//        Validate typeuser_id
        if (!Typeuser::find($request->typeuser_id)) {
            return response()->json(
                ['message' => 'Typeuser not found'], 404
            );
        }

//        Update with password hashed
        $user->update([
            'names' => $request->names,
            'email' => $request->email,
            'password' => $hashedPassword,
            'typeuser_id' => $request->typeuser_id
        ]);

//        Return the user
        return $user;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
//        Find a user by ID
        $user = User::find($id);

//        If the user is not found, return a 404 response
        if (!$user) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }

//        If the user is an admin, return a 400 response
        if ($user->typeuser_id === 1) {
            return response()->json(
                ['message' => 'You cannot delete the admin user'], 400
            );
        }

//        Delete the user
        $user->delete();

//        Return a 204 response
        return response()->json(
            ['message' => 'User deleted']
        );

    }
}
