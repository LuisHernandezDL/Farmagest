
@extends('layouts.app')

@section('title', 'Detalle del proveedor | FarmaGest')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detalle del proveedor</h1>
            <p class="text-muted mb-0">
                Consulta la información del proveedor registrado.
            </p>
        </div>

        <a href="{{ route('suppliers.index') }}"
           class="btn btn-outline-secondary">
            Volver a proveedores
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                {{ $supplier->name }}
            </h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <strong>ID del proveedor</strong>
                    <p>{{ $supplier->id }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Nombre</strong>
                    <p>{{ $supplier->name }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Teléfono</strong>
                    <p>{{ $supplier->phone ?? 'No registrado' }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Correo electrónico</strong>
                    <p>{{ $supplier->email ?? 'No registrado' }}</p>
                </div>

                <div class="col-md-12">
                    <strong>Dirección</strong>
                    <p>{{ $supplier->address ?? 'No registrada' }}</p>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <a href="{{ route('suppliers.edit', $supplier) }}"
                   class="btn btn-primary">
                    Editar proveedor
                </a>

                <a href="{{ route('suppliers.index') }}"
                   class="btn btn-outline-secondary">
                    Regresar
                </a>
            </div>
        </div>
    </div>
@endsection
