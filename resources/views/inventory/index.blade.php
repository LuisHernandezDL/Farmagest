@extends('layouts.app')

@section('title', 'Inventario | FarmaGest')

@section('content')
@php
    $hasFilters = $filters['search'] !== '' || $filters['category'] > 0 || $filters['status'] !== '';
    $statusOptions = [
        '' => 'Todos los estados',
        'low' => 'Stock bajo o agotado',
        'out' => 'Solo agotados',
        'available' => 'Disponibles',
    ];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
    <div>
        <div class="app-eyebrow">OPERACIONES</div>
        <h1 class="app-page-title mb-2">Inventario</h1>
        <p class="text-secondary mb-0">Consulta las existencias actuales y detecta los medicamentos que necesitan reposición.</p>
    </div>
</div>

<ul class="nav nav-pills gap-2 mb-4" style="--bs-nav-pills-link-active-bg: var(--fg-primary);">
    <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="{{ route('inventory.index') }}">
            <i class="bi bi-box-seam me-1" aria-hidden="true"></i> Existencias
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link text-secondary" href="{{ route('inventory.movements') }}">
            <i class="bi bi-clock-history me-1" aria-hidden="true"></i> Movimientos
        </a>
    </li>
</ul>

<div class="row g-3 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card app-metric-card h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="app-metric-label">Productos</span>
                    <span class="app-metric-icon bg-primary-subtle text-primary"><i class="bi bi-capsule" aria-hidden="true"></i></span>
                </div>
                <div class="app-metric-value">{{ number_format($summary['total']) }}</div>
                <div class="text-secondary small">Medicamentos en el catálogo</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card app-metric-card h-100">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="app-metric-label">Unidades</span>
                    <span class="app-metric-icon bg-success-subtle text-success"><i class="bi bi-stack" aria-hidden="true"></i></span>
                </div>
                <div class="app-metric-value">{{ number_format($summary['units']) }}</div>
                <div class="text-secondary small">Existencias totales disponibles</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('inventory.index', ['status' => 'low']) }}" class="card app-metric-card h-100 text-decoration-none text-reset">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="app-metric-label">Stock bajo</span>
                    <span class="app-metric-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-exclamation-triangle" aria-hidden="true"></i></span>
                </div>
                <div class="app-metric-value">{{ number_format($summary['low']) }}</div>
                <div class="text-secondary small">En el mínimo o por debajo</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('inventory.index', ['status' => 'out']) }}" class="card app-metric-card h-100 text-decoration-none text-reset">
            <div class="card-body p-3 p-md-4">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <span class="app-metric-label">Agotados</span>
                    <span class="app-metric-icon bg-danger-subtle text-danger"><i class="bi bi-x-octagon" aria-hidden="true"></i></span>
                </div>
                <div class="app-metric-value">{{ number_format($summary['out']) }}</div>
                <div class="text-secondary small">Sin existencias disponibles</div>
            </div>
        </a>
    </div>
</div>

@if ($summary['low'] > 0 && $filters['status'] === '')
    <div class="alert alert-warning d-flex flex-wrap align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill" aria-hidden="true"></i>
        <span>
            <strong>{{ $summary['low'] }}</strong>
            {{ $summary['low'] === 1 ? 'medicamento necesita' : 'medicamentos necesitan' }} reposición.
        </span>
        <a href="{{ route('inventory.index', ['status' => 'low']) }}" class="alert-link ms-md-auto">Ver listado</a>
    </div>
