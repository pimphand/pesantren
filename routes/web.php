<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Merchant\ProductCategoryController;
use App\Http\Controllers\Merchant\ProductController;
use App\Http\Controllers\Merchant\TransactionController;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Route::get('/documentation-api', function () {
    return view('scramble::docs', [
        'spec' => file_get_contents(base_path('public/api.json')),
        'config' => Scramble::getGeneratorConfig('default'),
    ]);
});

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // merchant routes
    Route::controller(ProductController::class)->name('products.')->group(function () {
        Route::get('/products', 'index')->name('index')->middleware('permission:product-read');
        Route::post('/products', 'store')->name('store')->middleware('permission:product-create');
        Route::get('/products-data', 'data')->name('data')->middleware('permission:product-read');
        Route::delete('/products/{product}', 'destroy')->name('destroy')->middleware('permission:product-delete');
        Route::put('/products/{product}', 'update')->name('update')->middleware('permission:product-update');
    });
    Route::controller(ProductCategoryController::class)->name('categories.')->group(function () {
        Route::get('/categories', 'index')->name('index')->middleware('permission:product_category-read');
        Route::post('/categories', 'store')->name('store')->middleware('permission:product_category-create');
        Route::get('/categories-data', 'data')->name('data')->middleware('permission:product_category-read');
        Route::delete('/categories/{productCategory}', 'destroy')->name('destroy')->middleware('permission:product_category-delete');
        Route::put('/categories/{productCategory}', 'update')->name('update')->middleware('permission:product_category-update');
    });
    Route::controller(TransactionController::class)->name('merchant.transactions.')->group(function () {
        Route::post('/merchant/transactions', 'store')->name('store')->middleware('permission:transaction-create');
        Route::get('/merchant/transactions', 'index')->name('index')->middleware('permission:transaction-read');
        Route::get('/merchant/transactions-data', 'data')->name('data')->middleware('permission:transaction-read');
        Route::get('/merchant-user-qr-code/{user}', 'qrCode')->name('qr-code')->middleware('permission:transaction-read');
    });
    //end merchant routes
});
