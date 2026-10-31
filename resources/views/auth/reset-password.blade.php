<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nueva contraseña | FarmaGest</title>
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
                    <h2 class="h5 mb-2">Establecer nueva contraseña</h2>
                    <p class="text-secondary small mb-0">
                        Escribe una contraseña de al menos 12 caracteres y confírmala.
                    </p>
                </header>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="{{ old('email', $email) }}" autocomplete="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Nueva contraseña</label>
                        <input type="password" class="form-control" id="password" name="password"
                               minlength="12" autocomplete="new-password" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="password_confirmation"
                               name="password_confirmation" minlength="12"
                               autocomplete="new-password" required>
                    </div>
                    <button class="btn btn-success w-100" type="submit">
                        <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                        Actualizar contraseña
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('password.request') }}" class="link-success">Solicitar otro enlace</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
