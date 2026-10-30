<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    // Mostrar proveedores con búsqueda y paginación.
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));

        $suppliers = Supplier::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'search'));
    }

    // Mostrar formulario para registrar un proveedor.
    public function create()
    {
        $supplier = new Supplier();

    return view('suppliers.form', compact('supplier'));
    }

    // Guardar un nuevo proveedor.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    // Mostrar información de un proveedor.
    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    // Mostrar formulario para editar un proveedor.
    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', compact('supplier'));
    }

    // Actualizar los datos del proveedor.
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    // Eliminar un proveedor.
    public function destroy(Supplier $supplier)
    {
        // Evitar eliminar proveedores que tienen compras registradas.
        if ($supplier->purchases()->exists()) {
            return redirect()->route('suppliers.index')
                ->with('error', 'No se puede eliminar el proveedor porque tiene compras registradas.');
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
