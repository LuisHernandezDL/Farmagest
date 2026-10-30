@extends('layouts.app')

@section('title', 'Compras | FarmaGest')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Compras</h1>
            <p class="text-muted mb-0">
                Administra las compras de medicamentos de la farmacia.
            </p>
        </div>

        <a href="{{ route('purchases.create') }}" class="btn btn-primary">
            + Nueva compra
        </a>
    </div>


    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Cerrar"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Proveedor</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchases as $purchase)
                            <tr>
                                <td>{{ $purchase->id }}</td>
                                <td>{{ $purchase->supplier->name ?? 'Sin proveedor' }}</td>
                                <td>
                                    {{ $purchase->purchased_at
                                        ? $purchase->purchased_at->format('d/m/Y')
                                        : 'Pendiente' }}
                                </td>
                                <td>Q {{ number_format($purchase->total, 2) }}</td>
                                <td>
                                    @if($purchase->status === 'confirmed')
                                        <span class="badge bg-success">Confirmada</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Borrador</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('purchases.show', $purchase) }}"
                                           class="btn btn-sm btn-outline-info">
                                            Ver
                                        </a>

                                        @if($purchase->status === 'draft')
                                            <a href="{{ route('purchases.edit', $purchase) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                Editar
                                            </a>

                                            <form action="{{ route('purchases.confirm', $purchase) }}"
                                                  method="POST">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm btn-success">
                                                    Confirmar
                                                </button>
                                            </form>

<form id="delete-purchase-{{ $purchase->id }}"
      action="{{ route('purchases.destroy', $purchase) }}"
      method="POST">
    @csrf
    @method('DELETE')

    <button type="button"
            class="btn btn-sm btn-outline-danger"
            data-bs-toggle="modal"
            data-bs-target="#deletePurchaseModal"
            data-form-id="delete-purchase-{{ $purchase->id }}">
        Eliminar
    </button>
</form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No hay compras registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>

<!-- Modal para confirmar la eliminación de una compra -->
<div class="modal fade" id="deletePurchaseModal" tabindex="-1"
     aria-labelledby="deletePurchaseModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="deletePurchaseModalLabel">
                    Confirmar eliminación
                </h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <p class="mb-0">
                    ¿Estás segura de que deseas eliminar esta compra en borrador?
                </p>
                <p class="text-muted small mt-2 mb-0">
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
                        id="confirmDeletePurchase">
                    Sí, eliminar compra
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('deletePurchaseModal');
    const confirmButton = document.getElementById('confirmDeletePurchase');
    let formToDelete = null;

    modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const formId = button.getAttribute('data-form-id');

        formToDelete = document.getElementById(formId);
    });

    confirmButton.addEventListener('click', function () {
        if (formToDelete) {
            confirmButton.disabled = true;
            formToDelete.submit();
        }
    });
});
</script>
   
@endsection

