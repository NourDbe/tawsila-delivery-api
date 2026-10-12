<?php

declare(strict_types=1);

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\StoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Order Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/orders/current',
    [OrderController::class, 'current']
);

Route::get(
    '/orders/today',
    [OrderController::class, 'today']
);

Route::get(
    '/orders/unassigned-delayed',
    [OrderController::class, 'delayedUnassigned']
);

Route::get(
    '/drivers/{driver}/orders/current',
    [OrderController::class, 'currentForDriver']
);

Route::post(
    '/orders',
    [OrderController::class, 'store']
);

/*
|--------------------------------------------------------------------------
| Store Routes
|--------------------------------------------------------------------------
*/

Route::get(
    '/stores',
    [StoreController::class, 'index']
);

Route::get(
    '/admin/stores',
    [StoreController::class, 'adminIndex']
);
