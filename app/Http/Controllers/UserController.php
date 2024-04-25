<?php

namespace App\Http\Controllers;

use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/user",
     *      summary="Get all users",
     *      tags={"Users"},
     *      security={{"bearerAuth": {}}},
     *          @OA\Response(
     *          response=200,
     *          description="List of users",
     *              @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/User")
     *              )
     *          ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      )
     * )
     *
     */
    public function index()
    {
        return User::all();
    }

    /**
     * @OA\Post(
     *      path="/api/user",
     *      summary="Store a new user",
     *      tags={"Users"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"names","email","password","typeuser_id"},
     *              @OA\Property(property="names", type="string", example="D'Gush Frontend", description="Full name of the user"),
     *              @OA\Property(property="email", type="string", example="dgush@frontend.com", description="Email of the user"),
     *              @OA\Property(property="password", type="string", example="12345678", description="Password of the user"),
     *              @OA\Property(property="typeuser_id", type="integer", example="2", description="Type of user")
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="User created",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="names", type="array",
     *                      @OA\Items(type="string", example="The names field is required.")
     *                  ),
     *                  @OA\Property(property="email", type="array",
     *                      @OA\Items(type="string", example="The email field is required.")
     *                  ),
     *                  @OA\Property(property="password", type="array",
     *                      @OA\Items(type="string", example="The password field is required.")
     *                  ),
     *                  @OA\Property(property="typeuser_id", type="array",
     *                      @OA\Items(type="string", example="The typeuser id field is required.")
     *                  )
     *              )
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Typeuser or User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Typeuser not found")
     *          )
     *      )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/api/user/{id}",
     *     summary="Get user by ID",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of user",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User found",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     *
     */
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

    /**
     * @OA\Put(
     *     path="/api/user/{id}",
     *     summary="Update user by ID",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of user",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"names","email","password","typeuser_id"},
     *              @OA\Property(property="names", type="string", example="D'Gush Frontend", description="Full name of the user"),
     *              @OA\Property(property="email", type="string", example="dgush@gmail.com", description="Email of the user"),
     *              @OA\Property(property="password", type="string", example="12345678", description="Password of the user"),
     *              @OA\Property(property="typeuser_id", type="integer", example="2", description="Type of user")
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User updated",
     *          @OA\JsonContent(ref="#/components/schemas/User")
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="Typeuser or User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     ),
     *     @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="names", type="array",
     *                      @OA\Items(type="string", example="The names field is required.")
     *                  ),
     *                  @OA\Property(property="email", type="array",
     *                      @OA\Items(type="string", example="The email field is required.")
     *                  ),
     *                  @OA\Property(property="password", type="array",
     *                      @OA\Items(type="string", example="The password field is required.")
     *                  ),
     *                  @OA\Property(property="typeuser_id", type="array",
     *                      @OA\Items(type="string", example="The typeuser id field is required.")
     *                  )
     *              )
     *         )
     *     )
     * )
     *
     */
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
     * @OA\Delete(
     *     path="/api/user/{id}",
     *     summary="Delete user by ID",
     *     tags={"Users"},
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID of user",
     *          required=true,
     *          example="1",
     *          @OA\Schema(
     *              type="string"
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="User deleted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User deleted")
     *          )
     *     ),
     *     @OA\Response(
     *          response=404,
     *          description="User not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="User not found")
     *          )
     *     ),
     *     @OA\Response(
     *          response=400,
     *          description="You cannot delete the admin user",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="You cannot delete the admin user")
     *          )
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated.")
     *          )
     *     )
     * )
     *
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
