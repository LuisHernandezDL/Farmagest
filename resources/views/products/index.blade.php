@extends('layouts.app')
@section('title', 'Productos | FarmaGest')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 gap-2">
    <div>
        <h1 class="h3 mb-1">Inventario de productos</h1>
        <p class="text-muted mb-0">Registro inicial de medicamentos y existencias.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-primary">+ Nuevo producto</a>
</div>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Código</th><th>Producto</th><th>Categoría</th><th>Precio venta</th><th>Stock</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->code }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category->name }}</td>
                        <td>Q {{ number_format((float) $product->sale_price, 2) }}</td>
                        <td>
                            {{ $product->stock }}
                            @if($product->stock <= $product->min_stock)
                                <span class="badge text-bg-warning ms-1">Stock bajo</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a class="btn btn-sm btn-outline-primary" href="{{ route('products.edit', $product) }}">Editar</a>
                                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('¿Eliminar este producto?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Todavía no hay productos. Registra el primero.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
