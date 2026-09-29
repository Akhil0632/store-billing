<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('products')->group(function () {
    Route::get('/low-stock', [ProductController::class, 'lowStock']);
});

Route::prefix('orders')->group(function () {
    Route::post('/',       [OrderController::class, 'store']);
    Route::get('/history', [OrderController::class, 'history']);
    Route::get('/{order}', [OrderController::class, 'show']);
});

