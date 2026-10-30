@extends('layouts.app')

@section('title', 'Detalle de compra | FarmaGest')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Detalle de la compra #{{ $purchase->id }}</h1>
            <p class="text-muted mb-0">
                Consulta los medicamentos y la información de esta compra.
            </p>
        </div>

        <a href="{{ route('purchases.index') }}"
           class="btn btn-outline-secondary">
            Volver a compras
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Información general</h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <strong>Proveedor</strong>
                    <p>{{ $purchase->supplier->name ?? 'Sin proveedor' }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Estado</strong>
                    <p>
                        @if($purchase->status === 'confirmed')
                            <span class="badge bg-success">Confirmada</span>
                        @else
                            <span class="badge bg-warning text-dark">Borrador</span>
                        @endif
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Fecha de compra</strong>
                    <p>
                        {{ $purchase->purchased_at
                            ? $purchase->purchased_at->format('d/m/Y H:i')
                            : 'Pendiente' }}
                    </p>
                </div>

                <div class="col-md-6">
                    <strong>Fecha de confirmación</strong>
                    <p>
                        {{ $purchase->confirmed_at
                            ? $purchase->confirmed_at->format('d/m/Y H:i')
                            : 'Sin confirmar' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Medicamentos comprados</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Medicamento</th>
                            <th>Cantidad</th>
                            <th>Precio unitario</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Producto no disponible' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>Q {{ number_format($item->unit_price, 2) }}</td>
                                <td>Q {{ number_format($item->line_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Esta compra no tiene medicamentos registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th colspan="3" class="text-end">Total de la compra:</th>
                            <th>Q {{ number_format($purchase->total, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="d-flex gap-2 mt-4">
                @if($purchase->status === 'draft')
                    <a href="{{ route('purchases.edit', $purchase) }}"
                       class="btn btn-primary">
                        Editar compra
                    </a>
                @endif

                <a href="{{ route('purchases.index') }}"
                   class="btn btn-outline-secondary">
                    Regresar
                </a>
            </div>
        </div>
    </div>
@endsection