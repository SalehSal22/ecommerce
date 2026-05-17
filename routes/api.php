
<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::post('/register/verify-otp', [AuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('throttle:20,1');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('throttle:30,1');
    Route::get('/profile', [AuthController::class, 'profile'])->middleware('auth:api');
});

Route::prefix('admin/auth')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->middleware('auth:admin');
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index']);
    Route::get('/{id}', [ProductsController::class, 'show']);
    Route::post('/', [ProductsController::class, 'store'])->middleware('auth:admin');
    Route::put('/{id}', [ProductsController::class, 'update'])->middleware('auth:admin');
    Route::delete('/{id}', [ProductsController::class, 'destroy'])->middleware('auth:admin');
});

Route::middleware('auth:api')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);

    Route::post('/orders', [OrdersController::class, 'store']);
    Route::get('/orders', [OrdersController::class, 'index']);
    Route::get('/orders/{id}', [OrdersController::class, 'show']);
});

Route::get('/inventory', [InventoryController::class, 'index'])->middleware('auth:admin');
Route::put('/inventory/{id}', [InventoryController::class, 'update'])->middleware('auth:admin');

Route::get('/reports/daily-sales', [ReportsController::class, 'dailySales'])->middleware('auth:admin');
Route::post('/reports/daily-sales', [ReportsController::class, 'generateDailySales'])->middleware('auth:admin');
