
@extends('layouts.app')

@php($editing = $supplier->exists)

@section('title', 'Formulario de proveedores | FarmaGest')

@section('content')
    <h1 class="h3 mb-3">
        {{ $editing ? 'Editar proveedor' : 'Registrar proveedor' }}
    </h1>

    <div class="card shadow-sm">
        <div class="card-body">

            {{-- Mostrar errores de validación --}}
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

            <form method="POST"
                  action="{{ $editing ? route('suppliers.update', $supplier) : route('suppliers.store') }}">

                @csrf

                @if($editing)
                    @method('PUT')
                @endif

                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Nombre del proveedor *
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="{{ old('name', $supplier->name) }}"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">
                            Teléfono
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $supplier->phone) }}"
                            maxlength="30"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            Correo electrónico
                        </label>
                        <input
                            type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            value="{{ old('email', $supplier->email) }}"
                            maxlength="255"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label">
                            Dirección
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="address"
                            name="address"
                            value="{{ old('address', $supplier->address) }}"
                            maxlength="500"
                        >
                    </div>

                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ $editing ? 'Guardar cambios' : 'Registrar proveedor' }}
                    </button>

                    <a href="{{ route('suppliers.index') }}"
                       class="btn btn-outline-secondary">
                        Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>
@endsection
