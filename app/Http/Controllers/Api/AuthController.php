<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use Dedoc\Scramble\Attributes\HeaderParameter;

class AuthController extends Controller
{
    /**
     * User Login.
     *
     * @response array{token: string, message: string}
     */
    public function login(LoginRequest $request): \Illuminate\Http\JsonResponse
    {
        if (! auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'errors' => [
                    'email' => ['Invalid credentials'],
                ],
            ], 401);
        }

        $user = auth()->user();

        if (! $user->hasRole('orang_tua')) {
            return response()->json([
                'errors' => [
                    'email' => ['Unauthorized role'],
                ],
            ], 403);
        }

        $user->tokens()->delete();

        $token = $user->createToken('login')->plainTextToken;
        $this->createLog('Login Api', 'Login Api', $user, $request->except(['password', 'pin']), 'login');

        return response()->json([
            'message' => 'Login successful',
            'token' => 'Bearer '.$token,
        ]);
    }

    /**
     * User logout.
     *
     * @response array{message: string}
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function logout(): \Illuminate\Http\JsonResponse
    {
        $this->createLog('Logout Api', 'Logout Api', null, null, 'logout');
        request()->user()->tokens()->delete();

        return response()->json([
            'message' => 'logout berhasil',
        ]);
    }
}
