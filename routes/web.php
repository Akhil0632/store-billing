<?php

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('orders.index'));

Route::resource('orders', OrderController::class)->only(['index', 'create', 'store', 'show']);
