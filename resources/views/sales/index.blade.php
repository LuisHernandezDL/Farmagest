@extends('layouts.app')
@section('title', 'Ventas | FarmaGest')
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <div class="app-eyebrow">OPERACIONES</div>
        <h1 class="app-page-title mb-1">Ventas</h1>
        <p class="text-muted mb-0">Historial de ventas confirmadas.</p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-success">
        <i class="bi bi-plus-lg me-1" aria-hidden="true"></i> Nueva venta
    </a>
</div>

<div class="card shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Venta</th>
                    <th>Fecha</th>
                    <th>Registrada por</th>
                    <th>Productos</th>
                    <th class="text-end">Total</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="fw-semibold">#{{ $sale->id }}</td>
                        <td>{{ ($sale->confirmed_at ?? $sale->created_at)->format('d/m/Y H:i') }}</td>
                        <td>{{ $sale->user?->name ?? 'Sin usuario asignado' }}</td>
                        <td>{{ $sale->items_count }}</td>
                        <td class="text-end fw-semibold">Q {{ number_format((float) $sale->total, 2) }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-success" href="{{ route('sales.show', $sale) }}">Ver comprobante</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Todavía no hay ventas registradas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $sales->links('pagination::bootstrap-5') }}</div>
@endsection
