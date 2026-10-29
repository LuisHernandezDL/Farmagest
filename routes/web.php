<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

// Mostrar el formulario e iniciar sesión.
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Cerrar sesión del usuario autenticado.
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Rutas disponibles únicamente para el administrador.
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/', function () {
        // Los indicadores provienen de la base de datos y reflejan las existencias actuales.
        $totalProducts = Product::count();
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();

        return view('dashboard.index', compact('totalProducts', 'lowStockProducts'));
    })->name('dashboard');

    Route::resource('products', ProductController::class)->except(['show', 'index']);
});

// El administrador y el cajero pueden consultar los productos.
Route::middleware(['auth', 'role:admin,cashier'])->group(function () {
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
});
