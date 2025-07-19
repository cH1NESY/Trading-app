<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\WarehouseController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\OrderController;

Route::get('/', function () {
    return view('welcome');
});

// Веб-маршруты
Route::prefix('web')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Склады
    Route::get('/warehouses', [WarehouseController::class, 'showList'])->name('warehouses.index');
    Route::get('/warehouses/{warehouse}/stocks', [WarehouseController::class, 'getStocks'])->name('warehouses.stocks');
    
    // Товары
    Route::get('/products', [ProductController::class, 'showList'])->name('products.index');
    
    // Заказы
    Route::get('/orders', [OrderController::class, 'showList'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'showCreate'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [OrderController::class, 'showOne'])->name('orders.show');
    Route::get('/orders/{order}/edit', [OrderController::class, 'showEdit'])->name('orders.edit');
    Route::put('/orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    
    // Действия с заказами
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/resume', [OrderController::class, 'resume'])->name('orders.resume');
});

// API маршруты (для AJAX запросов)
Route::prefix('api')->group(function () {
    Route::get('/warehouses', [WarehouseController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::post('/orders/{order}/complete', [OrderController::class, 'complete']);
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel']);
    Route::post('/orders/{order}/resume', [OrderController::class, 'resume']);
});
