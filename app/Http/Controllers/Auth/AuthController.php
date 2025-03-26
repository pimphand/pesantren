<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (! auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'errors' => [
                    'email' => ['Email atau password salah'],
                ],
            ], 401);
        }

        $user = auth()->user();
        $validRoles = ['developer', 'pengawas', 'kepala_ponpes', 'admin', 'merchant'];
        $roleValid = false;

        foreach ($validRoles as $role) {
            if ($user->hasRole($role)) {
                $roleValid = true;
                break;
            }
        }

        if (! $roleValid) {
            auth()->logout();  // Log the user out if they don't have a valid role

            return response()->json([
                'errors' => [
                    'role' => ['Akses ditolak, role tidak valid'],
                ],
            ], 403);  // Forbidden response
        }

        return response()->json([
            'message' => 'Login berhasil',
        ]);
    }

    public function logout(): \Illuminate\Http\JsonResponse
    {
        auth()->logout();
        Session::forget('pin');
        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}