@endif

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('inventory.index') }}" class="row g-2 align-items-end" role="search">
            <div class="col-12 col-lg-4">
                <label for="search" class="form-label small text-secondary mb-1">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                    <input type="search" id="search" name="search" class="form-control" maxlength="100"
                           value="{{ $filters['search'] }}" placeholder="Código o nombre">
                </div>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="category" class="form-label small text-secondary mb-1">Categoría</label>
                <select id="category" name="category" class="form-select">
                    <option value="">Todas las categorías</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected($filters['category'] === $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-6 col-lg-3">
                <label for="status" class="form-label small text-secondary mb-1">Estado</label>
                <select id="status" name="status" class="form-select">
                    @foreach ($statusOptions as $value => $label)
                        <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-2 d-flex gap-2">
                <button type="submit" class="btn btn-success flex-grow-1">
                    <i class="bi bi-funnel me-1" aria-hidden="true"></i> Filtrar
                </button>
                @if ($hasFilters)
                    <a href="{{ route('inventory.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros" aria-label="Limpiar filtros">
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
                    <th scope="col" class="d-none d-sm-table-cell">Código</th>
                    <th scope="col">Medicamento</th>
                    <th scope="col" class="d-none d-md-table-cell">Categoría</th>
                    <th scope="col" class="text-end">Existencia</th>
                    <th scope="col" class="text-end d-none d-sm-table-cell">Mínimo</th>
                    <th scope="col" class="d-none d-sm-table-cell">Estado</th>
                    <th scope="col" class="text-end">Historial</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    @php
                        $isOut = $product->stock === 0;
                        $isLow = $product->stock <= $product->min_stock;
                        // La barra toma como referencia el doble del mínimo para mostrar qué tan cerca está del límite.
                        $reference = max($product->min_stock * 2, 1);
                        $level = min(100, (int) round($product->stock / $reference * 100));
                        $barClass = $isOut ? 'bg-danger' : ($isLow ? 'bg-warning' : 'bg-success');
                    @endphp
                    <tr class="{{ $isOut ? 'table-danger' : ($isLow ? 'table-warning' : '') }}">
                        <td class="text-nowrap fw-semibold d-none d-sm-table-cell">{{ $product->code }}</td>
                        <td>
                            <div class="small fw-semibold d-sm-none">{{ $product->code }}</div>
                            {{ $product->name }}
                            <div class="small text-secondary d-md-none">{{ $product->category?->name }}</div>
                            @if ($isLow)
                                <span class="badge {{ $isOut ? 'text-bg-danger' : 'text-bg-warning' }} d-sm-none mt-1">{{ $isOut ? 'Agotado' : 'Stock bajo' }}</span>
                            @endif
                        </td>
                        <td class="d-none d-md-table-cell">{{ $product->category?->name }}</td>
                        <td class="text-end" style="min-width: 90px;">
                            <span class="fw-bold">{{ number_format($product->stock) }}</span>
                            <div class="progress mt-1" style="height: 5px;" role="progressbar"
                                 aria-label="Nivel de existencias" aria-valuenow="{{ $level }}" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar {{ $barClass }}" style="width: {{ $level }}%"></div>
                            </div>
                        </td>
                        <td class="text-end d-none d-sm-table-cell">{{ number_format($product->min_stock) }}</td>
                        <td class="text-nowrap d-none d-sm-table-cell">
                            @if ($isOut)
                                <span class="badge text-bg-danger"><i class="bi bi-x-octagon me-1" aria-hidden="true"></i>Agotado</span>
                            @elseif ($isLow)
                                <span class="badge text-bg-warning"><i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>Stock bajo</span>
                            @else
                                <span class="badge bg-success-subtle text-success-emphasis"><i class="bi bi-check-circle me-1" aria-hidden="true"></i>Disponible</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('inventory.movements', ['product' => $product->id]) }}" class="btn btn-sm btn-outline-secondary text-nowrap"
                               title="Ver movimientos de {{ $product->name }}">
                                <i class="bi bi-clock-history" aria-hidden="true"></i>
                                <span class="d-none d-lg-inline ms-1">Movimientos</span>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-secondary py-5">
                            <i class="bi bi-inbox fs-2 d-block mb-2" aria-hidden="true"></i>
                            @if ($hasFilters)
                                No se encontraron medicamentos con los filtros aplicados.
                                <a href="{{ route('inventory.index') }}">Limpiar filtros</a>
                            @else
                                Todavía no hay medicamentos registrados en el catálogo.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection
