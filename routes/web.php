<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Los indicadores provienen de la base de datos y reflejan las existencias actuales.
    $totalProducts = Product::count();
    $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();

    return view('dashboard.index', compact('totalProducts', 'lowStockProducts'));
})->name('dashboard');

Route::resource('products', ProductController::class)->except(['show']);
Route::resource('suppliers', SupplierController::class);