<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\StockRequisitionController;
use App\Http\Controllers\StockRequisitionItemController;

Route::apiResource(name: 'stores', controller: StoreController::class);
Route::apiResource(name: 'inventory', controller: InventoryController::class);
Route::apiResource(name: 'stock-requisitions', controller: StockRequisitionController::class);
Route::apiResource(name: 'stock-requisition-items', controller: StockRequisitionItemController::class);