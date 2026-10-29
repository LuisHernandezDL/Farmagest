<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with('category')->latest('id')->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.form', [
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules());
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Producto registrado correctamente.');
    }

    public function edit(Product $product): View
    {
        return view('products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate($this->rules($product));
        // Las existencias solo cambian mediante compras y ventas, nunca desde este formulario.
        unset($data['stock']);
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // No se elimina un medicamento que ya tenga historial comercial o de inventario.
        $hasHistory = DB::table('purchase_items')->where('product_id', $product->id)->exists()
            || DB::table('sale_items')->where('product_id', $product->id)->exists()
            || DB::table('stock_movements')->where('product_id', $product->id)->exists();

        if ($hasHistory) {
            return redirect()->route('products.index')->with(
                'error',
                'No puedes eliminar un producto con compras, ventas o movimientos registrados.'
            );
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado correctamente.');
    }

    private function rules(?Product $product = null): array
    {
        $uniqueCode = Rule::unique('products', 'code');
        if ($product && $product->exists) {
            $uniqueCode->ignore($product->id);
        }

        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'code' => ['required', 'string', 'max:50', $uniqueCode],
            'name' => ['required', 'string', 'max:180'],
            'purchase_price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'sale_price' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'stock' => ($product && $product->exists)
                ? ['required', 'integer', Rule::in([$product->stock])]
                : ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ];
    }
}
