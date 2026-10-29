<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FarmaGest')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/farmagest.css') }}" rel="stylesheet">
</head>
<body class="app-body">
@php
    // Las opciones futuras permanecen deshabilitadas hasta que sus responsables registren las rutas.
    $menu = [
        [
            'title' => 'GENERAL',
            'items' => [
                ['label' => 'Panel general', 'route' => 'dashboard', 'icon' => 'bi-grid-1x2'],
                ['label' => 'Productos', 'route' => 'products.index', 'icon' => 'bi-capsule'],
                ['label' => 'Proveedores', 'route' => 'suppliers.index', 'icon' => 'bi-truck'],
            ],
        ],
        [
            'title' => 'OPERACIONES',
            'items' => [
                ['label' => 'Compras', 'route' => 'purchases.index', 'icon' => 'bi-bag-plus'],
                ['label' => 'Inventario', 'route' => 'inventory.index', 'icon' => 'bi-box-seam'],
                ['label' => 'Ventas', 'route' => 'sales.index', 'icon' => 'bi-receipt'],
            ],
        ],
        [
            'title' => 'ADMINISTRACIÓN',
            'items' => [
                ['label' => 'Reportes', 'route' => 'reports.index', 'icon' => 'bi-bar-chart'],
                ['label' => 'Usuarios', 'route' => 'users.index', 'icon' => 'bi-people'],
            ],
        ],
    ];
@endphp
<div class="app-shell">
    <aside class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="farmagestSidebar" aria-labelledby="farmagestSidebarLabel">
        <div class="offcanvas-header d-lg-none">
            <strong id="farmagestSidebarLabel">Navegación</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#farmagestSidebar" aria-label="Cerrar menú"></button>
        </div>

        <div class="app-brand">
            <a class="app-brand-link" href="{{ route('dashboard') }}">
                <span class="app-brand-symbol"><i class="bi bi-heart-pulse" aria-hidden="true"></i></span>
                <span>
                    <span class="app-brand-name">FarmaGest</span>
                    <span class="app-brand-caption">Gestión de farmacia</span>
                </span>
            </a>
        </div>

        <nav class="app-navigation" aria-label="Menú principal">
            @foreach ($menu as $section)
                <p class="app-nav-heading">{{ $section['title'] }}</p>
                @foreach ($section['items'] as $item)
                    @php
                        $available = \Illuminate\Support\Facades\Route::has($item['route']);
                        $prefix = explode('.', $item['route'])[0];
                        $active = request()->routeIs($prefix . '*');
                    @endphp

                    @if ($available)
                        <a href="{{ route($item['route']) }}" class="app-nav-link {{ $active ? 'is-active' : '' }}" @if ($active) aria-current="page" @endif>
                            <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                            <span>{{ $item['label'] }}</span>
                            @if ($active) <i class="bi bi-chevron-right ms-auto app-nav-chevron" aria-hidden="true"></i> @endif
                        </a>
                    @else
                        <span class="app-nav-link is-disabled" title="Módulo en desarrollo" aria-disabled="true">
                            <i class="bi {{ $item['icon'] }}" aria-hidden="true"></i>
                            <span>{{ $item['label'] }}</span>
                            <small class="ms-auto">Pronto</small>
                        </span>
                    @endif
                @endforeach
            @endforeach
        </nav>
        <div class="app-sidebar-footer">
            <div class="small fw-semibold">Proyecto académico</div>
            <div class="small opacity-75">Versión de desarrollo</div>
        </div>
    </aside>

    <div class="app-main">
        <header class="app-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn app-menu-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#farmagestSidebar" aria-controls="farmagestSidebar" aria-label="Abrir menú">
                    <i class="bi bi-list fs-4" aria-hidden="true"></i>
                </button>
                <div>
                    <div class="app-topbar-label">Sistema de administración</div>
                    <div class="app-topbar-title">FarmaGest</div>
                </div>
            </div>
            <span class="badge rounded-pill app-environment-badge">Entorno de desarrollo</span>
        </header>

        <main class="app-content">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2" aria-hidden="true"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar alerta"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar alerta"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
