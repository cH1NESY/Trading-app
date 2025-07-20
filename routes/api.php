<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StockMovementController;

Route::get('/warehouses', [WarehouseController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::put('/orders/{order}', [OrderController::class, 'update']);
Route::patch('/orders/{order}/complete', [OrderController::class, 'complete']);
Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel']);
Route::patch('/orders/{order}/resume', [OrderController::class, 'resume']);
Route::get('/stock-movements', [StockMovementController::class, 'index']);
