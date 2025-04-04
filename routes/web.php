<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Merchant\ProductCategoryController;
use App\Http\Controllers\Merchant\ProductController;
use App\Http\Controllers\Merchant\ProfileController;
use App\Http\Controllers\Merchant\TransactionController;
use App\Http\Controllers\Developer\MerchantController;
use App\Http\Controllers\UserController;
use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/documentation-api', function () {
    $apiJsonPath = base_path('public/api.json');

    if (! File::exists($apiJsonPath)) {
        Artisan::call('scramble:export');
    }

    return view('scramble::docs', [
        'spec' => file_get_contents($apiJsonPath),
        'config' => Scramble::getGeneratorConfig('default'),
    ]);
});

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/enter-pin', [DashboardController::class, 'pin'])->name('pin.setup');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    // merchant routes
    Route::group(['prefix' => 'merchant', 'as' => 'merchant.'], function () {
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
        Route::controller(TransactionController::class)->name('transactions.')->group(function () {
            Route::post('/transactions', 'store')->name('store')->middleware('permission:transaction-create');
            Route::get('/transactions', 'index')->name('index')->middleware('permission:transaction-read');
            Route::get('/transactions-data', 'data')->name('data')->middleware('permission:transaction-read');
            Route::get('/merchant-user-qr-code/{user}', 'qrCode')->name('qr-code')->middleware('permission:transaction-create');
            Route::get('/transactions-print-invoice/{order}', 'printInvoice')->name('printInvoice');
        });

        Route::controller(ProfileController::class)->name('profile.')->group(function () {
            Route::get('/profile', 'index')->name('index')->middleware('permission:merchant-read');
            Route::put('/profile/{merchant}', 'update')->name('update')->middleware('permission:merchant-update');
        });
    });
    // end merchant routes

    // developer routes
    Route::group(['prefix' => 'developer', 'as' => 'developer.'], function() {
        Route::controller(MerchantController::class)->name('merchant.')->group(function () {
            Route::get('/merchant', 'index')->name('index')->middleware('permission:merchant-read');
            Route::post('/merchant', 'store')->name('store')->middleware('permission:merchant-create');
            Route::get('/merchant-data', 'data')->name('data')->middleware('permission:merchant-read');
            Route::delete('/merchant/{productCategory}', 'destroy')->name('destroy')->middleware('permission:product_category-delete');
            Route::put('/merchant/{productCategory}', 'update')->name('update')->middleware('permission:product_category-update');
        });
    });
});
