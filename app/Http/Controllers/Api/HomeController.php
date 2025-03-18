<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Dedoc\Scramble\Attributes\HeaderParameter;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * Home.
     */
    #[HeaderParameter('Authorization', 'Bearer {token}')]
    public function index(): JsonResponse
    {
        return response()->json([
            'message' => 'success',
            'data' => 'Welcome to the Home Page'
        ]);
    }
}
