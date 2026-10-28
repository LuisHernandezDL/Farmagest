<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Eliminación simple para la demostración inicial. Antes de añadir ventas,
        // cambiaremos esto por desactivación para preservar el historial.
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
            'stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ];
    }
}
