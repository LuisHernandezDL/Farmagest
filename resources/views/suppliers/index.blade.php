
@extends('layouts.app')

@section('title', 'Proveedores | FarmaGest')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Proveedores</h1>
            <p class="text-muted mb-0">
                Administra los proveedores de medicamentos de la farmacia.
            </p>
        </div>

        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
            + Nuevo proveedor
        </a>
    </div>



    <div class="card shadow-sm">
        <div class="card-body">

            {{-- Búsqueda de proveedores --}}
            <form method="GET" action="{{ route('suppliers.index') }}" class="row g-2 mb-4">
                <div class="col-md-9">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Buscar por nombre, correo o teléfono"
                           value="{{ $search }}">
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        Buscar
                    </button>

                    <a href="{{ route('suppliers.index') }}"
                       class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>
            </form>

            {{-- Tabla de proveedores --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Teléfono</th>
                            <th>Correo electrónico</th>
                            <th>Dirección</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->id }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone ?? 'No registrado' }}</td>
                                <td>{{ $supplier->email ?? 'No registrado' }}</td>
                                <td>{{ $supplier->address ?? 'No registrada' }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('suppliers.show', $supplier) }}"
                                           class="btn btn-sm btn-outline-info">
                                            Ver
                                        </a>

                                        <a href="{{ route('suppliers.edit', $supplier) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Editar
                                        </a>

                                        
<form method="POST"
      action="{{ route('suppliers.destroy', $supplier) }}"
      id="delete-supplier-{{ $supplier->id }}">
    @csrf
    @method('DELETE')

    <button type="button"
            class="btn btn-sm btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#confirmDeleteModal"
            data-delete-form="delete-supplier-{{ $supplier->id }}"
            data-supplier-name="{{ $supplier->name }}">
        Eliminar
    </button>
</form>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No hay proveedores registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginación --}}
            <div class="mt-3">
                {{ $suppliers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

<!-- Ventana de confirmación para eliminar proveedores -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1"
     aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">
                    ¿Eliminar proveedor?
                </h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                ¿Estás segura de eliminar al proveedor
                <strong id="supplierNameToDelete"></strong>?
                <p class="text-danger mt-2 mb-0">
                    Esta acción no se puede deshacer.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancelar
                </button>

                <button type="button"
                        class="btn btn-danger"
                        id="confirmDeleteButton">
                    Sí, eliminar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('confirmDeleteModal');
    const confirmButton = document.getElementById('confirmDeleteButton');
    const supplierName = document.getElementById('supplierNameToDelete');

    let formToDelete = null;

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        formToDelete = document.getElementById(
            button.getAttribute('data-delete-form')
        );

        supplierName.textContent =
            button.getAttribute('data-supplier-name');
    });

    confirmButton.addEventListener('click', function () {
        if (formToDelete) {
            confirmButton.disabled = true;
            formToDelete.submit();
        }
    });
});
</script>
@endpush

@endsection
