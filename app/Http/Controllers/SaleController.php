<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::query()
            ->where('status', 'confirmed')
            ->with(['user'])
            ->withCount('items')
            ->latest('id')
            ->paginate(12);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $products = Product::query()
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'sale_price', 'stock']);

        return view('sales.create', compact('products'));
    }

    public function store(Request $request, InventoryService $inventoryService): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);

        $ids = array_column($data['items'], 'product_id');
        if (count($ids) !== count(array_unique($ids))) {
            throw ValidationException::withMessages([
                'items' => 'Cada medicamento debe aparecer una sola vez en la venta.',
            ]);
        }

        // La transacción abarca la venta, los detalles y los descuentos de inventario.
        $sale = DB::transaction(function () use ($data, $inventoryService) {
            $sale = Sale::create([
                // Guardar el usuario autenticado que realizo la venta.
                'user_id' => Auth::id(),
                'status' => 'draft',
                'total' => '0.00',
            ]);

            $totalCents = 0;

            foreach ($data['items'] as $row) {
                $product = Product::findOrFail($row['product_id']);
                $quantity = (int) $row['quantity'];

                // Convertimos a centavos para conservar importes exactos con dos decimales.
                $unitPriceCents = (int) round(((float) $product->sale_price) * 100);
                $lineCents = $unitPriceCents * $quantity;
                $totalCents += $lineCents;

                if ($lineCents > 999999999999 || $totalCents > 999999999999) {
                    throw ValidationException::withMessages([
                        'items' => 'El importe supera el máximo permitido por el sistema.',
                    ]);
                }

                $detail = $sale->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => number_format($unitPriceCents / 100, 2, '.', ''),
                    'line_total' => number_format($lineCents / 100, 2, '.', ''),
                ]);

                $inventoryService->decrease($product->id, $quantity, 'sale_item', $detail->id);
            }

            $sale->update([
                'total' => number_format($totalCents / 100, 2, '.', ''),
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            return $sale;
        }, 3);

        return redirect()
            ->route('sales.show', $sale)
            ->with('success', 'Venta registrada correctamente.');
    }

    public function show(Sale $sale): View
    {
        abort_unless($sale->status === 'confirmed', 404);

        $sale->load(['items.product', 'user']);

        return view('sales.show', compact('sale'));
    }
}
