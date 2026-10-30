<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

// Inicio de sesión
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Acceso exclusivo del administrador
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class)
        ->except(['show', 'index']);

    Route::resource('suppliers', SupplierController::class);

    Route::resource('purchases', PurchaseController::class);

    Route::post('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])
        ->name('purchases.confirm');

    Route::get('/inventory', [InventoryController::class, 'index'])
        ->name('inventory.index');

    Route::get('/inventory/movements', [InventoryController::class, 'movements'])
        ->name('inventory.movements');
});

// Consulta de productos para administrador y cajero
Route::middleware(['auth', 'role:admin,cashier'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');
});

// Cargar las rutas protegidas del modulo de ventas.
require __DIR__ . '/sales.php';
