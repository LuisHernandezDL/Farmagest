@extends('layouts.app')

@section('title', 'Movimientos de inventario | FarmaGest')

@section('content')
@php
    $hasFilters = $filters['search'] !== '' || $filters['direction'] !== '' || $filters['from'] || $filters['to'];
    $clearUrl = $selectedProduct
        ? route('inventory.movements', ['product' => $selectedProduct->id])
        : route('inventory.movements');
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <div>
        <div class="app-eyebrow">OPERACIONES</div>
        <h1 class="app-page-title mb-2">Movimientos de inventario</h1>
        <p class="text-secondary mb-0">Historial de entradas por compras y salidas por ventas.</p>
    </div>
</div>

<ul class="nav nav-pills gap-2 mb-4" style="--bs-nav-pills-link-active-bg: var(--fg-primary);">
    <li class="nav-item">
        <a class="nav-link text-secondary" href="{{ route('inventory.index') }}">
            <i class="bi bi-box-seam me-1" aria-hidden="true"></i> Existencias
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="{{ route('inventory.movements') }}">
            <i class="bi bi-clock-history me-1" aria-hidden="true"></i> Movimientos
        </a>
    </li>
</ul>

@if ($selectedProduct)
    <div class="alert alert-light border d-flex flex-wrap align-items-center gap-2">
        <i class="bi bi-capsule text-success" aria-hidden="true"></i>
        <span>
            Mostrando el historial de <strong>{{ $selectedProduct->code }} · {{ $selectedProduct->name }}</strong>
            <span class="text-secondary">(existencia actual: {{ number_format($selectedProduct->stock) }})</span>
        </span>
        <a href="{{ route('inventory.movements') }}" class="ms-md-auto small">Ver todos los productos</a>
    </div>
@endif

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="app-metric-label">Movimientos</span>
                    <span class="app-metric-icon bg-primary-subtle text-primary"><i class="bi bi-arrow-left-right" aria-hidden="true"></i></span>
                </div>
                <div class="app-metric-value">{{ number_format($totals['movements']) }}</div>
                <div class="text-secondary small">Registros en los resultados</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="app-metric-label">Entradas</span>
                    <span class="app-metric-icon bg-success-subtle text-success"><i class="bi bi-box-arrow-in-down" aria-hidden="true"></i></span>
                </div>
                <div class="app-metric-value text-success">+{{ number_format($totals['in']) }}</div>
                <div class="text-secondary small">Unidades ingresadas</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="app-metric-label">Salidas</span>
                    <span class="app-metric-icon bg-danger-subtle text-danger"><i class="bi bi-box-arrow-up" aria-hidden="true"></i></span>
                </div>
                <div class="app-metric-value text-danger">−{{ number_format($totals['out']) }}</div>
                <div class="text-secondary small">Unidades despachadas</div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.movements') }}" class="row g-2 align-items-end" role="search">
            @if ($selectedProduct)
                <input type="hidden" name="product" value="{{ $selectedProduct->id }}">
            @endif
            <div class="col-12 col-lg-3">
                <label for="search" class="form-label small text-secondary mb-1">Medicamento</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" id="search" name="search" class="form-control" maxlength="100"
                           value="{{ $filters['search'] }}" placeholder="Código o nombre">
                </div>
            </div>
            <div class="col-12 col-sm-4 col-lg-3">
                <label for="direction" class="form-label small text-secondary mb-1">Tipo</label>
                <select id="direction" name="direction" class="form-select">
                    <option value="">Todos los tipos</option>
                    <option value="in" @selected($filters['direction'] === 'in')>Solo entradas</option>
                    <option value="out" @selected($filters['direction'] === 'out')>Solo salidas</option>
                </select>
            </div>
            <div class="col-6 col-sm-4 col-lg-2">
                <label for="from" class="form-label small text-secondary mb-1">Desde</label>
                <input type="date" id="from" name="from" class="form-control" value="{{ $filters['from'] }}">
            </div>
            <div class="col-6 col-sm-4 col-lg-2">
                <label for="to" class="form-label small text-secondary mb-1">Hasta</label>
                <input type="date" id="to" name="to" class="form-control" value="{{ $filters['to'] }}">
            </div>
            <div class="col-12 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-grow-1">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i> Filtrar
                </button>
                @if ($hasFilters)
                    <a href="{{ $clearUrl }}" class="btn btn-outline-secondary" title="Limpiar filtros" aria-label="Limpiar filtros">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="d-none d-sm-table-cell">Fecha</th>
                    <th scope="col">Medicamento</th>
                    <th scope="col" class="d-none d-sm-table-cell">Tipo</th>
                    <th scope="col" class="text-end">Cantidad</th>
                    <th scope="col" class="text-end">Existencia<span class="d-none d-sm-inline"> resultante</span></th>
                    <th scope="col" class="d-none d-md-table-cell">Origen</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movements as $movement)
                    @php
                        $isEntry = $movement->direction === 'in';
                        $referenceKey = $movement->source_type . ':' . $movement->source_id;
                        $fallbackLabel = $movement->source_type === 'purchase_item' ? 'Compra' : 'Venta';
                        $reference = $references[$referenceKey] ?? $fallbackLabel . ' (detalle #' . $movement->source_id . ')';
                    @endphp
                    <tr>
                        <td class="text-nowrap d-none d-sm-table-cell">
                            {{ $movement->created_at?->format('d/m/Y') }}
                            <div class="small text-secondary">{{ $movement->created_at?->format('H:i') }}</div>
                        </td>
                        <td>
                            <span class="fw-semibold">{{ $movement->product?->code }}</span>
                            <div class="small">{{ $movement->product?->name }}</div>
                            <div class="small text-secondary d-md-none">{{ $reference }}</div>
                            <div class="small text-secondary d-sm-none">{{ $movement->created_at?->format('d/m/Y H:i') }}</div>
                        </td>
                        <td class="text-nowrap d-none d-sm-table-cell">
                            @if ($isEntry)
                                <span class="badge bg-success-subtle text-success-emphasis">
                                    <i class="bi bi-arrow-down-circle me-1" aria-hidden="true"></i>Entrada
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger-emphasis">
                                    <i class="bi bi-arrow-up-circle me-1" aria-hidden="true"></i>Salida
                                </span>
                            @endif
                        </td>
                        <td class="text-end fw-bold text-nowrap {{ $isEntry ? 'text-success' : 'text-danger' }}">
                            {{ $isEntry ? '+' : '−' }}{{ number_format($movement->quantity) }}
                            <div class="small fw-normal d-sm-none">{{ $isEntry ? 'Entrada' : 'Salida' }}</div>
                        </td>
                        <td class="text-end">{{ number_format($movement->balance_after) }}</td>
                        <td class="d-none d-md-table-cell text-secondary">{{ $reference }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-secondary py-5">
                            <i class="bi bi-journal-x fs-2 d-block mb-2" aria-hidden="true"></i>
                            @if (! $hasAnyMovement)
                                Aún no hay movimientos registrados. Se generan automáticamente al confirmar compras y ventas.
                            @elseif ($hasFilters || $selectedProduct)
                                No se encontraron movimientos con los filtros aplicados.
                                <a href="{{ route('inventory.movements') }}">Ver todo el historial</a>
                            @else
                                No hay movimientos para mostrar.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $movements->links('pagination::bootstrap-5') }}</div>
@endsection
