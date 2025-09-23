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

Route::get('/hello', function () {
    return 'hello world';
});