@extends('layouts.app')

@section('title', 'Panel general | FarmaGest')

@section('content')

{{-- Encabezado principal del dashboard --}}
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <div class="app-eyebrow">RESUMEN DEL SISTEMA</div>
        <h1 class="app-page-title mb-2">Panel general</h1>
        <p class="text-secondary mb-0">Consulta el inventario y las operaciones de tu farmacia.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-success d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo producto
    </a>
</div>

{{-- Indicadores principales de productos, ventas y compras --}}
<div class="row g-3 mb-4">

    {{-- Total de productos registrados --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Productos registrados</span>
                    <span class="app-metric-icon bg-primary-subtle text-primary">
                        <i class="bi bi-capsule"></i>
                    </span>
                </div>
                <div class="app-metric-value">{{ number_format($totalProducts) }}</div>
                <div class="text-secondary small">Productos guardados en el catálogo</div>
            </div>
        </div>
    </div>

    {{-- Productos cuya existencia es igual o inferior al stock mínimo --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Productos con stock bajo</span>
                    <span class="app-metric-icon bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-exclamation-triangle"></i>
                    </span>
                </div>
                <div class="app-metric-value">{{ number_format($lowStockProducts) }}</div>
                <div class="text-secondary small">Existencias menores o iguales al mínimo</div>
            </div>
        </div>
    </div>

    {{-- Cantidad de ventas confirmadas durante el día --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Ventas del día</span>
                    <span class="app-metric-icon bg-success-subtle text-success">
                        <i class="bi bi-cart-check"></i>
                    </span>
                </div>
                <div class="app-metric-value">{{ number_format($todaySales) }}</div>
                <div class="text-secondary small">Operaciones confirmadas durante el día</div>
            </div>
        </div>
    </div>

    {{-- Importe total de las ventas confirmadas durante el día --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Total vendido hoy</span>
                    <span class="app-metric-icon bg-info-subtle text-info-emphasis">
                        <i class="bi bi-currency-dollar"></i>
                    </span>
                </div>
                <div class="app-metric-value">Q {{ number_format((float) $totalSoldToday, 2) }}</div>
                <div class="text-secondary small">Importe de las ventas confirmadas</div>
            </div>
        </div>
    </div>

    {{-- Cantidad de compras confirmadas durante el día --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Compras del día</span>
                    <span class="app-metric-icon bg-primary-subtle text-primary">
                        <i class="bi bi-bag-check"></i>
                    </span>
                </div>
                <div class="app-metric-value">{{ number_format($todayPurchases) }}</div>
                <div class="text-secondary small">Operaciones confirmadas durante el día</div>
            </div>
        </div>
    </div>

    {{-- Importe total de las compras confirmadas durante el día --}}
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Total comprado hoy</span>
                    <span class="app-metric-icon bg-warning-subtle text-warning-emphasis">
                        <i class="bi bi-receipt"></i>
                    </span>
                </div>
                <div class="app-metric-value">Q {{ number_format((float) $totalPurchasedToday, 2) }}</div>
                <div class="text-secondary small">Importe de las compras confirmadas</div>
            </div>
        </div>
    </div>
</div>

{{-- Accesos rápidos a los módulos disponibles del sistema --}}
<div class="card app-panel-card">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">Accesos rápidos</h2>
                <p class="text-secondary small mb-0">Accede a las funciones disponibles del sistema.</p>
            </div>
            <span class="badge text-bg-light border">Módulos en integración</span>
        </div>

        <div class="row g-3">

            {{-- Acceso al módulo de productos --}}
            <div class="col-12 col-md-6">
                <a class="app-quick-link" href="{{ route('products.index') }}">
                    <span class="app-quick-icon">
                        <i class="bi bi-box-seam" aria-hidden="true"></i>
                    </span>
                    <span class="flex-grow-1">
                        <strong class="d-block">Gestionar productos</strong>
                        <span class="text-secondary small">Registrar, consultar y editar medicamentos</span>
                    </span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="col-12 col-md-6">
                <a class="app-quick-link" href="{{ route('purchases.index') }}">
                    <span class="app-quick-icon"><i class="bi bi-bag-plus" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">
                        <strong class="d-block">Gestionar compras</strong>
                        <span class="text-secondary small">Registrar y confirmar compras</span>
                    </span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="col-12 col-md-6">
                <a class="app-quick-link" href="{{ route('inventory.index') }}">
                    <span class="app-quick-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">
                        <strong class="d-block">Consultar inventario</strong>
                        <span class="text-secondary small">Existencias y movimientos</span>
                    </span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            <div class="col-12 col-md-6">
                <a class="app-quick-link" href="{{ route('suppliers.index') }}">
                    <span class="app-quick-icon"><i class="bi bi-truck" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">
                        <strong class="d-block">Gestionar proveedores</strong>
                        <span class="text-secondary small">Consultar y registrar proveedores</span>
                    </span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
