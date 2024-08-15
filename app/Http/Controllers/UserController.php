<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * @OA\Get(
     *      path="/dgush-backend/public/api/user",
     *      summary="Get all users",
     *      tags={"Users"},
     *      security={{"bearerAuth": {}}},
     *          @OA\Response(
     *          response=200,
     *          description="List of users",
     *              @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User"))
     *          ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated."))
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
     *      path="/dgush-backend/public/api/user",
     *      summary="Store a new user",
     *      tags={"Users"},
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"names","email","password","typeuser_id"},
     *              @OA\Property(property="names", type="string", example="D'Gush Frontend", description="Full name of the user"),
     *              @OA\Property(property="lastnames", type="string", example="Admin", description="Last name of the user"),
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
     *          description="Typeuser not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Typeuser not found")
     *          )
     *      )
     * )
     */
    public function store(Request $request)
    {
        if (auth()->user()->typeuser_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validator = validator()->make($request->all(), [
            'name' => 'required|string',
            'lastnames' => 'required|string',
            'dni' => [
                'required',
                'string',
                'min:8',
                'max:8',
                Rule::unique('people', 'dni')->whereNull('deleted_at'),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('people', 'email')->whereNull('deleted_at')
            ],
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'typeuser_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $lastnames = explode(' ', $request->lastnames);

        $person = Person::create([
            'dni' => $request->input('dni'),
            'names' => $request->input('name'),
            'fatherSurname' => $lastnames[0],
            'motherSurname' => $lastnames[1] ?? '',
            'email' => $request->input('email'),
            'phone' => '',
            'address' => '',
            'reference' => '',
            'district_id' => null,
            'typeuser_id' => $request->input('typeuser_id')
        ]);

        $user = User::create([
            'names' => $person->names,
            'lastnames' => $person->fatherSurname . ' ' . $person->motherSurname,
            'email' => $person->email,
            'password' => bcrypt($request->input('password')),
            'typeuser_id' => $request->input('typeuser_id'),
            'person_id' => $person->id,
        ]);

        $user = User::find($user->id);
        return response()->json($user);
    }

    /**
     * @OA\Get(
     *     path="/dgush-backend/public/api/user/{id}",
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
        if (auth()->user()->typeuser_id != 1) return response()->json(['message' => 'Unauthorized'], 401);
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        return $user;
    }

    /**
     * @OA\Put(
     *     path="/dgush-backend/public/api/user/{id}",
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
     *              @OA\Property(property="lastnames", type="string", example="Admin", description="Last name of the user"),
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
        if (auth()->user()->typeuser_id != 1) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $user = User::find($id);

        if (!$user) {
            return response()->json(
                ['message' => 'User not found'], 404
            );
        }

        $request->validate([
            'names' => 'required|string',
            'lastnames' => 'required|string',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'required|string',
            'typeuser_id' => 'required|integer'
        ]);

        $hashedPassword = Hash::make($request->password);

        if (!Typeuser::find($request->typeuser_id)) {
            return response()->json(
                ['message' => 'Typeuser not found'], 404
            );
        }

        $user->update([
            'names' => $request->names,
            'email' => $request->email,
            'password' => $hashedPassword,
            'typeuser_id' => $request->typeuser_id
        ]);

        return $user;
    }

    /**
     * @OA\Delete(
     *     path="/dgush-backend/public/api/user/{id}",
     *     summary="Delete user by ID",
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
        if (auth()->user()->typeuser_id != 1) return response()->json(['message' => 'Unauthorized'], 401);
        $user = User::find($id);
        if (!$user) return response()->json(['message' => 'User not found'], 404);
        $person = Person::find($user->person_id);
        if (!$person) return response()->json(['message' => 'Person not found'], 404);
        if ($user->typeuser_id === 1) return response()->json(['message' => 'You cannot delete the admin user'], 400);
        $person->delete();
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
