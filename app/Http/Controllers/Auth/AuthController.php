<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TypeUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

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
            $token = $user->createToken('AuthToken', expiresAt: now()->addMinutes(1))->plainTextToken;

            // Devolver el usuario completo junto con el token en la respuesta
            return response()->json([
                'user' => $user,
                'access_token' => $token,
                'typeuser' => $user->typeuser
            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }

    /**
     * Método para cerrar sesión y revocar el token de acceso del usuario.
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

    public function authenticate(Request $request)
    {
        $user = auth('sanctum')->user();
        $token = $request->bearerToken();

        if ($user) {
            $typeuser = $user->typeuser()->first();

            return response()->json([
                'user' => $user,
                'access_token' => $token,
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
