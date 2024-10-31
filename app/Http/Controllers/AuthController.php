<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginValidation;
use App\Http\Requests\PasswordResetLinkValidation;
use App\Http\Requests\PasswordResetValidation;
use App\Http\Requests\RegisterValidation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Deekshith"),
     *             @OA\Property(property="email", type="string", format="email", example="deekshith@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Registration successful"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Deekshith"),
     *                 @OA\Property(property="email", type="string", format="email", example="deekshith@gmail.com"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-31T10:12:34.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-31T10:12:34.000000Z")
     *             ),
     *             @OA\Property(property="token", type="string", example="1|jnr7MZLyASiNmMS5zr4U9irIJ5yU1frpcQIrWGt5fc75690c")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Registration failed"),
     *     @OA\Response(response=400, description="Invalid input data.")
     * )
     */

    public function register(RegisterValidation $request)
    {
        try{
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
    
            // Generate a Sanctum token
            $token = $user->createToken('API Token')->plainTextToken;
    
            return response()->json([
                'message' => 'Registration successful',
                'user' => $user,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json([
                'message' => 'Registration failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="deekshith@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="token", type="string", example="4|LOhgmzL2UzgWIjYELybQNA1qhJXMkm4jlpoiyOW49cbbe66b"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Deekshith"),
     *                 @OA\Property(property="email", type="string", format="email", example="deekshith@gmail.com"),
     *                 @OA\Property(property="email_verified_at", type="string", nullable=true, example=null),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-31T10:12:34.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-10-31T10:12:34.000000Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Invalid login credentials"),
     *     @OA\Response(response=500, description="Login failed")
     * )
     */

    public function login(LoginValidation $request)
    {
        try{
            $user = User::where('email', $request->email)->first();
    
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid login credentials'], 401);
            }
    
            // Create a Sanctum token
            $token = $user->createToken('API Token')->plainTextToken;
    
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            // Handle the exception
            return response()->json([
                'message' => 'Login failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/password/reset-request",
     *     summary="Request a password reset link",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="deekshith123@gmail.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="We have emailed your password reset link.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Too many requests, please wait before retrying.",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Please wait before retrying.")
     *         )
     *     )
     * )
     */

    public function sendPasswordResetLink(PasswordResetLinkValidation $request)
    {

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 500);
    }

    /**
     * @OA\Post(
     *     path="/api/password/reset",
     *     summary="Reset the user password",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string", format="email", example="deekshith@gmail.com"),
     *             @OA\Property(property="password", type="string", example="456789"),
     *             @OA\Property(property="password_confirmation", type="string", example="456789"),
     *             @OA\Property(property="token", type="string", example="$2y$12$m47IbrZrtR8/af8yfgt1SuhjlFTDgSko3CMujFn/k3EDI08vw9Ti6")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid password reset token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="This password reset token is invalid.")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */

    public function passwordReset(PasswordResetValidation $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 500);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Log out the user",
     *     tags={"Auth"},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logout failed due to server error.")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}
