<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PinMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Memastikan user sudah login
        if (Auth::check()) {
            $user = Auth::user();

            // Mengecek apakah user memiliki role 'merchant' dan belum mengatur PIN
            if ($user->hasRole('merchant') && !$user->pin) {
                // Jika user merchant dan belum memiliki PIN, alihkan ke halaman untuk mengisi PIN
                return redirect()->route('pin.setup');
            }

            // Jika PIN belum ada di session, alihkan ke halaman PIN setup
            if ($user->hasRole('merchant') && !session()->has('pin')) {
                return redirect()->route('pin.setup');
            }
        }

        return $next($request);
    }
}
