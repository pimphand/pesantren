<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\HomeController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Student Routes
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('{id}/bank-mutation', [StudentController::class, 'bankMutation']);
        Route::post('parent-update', [StudentController::class, 'updateProfile']);
    });

    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::get('histories', [PaymentController::class, 'index']);
        Route::post('top-up', [PaymentController::class, 'store']);
        Route::get('top-up/{payment}', [PaymentController::class, 'show']);
        Route::get('banks', [PaymentController::class, 'banks']);
    });

    Route::get('home', [HomeController::class, 'index']);
});
