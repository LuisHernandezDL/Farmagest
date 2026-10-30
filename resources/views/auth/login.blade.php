<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Iniciar sesión | FarmaGest</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/farmagest.css') }}" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>

    <main class="login-page d-flex align-items-center justify-content-center p-3">
        <section class="card login-card shadow-sm">
            <div class="card-body p-4 p-md-5">

                <header class="text-center mb-4">
                    <div class="login-symbol mb-3">
                        <i class="bi bi-heart-pulse" aria-hidden="true"></i>
                    </div>

                    <h1 class="h3 login-title mb-1">FarmaGest</h1>
                    <p class="text-secondary mb-4">Gestión de farmacia</p>

                    <h2 class="h5 mb-2">Iniciar sesión</h2>
                    <p class="text-secondary small mb-0">
                        Ingresa tus credenciales para continuar
                    </p>
                </header>

                @if ($errors->any())
                    <div id="login-error-message" class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- El token CSRF protege el envío del formulario. --}}
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">
                            Correo electrónico
                        </label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">
                            Contraseña
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-box-arrow-in-right me-1" aria-hidden="true"></i>
                        Iniciar sesión
                    </button>
                </form>

                <footer class="text-center text-secondary small mt-4">
                    Acceso exclusivo para usuarios autorizados
                </footer>

            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Mantener el error de credenciales dentro del formulario.
            const mensajeError = document.getElementById('login-error-message');

            if (mensajeError) {
                setTimeout(function () {
                    mensajeError.style.transition = 'opacity 0.5s ease';
                    mensajeError.style.opacity = '0';

                    setTimeout(function () {
                        mensajeError.remove();
                    }, 500);
                }, 4000);
            }

            // Mostrar la confirmación al cerrar sesión.
            @if (session('status'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sesión cerrada',
                    text: @json(session('status')),
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#147D64'
                });
            @endif
        });
    </script>
</body>
</html>

