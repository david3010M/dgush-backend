<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{

    /**
     * Authenticate user and generate access token
     * @OA\Post (
     *     path="/api/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="dgush@gmail.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="12345678"
     *             )
     *         )
     *     ),
     *
     *
     *     @OA\Response(
     *         response=200,
     *         description="User logged in",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 example="10|DhvyeOsYelrCP7YXyx0RGG2E9KFG2PE9RFEjqWwwe69d7147",
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="11"
     *                     ),
     *                     @OA\Property(
     *                         property="names",
     *                         type="string",
     *                         example="D Gush"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="dgush@gmail.com"
     *                     ),
     *                     @OA\Property(
     *                          property="typeuser_id",
     *                          type="number",
     *                          example="2"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2024-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2024-02-23T12:13:45.000000Z"
     *                     ),
     *                      @OA\Property(
     *                          property="deleted_at",
     *                          type="string",
     *                          example="null",
     *                      )
     *                 )
     *             ),
     *             @OA\Property(
     *                  property="typeuser",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                          example="6"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Admin"
     *                      ),
     *                      @OA\Property(
     *                          property="created_at",
     *                          type="string",
     *                          example="2024-02-23T00:09:16.000000Z"
     *                      ),
     *                      @OA\Property(
     *                          property="updated_at",
     *                          type="string",
     *                          example="2024-02-23T12:13:45.000000Z"
     *                      ),
     *                       @OA\Property(
     *                           property="deleted_at",
     *                           type="string",
     *                           example="null",
     *                       )
     *                  )
     *              ),
     *              @OA\Property(
     *                   property="tokenInfo",
     *                   type="array",
     *                   @OA\Items(
     *                       type="object",
     *                       @OA\Property(
     *                           property="id",
     *                           type="number",
     *                           example="1"
     *                       ),
     *                       @OA\Property(
     *                           property="name",
     *                           type="string",
     *                           example="AuthToken"
     *                       ),
     *                       @OA\Property(
     *                           property="abilities",
     *                           type="array",
     *                           @OA\Items(
     *                               type="object",
     *                               example="*"
     *                           )
     *                       ),
     *                       @OA\Property(
     *                           property="expires_at",
     *                           type="string",
     *                           example="2024-04-22T15:07:59.000000Z",
     *                       ),
     *                       @OA\Property(
     *                           property="tokenable_id",
     *                           type="number",
     *                           example="11",
     *                       ),
     *                       @OA\Property(
     *                           property="tokenable_type",
     *                           type="string",
     *                           example="App\\Models\\User",
     *                       ),
     *                       @OA\Property(
     *                           property="created_at",
     *                           type="string",
     *                           example="2024-02-23T00:09:16.000000Z"
     *                       ),
     *                       @OA\Property(
     *                           property="updated_at",
     *                           type="string",
     *                           example="2024-02-23T12:13:45.000000Z"
     *                       ),
     *                   )
     *               )
     *         )
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="User not authenticated",
     *           @OA\JsonContent(
     *               @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Unauthenticated."
     *              )
     *           )
     *      )
     * )
     */
    public function login(Request $request)
    {
        // Validar las credenciales del usuario
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verificar si las credenciales son válidas
        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        // Intentar autenticar al usuario
        if (Auth::attempt($credentials)) {
            // Obtener el usuario autenticado
            $user = Auth::user();

            // Generar un token de acceso para el usuario
            $token = $user->createToken('AuthToken', expiresAt: now()->addMinutes(240));

            $typeuser = $user->typeuser()->first()->load('access', 'hasPermission');

            return response()->json([
                'access_token' => $token->plainTextToken,
                'user' => $user,
                'typeuser' => $typeuser,
                'tokenInfo' => $token->accessToken
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }

    /**
     * Log out user.
     * @OA\Get (
     *     path="/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *      @OA\Response(
     *          response=200,
     *          description="User logged out",
     *           @OA\JsonContent(
     *               @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Logged out successfully."
     *              )
     *           )
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="User not authenticated",
     *           @OA\JsonContent(
     *               @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Unauthenticated."
     *              )
     *           )
     *      )
     * )
     */
    public function logout(Request $request)
    {
        if (auth('sanctum')->user()) {
            auth('sanctum')->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }

    /**
     * Get user and access token
     * @OA\Get (
     *     path="/api/authenticate",
     *     tags={"Authentication"},
     *     summary="Authenticate user",
     *     @OA\Response(
     *         response=200,
     *         description="User logged in",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="access_token",
     *                 type="string",
     *                 example="10|DhvyeOsYelrCP7YXyx0RGG2E9KFG2PE9RFEjqWwwe69d7147",
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(
     *                         property="id",
     *                         type="number",
     *                         example="11"
     *                     ),
     *                     @OA\Property(
     *                         property="names",
     *                         type="string",
     *                         example="D Gush"
     *                     ),
     *                     @OA\Property(
     *                         property="email",
     *                         type="string",
     *                         example="dgush@gmail.com"
     *                     ),
     *                     @OA\Property(
     *                          property="typeuser_id",
     *                          type="number",
     *                          example="2"
     *                     ),
     *                     @OA\Property(
     *                         property="created_at",
     *                         type="string",
     *                         example="2024-02-23T00:09:16.000000Z"
     *                     ),
     *                     @OA\Property(
     *                         property="updated_at",
     *                         type="string",
     *                         example="2024-02-23T12:13:45.000000Z"
     *                     ),
     *                      @OA\Property(
     *                          property="deleted_at",
     *                          type="string",
     *                          example="null",
     *                      )
     *                 )
     *             ),
     *             @OA\Property(
     *                  property="typeuser",
     *                  type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(
     *                          property="id",
     *                          type="number",
     *                          example="6"
     *                      ),
     *                      @OA\Property(
     *                          property="name",
     *                          type="string",
     *                          example="Admin"
     *                      ),
     *                      @OA\Property(
     *                          property="created_at",
     *                          type="string",
     *                          example="2024-02-23T00:09:16.000000Z"
     *                      ),
     *                      @OA\Property(
     *                          property="updated_at",
     *                          type="string",
     *                          example="2024-02-23T12:13:45.000000Z"
     *                      ),
     *                       @OA\Property(
     *                           property="deleted_at",
     *                           type="string",
     *                           example="null",
     *                       )
     *                  )
     *              ),
     *              @OA\Property(
     *                   property="tokenInfo",
     *                   type="array",
     *                   @OA\Items(
     *                       type="object",
     *                       @OA\Property(
     *                           property="id",
     *                           type="number",
     *                           example="1"
     *                       ),
     *                       @OA\Property(
     *                           property="name",
     *                           type="string",
     *                           example="AuthToken"
     *                       ),
     *                       @OA\Property(
     *                           property="abilities",
     *                           type="array",
     *                           @OA\Items(
     *                               type="object",
     *                               example="*"
     *                           )
     *                       ),
     *                       @OA\Property(
     *                           property="expires_at",
     *                           type="string",
     *                           example="2024-04-22T15:07:59.000000Z",
     *                       ),
     *                       @OA\Property(
     *                           property="tokenable_id",
     *                           type="number",
     *                           example="11",
     *                       ),
     *                       @OA\Property(
     *                           property="tokenable_type",
     *                           type="string",
     *                           example="App\\Models\\User",
     *                       ),
     *                       @OA\Property(
     *                           property="created_at",
     *                           type="string",
     *                           example="2024-02-23T00:09:16.000000Z"
     *                       ),
     *                       @OA\Property(
     *                           property="updated_at",
     *                           type="string",
     *                           example="2024-02-23T12:13:45.000000Z"
     *                       ),
     *                   )
     *               )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="User not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(
     *                  property="message",
     *                  type="string",
     *                  example="Unauthenticated."
     *             )
     *          )
     *     )
     * )
     */
    public function authenticate(Request $request)
    {
        $user = auth('sanctum')->user();
        $token = $request->bearerToken();

        if ($user) {
            $typeuser = $user->typeuser()->first()->load('access', 'hasPermission');

            return response()->json([
                'access_token' => $token,
                'user' => $user,
                'typeuser' => $typeuser,
                'tokenInfo' => $user->currentAccessToken()
            ]);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }


    /**
     * Método para registrar un nuevo usuario.
     */
    public function register(Request $request)
    {
        // Validar los datos de registro del usuario
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);

        // Verificar si los datos son válidos
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Crear un nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generar un token de acceso para el nuevo usuario
        $token = $user->createToken('AuthToken')->plainTextToken;

        // Devolver el usuario completo junto con el token en la respuesta
        return response()->json(['user' => $user, 'token' => $token], 201);
    }
}
