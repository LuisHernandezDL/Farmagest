<?php

use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

// Ventas: acceso exclusivo para usuarios autenticados con rol administrador o cajero.
Route::middleware(['auth', 'role:admin,cashier'])->group(function () {
    Route::resource('sales', SaleController::class)
        ->only(['index', 'create', 'store', 'show']);
});
