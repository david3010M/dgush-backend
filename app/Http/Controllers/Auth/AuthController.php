<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgetPassword;
use App\Models\ForgetPasswordCode;
use App\Models\Person;
use App\Models\TypeUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class AuthController extends Controller
{
    /**
     * Log out user.
     * @OA\Get (
     *     path="/dgush-backend/public/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     security={{"bearerAuth":{}}},
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
     * Authenticate user and generate access token
     * @OA\Post (
     *     path="/dgush-backend/public/api/login",
     *     tags={"Authentication"},
     *     summary="Login user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="example@gmail.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="abcd1234"
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
     *                  property="optionMenuAccess",
     *                  type="string",
     *                  example="1, 2, 3, 4"
     *              ),
     *              @OA\Property(
     *                  property="permissions",
     *                  type="string",
     *                  example="1, 2, 3, 4, 5, 6, 7, 8, 9, 10",
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *          response=401,
     *          description="User not authenticated",
     *           @OA\JsonContent(
     *               @OA\Property(
     *                   property="message",
     *                   type="string",
     *                   example="Unauthorized."
     *              )
     *           )
     *      ),
     *       @OA\Response(
     *           response=400,
     *           description="Credentials are invalid",
     *            @OA\JsonContent(
     *                @OA\Property(
     *                    property="message",
     *                    type="string",
     *                    example="Invalid credentials."
     *               )
     *            )
     *       )
     * )
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $token = $user->createToken('AuthToken', expiresAt: now()->addDays(7));
            $typeuser = $user->typeuser()->first();
            $typeuserAccess = $typeuser->getAccess($typeuser->id);

            return response()->json([
                'access_token' => $token->plainTextToken,
//                'expires_at' => Carbon::parse($token->accessToken->expires_at)->toDateTimeString(),
                'user' => $user,
                'typeuser' => $typeuser,
//                'optionMenuAccess' => $typeuserAccess,
//                'permissions' => $typeuserHasPermission

            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }

    /**
     * Get user and access token
     * @OA\Get (
     *     path="/dgush-backend/public/api/authenticate",
     *     tags={"Authentication"},
     *     summary="Authenticate user",
     *     security={{"bearerAuth":{}}},
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
     *               @OA\Property(
     *                   property="optionMenuAccess",
     *                   type="string",
     *                   example="1, 2, 3, 4"
     *               ),
     *               @OA\Property(
     *                   property="permissions",
     *                   type="string",
     *                   example="1, 2, 3, 4, 5, 6, 7, 8, 9, 10",
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

        if ($user) {
            $user = User::find($user->id);

            $user->tokens()->delete();

            $token = $user->createToken('AuthToken', ['expires_at' => now()->addDays(7)])->plainTextToken;
            $typeuser = $user->typeuser()->first();

            return response()->json([
                'access_token' => $token,
                'user' => $user,
                'typeuser' => $typeuser,
            ]);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }


    /**
     * Register a new user
     * @OA\Post (
     *     path="/dgush-backend/public/api/register",
     *     tags={"Authentication"},
     *     summary="Register user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name",type="string",example="D Gush"),
     *             @OA\Property(property="lastnames",type="string",example="Gush"),
     *             @OA\Property(property="email",type="string",example="dgush123@gmail.com"),
     *             @OA\Property(property="password",type="string",example="abcd1234"),
     *             @OA\Property(property="accept_terms",type="boolean",example="true")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered",
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
     *                     @OA\Property(property="id",type="number",example="11"),
     *                     @OA\Property(property="names",type="string",example="D Gush"),
     *                     @OA\Property(property="email",type="string",example="dgush123@gmail.com"),
     *                     @OA\Property(property="typeuser_id",type="number",example="2")
     *                )
     *             ),
     *             @OA\Property(property="typeuser",type="array",
     *                  @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id",type="number",example="2"),
     *                      @OA\Property(property="name",type="string",example="User")
     *                  )
     *              ),
     *              @OA\Property(property="optionMenuAccess",type="string",example="1, 2, 3, 4"),
     *              @OA\Property(property="permissions",type="string",example="1, 2, 3, 4, 5, 6, 7, 8, 9, 10")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="array",
     *                 @OA\Items(
     *                     type="string",
     *                     example="The email has already been taken."
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="You must accept the terms and conditions",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error", type="string", example="You must accept the terms and conditions"
     *             )
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lastnames' => 'required|string',
            'dni' => [
                'required',
                'string',
                'min:8',
                'max:8',
                Rule::unique('people', 'dni')->whereNull('deleted_at')
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('people', 'email')->whereNull('deleted_at')
            ],
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
            'accept_terms' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if (!$request->accept_terms) {
            return response()->json(['error' => 'You must accept the terms and conditions'], 400);
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
            'typeuser_id' => 2,
        ]);

        $user = User::create([
            'names' => $person->names,
            'lastnames' => $person->fatherSurname . ' ' . $person->motherSurname,
            'email' => $person->email,
            'password' => bcrypt($request->input('password')),
            'typeuser_id' => 2,
            'person_id' => $person->id,
        ]);

        $typeuser = TypeUser::find(2);

        $optionMenuAccess = $typeuser->getAccess($typeuser->id);

        $token = $user->createToken('AuthToken', expiresAt: now()->addDays(7));

        return response()->json(
            [
                'access_token' => $token->plainTextToken,
                'expires_at' => Carbon::parse($token->accessToken->expires_at)->toDateTimeString(),
                'user' => $user,
                'typeuser' => $typeuser,
                'optionMenuAccess' => $optionMenuAccess,
            ]
        );
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $user = User::where('email', $request->email)->first();

        $code = random_int(100000, 999999);

        ForgetPasswordCode::where('email', $request->email)->update(['used' => true]);

        ForgetPasswordCode::create([
            'email' => $request->email,
            'code' => Hash::make($code),
            'expires_at' => now()->addMinutes(10),
            'user_id' => $user->id,
        ]);

        Mail::to($request->input('email'))
            ->send(new ForgetPassword($code, $user->names));

        $response = [
            'message' => 'El código ha sido enviado a tu correo electrónico',
            'email' => $request->input('email')
        ];

        return response()->json($response);
    }

    public function validateCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'code' => 'required|numeric',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $code = ForgetPasswordCode::where('email', $request->email)
            ->where('used', false)
            ->where('expires_at', '>=', now())
            ->first();

        if ($code && Hash::check($request->code, $code->code)) {
            $user = User::find($code->user_id);
            $user->password = bcrypt($request->password);
            $user->save();

            $code->used = true;
            $code->used_at = now();
            $code->save();

            return response()->json(['message' => 'Contraseña actualizada correctamente']);
        } else {
            return response()->json(['error' => 'Código inválido o expirado'], 422);
        }
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $user = auth('sanctum')->user();

        if (Hash::check($request->current_password, $user->password)) {
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json(['message' => 'Contraseña actualizada correctamente']);
        } else {
            return response()->json(['error' => 'Contraseña actual incorrecta'], 422);
        }
    }


}
