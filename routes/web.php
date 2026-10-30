<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

// Muestra el dashboard como página principal del sistema.
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// Define las rutas para las operaciones del módulo de productos.
Route::resource('products', ProductController::class)->except(['show']);
