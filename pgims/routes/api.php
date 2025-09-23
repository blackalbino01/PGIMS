<?php

use Illuminate\Http\Request;
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
use App\Http\Controllers\ReportingController;


// Public routes for registration and login (no auth needed)
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

// Routes for authenticated users only
Route::middleware('auth:sanctum')->group(function () {

    // Logout and current user info accessible to all authenticated users
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', function (Request $request) {
        return $request->user();
    });

    /*
     * Admin only routes
     * Full control on users and suppliers management
     */
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('suppliers', SupplierController::class);
    });

    /*
     * Admin and Manager routes
     * Manage products, inventory, orders, stock requisitions, and related resources
     */
    Route::middleware('role:admin,manager')->group(function () {
        Route::apiResource('products', ProductController::class)->except('show');
        Route::apiResource('inventory', InventoryController::class);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('order-items', OrderItemController::class);
        Route::apiResource('purchase-orders', PurchaseOrderController::class);
        Route::apiResource('stock-requisitions', StockRequisitionController::class);
        Route::apiResource('stock-requisition-items', StockRequisitionItemController::class);
        Route::apiResource('stores', StoreController::class);

        // Reporting accessible to Admin and Manager roles
        Route::get('reports/daily-sales', [ReportingController::class, 'dailySalesSummary']);
        Route::get('reports/payment-breakdown', [ReportingController::class, 'paymentBreakdown']);
        Route::get('reports/profit', [ReportingController::class, 'profitReport']);
        Route::get('reports/inventory-status', [ReportingController::class, 'inventoryStatus']);
        Route::get('reports/customer-credit', [ReportingController::class, 'customerCreditReport']);
        Route::get('reports/expense', [ReportingController::class, 'expenseReport']);
    });

    /*
     * Admin, Manager, and Cashier routes
     * Manage customers, transactions, notifications
     */
    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::apiResource('customers', CustomerController::class);
        Route::apiResource('transactions', TransactionsController::class);
        Route::apiResource('notifications', NotificationController::class);
        Route::post('customers/{customer}/deposit', [CustomerController::class, 'deposit']);
    });

    /*
     * Bank accounts possibly editable by admin and finance roles
     * Adjust roles accordingly based on your org needs
     */
    Route::middleware('role:admin,finance')->group(function () {
        Route::apiResource('bank-accounts', BankAccountController::class);
    });
});