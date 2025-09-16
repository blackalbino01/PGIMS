<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StockRequisitionController;
use App\Http\Controllers\StockRequisitionItemController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AuthController;



// Public routes for registration and login
Route::post(uri: 'register', action: [AuthController::class, 'register']);
Route::post(uri: 'login', action: [AuthController::class, 'login']);

// Group routes protected by auth:sanctum middleware for API authentication
Route::middleware(middleware: 'auth:sanctum')->group(callback: function(): void {
    Route::apiResource(name: 'users', controller: UserController::class);
    Route::apiResource(name: 'customers', controller: CustomerController::class);
    Route::apiResource(name: 'products', controller: ProductController::class);
    Route::apiResource(name: 'inventory', controller: InventoryController::class);
    Route::apiResource(name: 'orders', controller: OrderController::class);
    Route::apiResource(name: 'order-items', controller: OrderItemController::class);
    Route::apiResource(name: 'purchase-orders', controller: PurchaseOrderController::class);
    Route::apiResource(name: 'stock-requisitions', controller: StockRequisitionController::class);
    Route::apiResource(name: 'stock-requisition-items', controller: StockRequisitionItemController::class);
    Route::apiResource(name: 'stores', controller: StoreController::class);
    Route::apiResource(name: 'bank-accounts', controller: BankAccountController::class);
    Route::apiResource(name: 'transactions', controller: TransactionsController::class);
    Route::apiResource(name: 'notifications', controller: NotificationController::class);
    Route::apiResource(name: 'suppliers', controller: SupplierController::class);

    Route::post(uri: 'logout', action: [AuthController::class, 'logout']);
    Route::get(uri: 'user', action: function (Request $request) {
        return $request->user();
    });

    Route::post(uri: 'customers/{customer}/deposit', action: [CustomerController::class, 'deposit']);

});
