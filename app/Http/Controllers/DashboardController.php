<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Purchase;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Obtiene el total de productos registrados.
        $totalProducts = Product::count();

        // Cuenta los productos cuya existencia es igual o inferior al stock mínimo.
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')->count();

        // Cuenta las ventas confirmadas durante el día actual.
        $todaySales = Sale::where('status', 'confirmed')
            ->whereDate('confirmed_at', today())
            ->count();

        // Calcula el total vendido en las ventas confirmadas del día.
        $totalSoldToday = Sale::where('status', 'confirmed')
            ->whereDate('confirmed_at', today())
            ->sum('total');

        // Cuenta las compras confirmadas durante el día actual.
        $todayPurchases = Purchase::where('status', 'confirmed')
            ->whereDate('confirmed_at', today())
            ->count();

        // Calcula el total de las compras confirmadas del día.
        $totalPurchasedToday = Purchase::where('status', 'confirmed')
            ->whereDate('confirmed_at', today())
            ->sum('total');

        // Envía los indicadores a la vista principal del dashboard.
        return view('dashboard.index', compact(
            'totalProducts',
            'lowStockProducts',
            'todaySales',
            'totalSoldToday',
            'todayPurchases',
            'totalPurchasedToday'
        ));
    }
}