@extends('layouts.app')
@section('title', 'Comprobante de venta | FarmaGest')
@section('content')
<style>
    @media print {
        .app-sidebar, .app-topbar, .app-environment-badge, .sale-no-print { display: none !important; }
        .app-main { width: 100% !important; }
        .app-content { padding: 0 !important; }
        body.app-body { background: white; }
        .card { border: 0 !important; box-shadow: none !important; }
    }
</style>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 sale-no-print">
    <div>
        <div class="app-eyebrow">VENTA CONFIRMADA</div>
        <h1 class="app-page-title mb-0">Comprobante #{{ $sale->id }}</h1>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('sales.index') }}">Volver al historial</a>
        <button class="btn btn-success" type="button" onclick="window.print()">
            <i class="bi bi-printer me-1" aria-hidden="true"></i> Imprimir
        </button>
    </div>
</div>
<div class="card shadow-sm">
    <div class="card-body p-4 p-md-5">
        <div class="d-flex flex-wrap justify-content-between gap-3 border-bottom pb-3 mb-4">
            <div>
                <h2 class="h4 fw-bold mb-1">FarmaGest</h2>
                <p class="text-muted mb-0">Constancia interna de venta</p>
                <small class="text-muted">No es una factura fiscal.</small>
            </div>
            <div class="text-md-end">
                <div class="fw-semibold">Venta #{{ $sale->id }}</div>
                <div>{{ ($sale->confirmed_at ?? $sale->created_at)->format('d/m/Y H:i') }}</div>
                <div class="text-muted small">Atendió: {{ $sale->user?->name ?? 'No asignado' }}</div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Medicamento</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Precio</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->items as $item)
                        <tr>
                            <td>{{ $item->product?->name ?? 'Producto no disponible' }}</td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Q {{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="text-end">Q {{ number_format((float) $item->line_total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end fs-5">TOTAL</th>
                        <th class="text-end fs-5">Q {{ number_format((float) $sale->total, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <p class="text-center text-muted small mt-4 mb-0">Gracias por su compra.</p>
    </div>
</div>
@endsection
