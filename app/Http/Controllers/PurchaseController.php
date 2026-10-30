<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = Purchase::with('supplier')
        ->orderByDesc('id')
        ->paginate(10);

    return view('purchases.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener los proveedores ordenados alfabéticamente.
    $suppliers = Supplier::orderBy('name')->get();

    // Obtener los medicamentos ordenados alfabéticamente.
    $products = Product::orderBy('name')->get();

    // Enviar la información al formulario de compras.
    return view('purchases.form', [
        'purchase' => new Purchase(),
        'suppliers' => $suppliers,
        'products' => $products,
    ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validar los datos de la compra.
    $validated = $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|distinct|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
    ]);

    // Guardar la compra y sus detalles en una transacción.
    DB::transaction(function () use ($validated) {

        $purchase = Purchase::create([
            'supplier_id' => $validated['supplier_id'],
            'status' => 'draft',
            'total' => 0,
        ]);

        $total = 0;

        foreach ($validated['items'] as $item) {

            $lineTotal = round(
                $item['quantity'] * $item['unit_price'],
                2
            );

            PurchaseItem::create([
                'purchase_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $lineTotal,
            ]);

            $total += $lineTotal;
        }

        // Actualizar el total de la compra.
        $purchase->update([
            'total' => round($total, 2),
        ]);
    });

    return redirect()
        ->route('purchases.index')
        ->with('success', 'Compra registrada correctamente como borrador.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
{
    // Obtener la compra con su proveedor y medicamentos.
    $purchase->load([
        'supplier',
        'items.product',
    ]);

    // Enviar la información a la vista de detalles.
    return view('purchases.show', compact('purchase'));
}

    /**
     * Show the form for editing the specified resource.
     */
public function edit(Purchase $purchase)
{
    // Evitar editar compras que ya fueron confirmadas.
    if ($purchase->status !== 'draft') {
        return redirect()
            ->route('purchases.index')
            ->with('error', 'No se puede editar una compra confirmada.');
    }

    // Obtener los medicamentos registrados en la compra.
    $purchase->load('items');

    // Obtener proveedores y medicamentos para el formulario.
    $suppliers = Supplier::orderBy('name')->get();
    $products = Product::orderBy('name')->get();

    return view('purchases.form', compact(
        'purchase',
        'suppliers',
        'products'
    ));
}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Purchase $purchase)
{
    // No permitir modificaciones de compras confirmadas.
    if ($purchase->status !== 'draft') {
        return redirect()
            ->route('purchases.index')
            ->with('error', 'No se puede modificar una compra confirmada.');
    }

    // Validar los datos recibidos.
    $validated = $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|distinct|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.unit_price' => 'required|numeric|min:0',
    ]);

    // Actualizar la compra y sus detalles en una transacción.
    DB::transaction(function () use ($purchase, $validated) {

        // Bloquear y comprobar nuevamente el estado de la compra.
        $purchase = Purchase::whereKey($purchase->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($purchase->status !== 'draft') {
            throw ValidationException::withMessages([
                'purchase' => 'La compra ya fue confirmada y no puede modificarse.',
            ]);
        }

        // Eliminar los detalles anteriores.
        $purchase->items()->delete();

        $total = 0;

        // Registrar los nuevos detalles.
        foreach ($validated['items'] as $item) {

            $lineTotal = round(
                $item['quantity'] * $item['unit_price'],
                2
            );

            $purchase->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $lineTotal,
            ]);

            $total += $lineTotal;
        }

        // Actualizar proveedor y total.
        $purchase->update([
            'supplier_id' => $validated['supplier_id'],
            'total' => round($total, 2),
        ]);
    });

    return redirect()
        ->route('purchases.index')
        ->with('success', 'Compra actualizada correctamente.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
{
    DB::transaction(function () use ($purchase) {

        // Bloquear la compra para evitar cambios simultáneos.
        $purchase = Purchase::whereKey($purchase->id)
            ->lockForUpdate()
            ->firstOrFail();

        // Impedir eliminar compras confirmadas.
        if ($purchase->status !== 'draft') {
            throw ValidationException::withMessages([
                'purchase' => 'No se puede eliminar una compra confirmada.',
            ]);
        }

        // Eliminar la compra.
        // Sus detalles se eliminan automáticamente por cascadeOnDelete().
        $purchase->delete();
    });

    return redirect()
        ->route('purchases.index')
        ->with('success', 'Compra eliminada correctamente.');
}
// Confirmar una compra y actualizar el inventario.
public function confirm(Purchase $purchase, InventoryService $inventoryService)
{
    DB::transaction(function () use ($purchase, $inventoryService) {

        // Bloquear la compra para evitar confirmaciones simultáneas.
        $purchase = Purchase::whereKey($purchase->id)
            ->lockForUpdate()
            ->firstOrFail();

        // Evitar confirmar una compra más de una vez.
        if ($purchase->status !== 'draft') {
            throw ValidationException::withMessages([
                'purchase' => 'Esta compra ya fue confirmada.',
            ]);
        }

        // Obtener los medicamentos de la compra.
        $items = $purchase->items;

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'purchase' => 'No se puede confirmar una compra sin medicamentos.',
            ]);
        }

        // Aumentar las existencias de cada medicamento.
        foreach ($items as $item) {
            $inventoryService->increase(
                $item->product_id,
                $item->quantity,
                'purchase_item',
                $item->id
            );
        }

        // Marcar la compra como confirmada.
        $purchase->update([
            'status' => 'confirmed',
            'purchased_at' => now(),
            'confirmed_at' => now(),
        ]);
    });

    return redirect()
        ->route('purchases.index')
        ->with('success', 'Compra confirmada e inventario actualizado correctamente.');
}
}
