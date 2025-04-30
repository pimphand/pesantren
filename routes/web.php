<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Merchant\ProductCategoryController;
use App\Http\Controllers\Merchant\ProductController;
use App\Http\Controllers\Merchant\ProfileController;
use App\Http\Controllers\Merchant\TransactionController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PaymentController;
use Dedoc\Scramble\Scramble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/documentation-api', function (Request $request) {
    if ($request->has('force')) {
        Artisan::call('scramble:export --path=public/api.json');
    }
    $apiJsonPath = public_path('api.json');

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
            Route::get('/transaction/token', 'generateToken')->name('token');
        });

        Route::controller(ProfileController::class)->name('profile.')->group(function () {
            Route::get('/profile', 'index')->name('index')->middleware('permission:merchant-read');
            Route::put('/profile/{merchant}', 'update')->name('update')->middleware('permission:merchant-update');
        });
    });
    // end merchant routes

    // menu routes
    Route::controller(MenuController::class)->name('menu.')->group(function () {
        Route::get('/menu', 'index')->name('index')->middleware('permission:menu-read');
        Route::post('/menu', 'store')->name('store')->middleware('permission:menu-create');
        Route::get('/menu-data', 'data')->name('data')->middleware('permission:menu-read');
        Route::put('/menu/{menu}', 'update')->name('update')->middleware('permission:menu-read');
        Route::delete('/menu/{menu}', 'destroy')->name('destroy')->middleware('permission:menu-delete');
        // Sub Menu Routes
        Route::get('/menu/{menu}', 'subMenu')->name('subMenu')->middleware('permission:menu-read');
        Route::get('/menu-data/{menu}', 'dataSubmenu')->name('subMenu-data')->middleware('permission:menu-read');
    });
    // end menu routes


    // merchant routes
    Route::controller(MerchantController::class)->name('merchant_list.')->group(function () {
        Route::get('/merchant_list', 'index')->name('index')->middleware('permission:merchant-read');
        Route::post('/merchant_list', 'store')->name('store')->middleware('permission:merchant-create');
        Route::get('/merchant_list-data', 'data')->name('data')->middleware('permission:merchant-read');
        Route::put('/merchant_list/{merchant}', 'update')->name('update')->middleware('permission:merchant-update');
        Route::delete('/merchant_list/{merchant}', 'destroy')->name('destroy')->middleware('permission:merchant-delete');
    });

    // Santri Routes
    Route::controller(SantriController::class)->name('santri.')->group(function () {
        Route::get('/santri', 'index')->name('index')->middleware('permission:santri-read');
        Route::post('/santri', 'store')->name('store')->middleware('permission:santri-create');
        Route::get('/santri-data', 'data')->name('data')->middleware('permission:santri-read');
        Route::put('/santri/{santri}', 'update')->name('update')->middleware('permission:santri-update');
        Route::delete('/santri/{santri}', 'destroy')->name('destroy')->middleware('permission:santri-delete');
    });
    // End Santri Routes
    
    // Orang Tua Routes
    Route::controller(ParentController::class)->name('orang_tua.')->group(function () {
        Route::get('/orang_tua', 'index')->name('index')->middleware('permission:santri-read');
        Route::post('/orang_tua', 'store')->name('store')->middleware('permission:santri-create');
        Route::get('/orang_tua-data', 'data')->name('data')->middleware('permission:santri-read');
        Route::put('/orang_tua/{orang_tua}', 'update')->name('update')->middleware('permission:santri-update');
        Route::delete('/orang_tua/{orang_tua}', 'destroy')->name('destroy')->middleware('permission:santri-delete');
    });
    // End Orang Tua Routes

    // Payment Routes
    Route::controller(PaymentController::class)->name('payment.')->group(function () {
        Route::get('/payment', 'index')->name('index')->middleware('permission:payment-read');
        Route::post('/payment', 'store')->name('store')->middleware('permission:payment-create');
        Route::get('/payment-data', 'data')->name('data')->middleware('permission:payment-read');
        Route::put('/payment/{payment}', 'update')->name('update')->middleware('permission:payment-update');
        Route::delete('/payment/{payment}', 'destroy')->name('destroy')->middleware('permission:payment-delete');
    });
    // End Payment Routes
});
