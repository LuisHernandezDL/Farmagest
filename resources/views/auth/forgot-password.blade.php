<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Recuperar contraseña | FarmaGest</title>
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
                    <h2 class="h5 mb-2">Recuperar contraseña</h2>
                    <p class="text-secondary small mb-0">
                        Ingresa el correo de tu cuenta para solicitar un enlace de recuperación.
                    </p>
                </header>

                @if (session('status'))
                    <div class="alert alert-success" role="status">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label" for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="{{ old('email') }}" autocomplete="email" required autofocus>
                    </div>
                    <button class="btn btn-success w-100" type="submit">
                        <i class="bi bi-envelope me-1" aria-hidden="true"></i>
                        Solicitar enlace de recuperación
                    </button>
                </form>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="link-success">Volver a iniciar sesión</a>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
