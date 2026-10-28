@extends('layouts.app')
@php($editing = $product->exists)
@section('title', 'Formulario de producto | FarmaGest')
@section('content')
<h1 class="h3 mb-3">{{ $editing ? 'Editar producto' : 'Registrar producto' }}</h1>
<div class="card shadow-sm"><div class="card-body">
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa estos datos:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ $editing ? route('products.update', $product) : route('products.store') }}">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="code">Código</label>
                <input class="form-control" id="code" name="code" value="{{ old('code', $product->code) }}" maxlength="50" required>
            </div>
            <div class="col-md-8">
                <label class="form-label" for="name">Nombre del medicamento</label>
                <input class="form-control" id="name" name="name" value="{{ old('name', $product->name) }}" maxlength="180" required>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="category_id">Categoría</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <option value="">Selecciona una categoría</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="purchase_price">Precio compra (Q)</label>
                <input type="number" class="form-control" id="purchase_price" name="purchase_price" min="0" step="0.01" value="{{ old('purchase_price', $product->purchase_price ?? '0.00') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="sale_price">Precio venta (Q)</label>
                <input type="number" class="form-control" id="sale_price" name="sale_price" min="0" step="0.01" value="{{ old('sale_price', $product->sale_price ?? '0.00') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="stock">Existencias iniciales</label>
                <input type="number" class="form-control" id="stock" name="stock" min="0" step="1" value="{{ old('stock', $product->stock ?? 0) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label" for="min_stock">Stock mínimo</label>
                <input type="number" class="form-control" id="min_stock" name="min_stock" min="0" step="1" value="{{ old('min_stock', $product->min_stock ?? 5) }}" required>
            </div>
        </div>
        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-primary" type="submit">{{ $editing ? 'Guardar cambios' : 'Registrar producto' }}</button>
            <a class="btn btn-outline-secondary" href="{{ route('products.index') }}">Cancelar</a>
        </div>
    </form>
</div></div>
@endsection
