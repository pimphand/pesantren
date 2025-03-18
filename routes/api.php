<?php

use Illuminate\Support\Facades\Route;

Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    Route::get('student-parents', [\App\Http\Controllers\Api\StudentController::class, 'index']);
    Route::get('student-parents/{id}/bank-mutation', [\App\Http\Controllers\Api\StudentController::class, 'bankMutation']);
    Route::post('student-parents', [\App\Http\Controllers\Api\StudentController::class, 'updateProfile']);

    Route::get('payment-histories', [\App\Http\Controllers\Api\PaymentController::class, 'index']);
    Route::post('top-up', [\App\Http\Controllers\Api\PaymentController::class, 'store']);
    Route::get('top-up/{payment}', [\App\Http\Controllers\Api\PaymentController::class, 'show']);
    Route::get('list-banks', [\App\Http\Controllers\Api\PaymentController::class, 'banks']);

    Route::get('home', [\App\Http\Controllers\Api\HomeController::class, 'index']);
});
