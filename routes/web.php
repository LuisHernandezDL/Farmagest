<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Los indicadores provienen de la base de datos y reflejan las existencias actuales.
    $totalProducts = Product::count();
    $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();

    return view('dashboard.index', compact('totalProducts', 'lowStockProducts'));
})->name('dashboard');

Route::resource('products', ProductController::class)->except(['show']);

// Rutas para administrar las compras.
Route::resource('purchases', PurchaseController::class);

// Ruta para confirmar una compra y actualizar el inventario.
Route::post('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])
    ->name('purchases.confirm');
