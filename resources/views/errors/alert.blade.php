<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Error | FarmaGest</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <main class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <h1 class="h4">FarmaGest</h1>
            <p class="text-secondary">
                Estamos preparando tu regreso al sistema.
            </p>

            <a href="{{ $redirectUrl }}" class="btn btn-success">
                Volver al sistema
            </a>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                icon: 'error',
                title: @json($title),
                text: @json($message),
                confirmButtonText: 'Volver al sistema',
                confirmButtonColor: '#147D64',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(function () {
                window.location.href = @json($redirectUrl);
            });
        });
    </script>
</body>
</html>
