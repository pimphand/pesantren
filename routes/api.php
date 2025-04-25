<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('api.login')->middleware('throttle:5,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Student Routes
    Route::prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index']);
        Route::get('bank-mutation', [StudentController::class, 'bankMutation']);
        Route::get('{id}/qrCode', [StudentController::class, 'qrCode']);
        Route::post('parent-update', [StudentController::class, 'updateProfile']);
    });

    // Payment Routes
    Route::prefix('payments')->group(function () {
        Route::get('histories', [PaymentController::class, 'index']);
        Route::get('generate-key', [PaymentController::class, 'generateKey']);
        Route::post('top-up', [PaymentController::class, 'store'])->middleware('throttle:3,1');
        Route::get('top-up/{payment}', [PaymentController::class, 'show']);
        Route::get('banks', [PaymentController::class, 'banks']);
    });

    Route::get('home', [HomeController::class, 'index']);
});
