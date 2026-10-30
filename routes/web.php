
<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $totalProducts = Product::count();
    $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();

    return view('dashboard.index', compact('totalProducts', 'lowStockProducts'));
})->name('dashboard');

Route::resource('products', ProductController::class)->except(['show']);

Route::resource('suppliers', SupplierController::class);

Route::resource('purchases', PurchaseController::class);

Route::post('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])
    ->name('purchases.confirm');

