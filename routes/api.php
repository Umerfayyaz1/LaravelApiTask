<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post("register", action: [AuthController::class,"register"]);
Route::post("login", action: [AuthController::class,"login"]);
Route::get("/", action: [AuthController::class,"welcome"]);

Route::group([
    "middleware"=>("auth:sanctum")
],function(){
    Route::get("profile", action: [AuthController::class,"profile"]);
    Route::get("logout", action: [AuthController::class,"logout"]);

    Route::get('/products', [ProductController::class, 'index']);       // List all products
    Route::get('/products/{id}', [ProductController::class, 'show']);   // View a product by ID

    Route::post('orders', [OrderController::class, 'placeOrder']); // Customer: Place a new order
    Route::get('orders', [OrderController::class, 'getUserOrders']); // Customer: Fetch user's orders

    // Product routes restricted to admins only
    Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);    // Add a product (admin only)
        Route::put('/products/{id}', [ProductController::class, 'update']); // Update a product (admin only)
        Route::delete('/products/{id}', [ProductController::class, 'destroy']); // Delete a product (admin only)

        Route::get('admin/orders', [OrderController::class, 'getAllOrders']); // Admin: Fetch all orders
        Route::put('admin/orders/{id}/mark-as-placed', [OrderController::class, 'markOrderAsPlaced']); // Admin: Mark order as placed
        Route::put('admin/orders/{id}/update-status', [OrderController::class, 'updateOrderStatus']); // Admin: Update order status
    });


});
