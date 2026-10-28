@extends('layouts.app')

@section('title', 'Panel general | FarmaGest')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <div class="app-eyebrow">RESUMEN DEL SISTEMA</div>
        <h1 class="app-page-title mb-2">Panel general</h1>
        <p class="text-secondary mb-0">Consulta el inventario y accede a las operaciones de tu farmacia.</p>
    </div>
    <a href="{{ route('products.create') }}" class="btn btn-success d-inline-flex align-items-center gap-2">
        <i class="bi bi-plus-lg" aria-hidden="true"></i> Nuevo producto
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Productos registrados</span>
                    <span class="app-metric-icon bg-primary-subtle text-primary"><i class="bi bi-capsule"></i></span>
                </div>
                <div class="app-metric-value">{{ number_format($totalProducts) }}</div>
                <div class="text-secondary small">Productos guardados en el catálogo</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Productos con stock bajo</span>
                    <span class="app-metric-icon bg-warning-subtle text-warning-emphasis"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
                <div class="app-metric-value">{{ number_format($lowStockProducts) }}</div>
                <div class="text-secondary small">Existencias menores o iguales al mínimo</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-12 col-xl-4">
        <div class="card app-metric-card h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <span class="app-metric-label">Estado del proyecto</span>
                    <span class="app-metric-icon bg-success-subtle text-success"><i class="bi bi-tools"></i></span>
                </div>
                <div class="fw-bold fs-4 mt-4 mb-2">En desarrollo</div>
                <div class="text-secondary small">Los módulos se habilitarán conforme se integren.</div>
            </div>
        </div>
    </div>
</div>

<div class="card app-panel-card">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h2 class="h5 mb-1">Accesos rápidos</h2>
                <p class="text-secondary small mb-0">Comienza por el catálogo de productos.</p>
            </div>
            <span class="badge text-bg-light border">Módulos en construcción</span>
        </div>
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <a class="app-quick-link" href="{{ route('products.index') }}">
                    <span class="app-quick-icon"><i class="bi bi-box-seam" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">
                        <strong class="d-block">Gestionar productos</strong>
                        <span class="text-secondary small">Registrar, consultar y editar medicamentos</span>
                    </span>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
            <div class="col-12 col-md-6">
                <div class="app-quick-link app-quick-link-disabled">
                    <span class="app-quick-icon"><i class="bi bi-receipt" aria-hidden="true"></i></span>
                    <span class="flex-grow-1">
                        <strong class="d-block">Compras y ventas</strong>
                        <span class="text-secondary small">Disponible cuando se integren los módulos</span>
                    </span>
                    <span class="badge text-bg-light border">Pronto</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
