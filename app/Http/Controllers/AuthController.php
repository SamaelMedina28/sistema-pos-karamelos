<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Registrar un nuevo usuario",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuario registrado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            // 'password_confirmation' => 'required|string|min:8',
        ]);

        // Si la validación falla, retorna errores
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Crear el usuario si la validación pasa
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Retornar el usuario creado
        return response()->json(['user' => $user], 201);
    }

    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Iniciar sesión",
     *     tags={"Autenticación"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="test@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login exitoso, retorna usuario y cookie token"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciales inválidas"
     *     )
     * )
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        // Si la validación falla, retorna errores
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Extraer unicamente email y password del request
        $credentials = $request->only('email', 'password');

        try {
            // Si las credenciales son correctas genera un token y si no retorna error
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
            // Si todo va bien, retorna el usuario y el token en forma de cookie
            return response()->json(['user' => JWTAuth::user()], 200)->cookie(
                'token',
                $token,
                config('jwt.ttl'),
                '/',
                null,
                true, // Secure
                true, // HttpOnly
                false,
                'none'
            ); // Cookie por 1 día
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token', 'exception' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user",
     *     summary="Obtener usuario autenticado",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     */
    public function getUser(Request $request)
    {
        // Obtener el token de la cookie
        $token = $request->cookie('token');

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            // Verificate si el token es valido y obtiene el usuario
            /* TODO: alternativas
            JWTAuth::setToken($token)->checkOrFail();
            JWTAuth::setToken($token)->authenticate();
            $user = JWTAuth::parseToken()->authenticate(); //-- Usa el token del request (header o cookie)

             */
            $user = JWTAuth::setToken($token)->user();
            // Si no se encuentra el usuario con ese token
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            // Si el token es valido, retorna la información del usuario
            return response()->json(['user' => $user], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not decode token', 'exception' => $e->getMessage()], 401);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Cerrar sesión",
     *     tags={"Autenticación"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout exitoso"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Obtener el token de la cookie
        $token = $request->cookie('token');
        if ($token) {
            try {
                // Invalida el token que le pasamos
                JWTAuth::setToken($token)->invalidate();
                // Retorna una respuesta exitosa y elimina la cookie
                // TODO: revisar si es necesario el withoutCookie

                return response()->json(['message' => 'Successfully logged out'], 200)->cookie(
                    'token',
                    '', // Valor vacío
                    -1, // Tiempo negativo para expirar inmediatamente
                    '/',
                    null,
                    true,
                    true,
                    false,
                    'Strict'
                );
            } catch (JWTException $e) {
                return response()->json(['error' => 'Could not invalidate token', 'exception' => $e->getMessage()], 500);
            }
        }
    }
}
