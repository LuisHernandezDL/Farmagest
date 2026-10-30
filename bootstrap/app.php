<?php

use App\Http\Middleware\EnsureRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar el alias para comprobar los roles en las rutas.
        $middleware->alias([
            'role' => EnsureRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Personalizar los errores 403 y 404.
        $exceptions->render(function (
            HttpExceptionInterface $e,
            Request $request
        ) {
            $codigo = $e->getStatusCode();

            if (
                $request->expectsJson()
                || !in_array($codigo, [403, 404], true)
            ) {
                return null;
            }

            $usuario = $request->user();

            if (!$usuario) {
                $redirectUrl = route('login');
            } elseif ($usuario->role === 'admin') {
                $redirectUrl = route('dashboard');
            } else {
                $redirectUrl = route('products.index');
            }

            return response()->view('errors.alert', [
                'title' => $codigo === 403
                    ? 'Acceso denegado'
                    : 'Página no encontrada',
                'message' => $codigo === 403
                    ? 'No tienes permiso para acceder a esta página.'
                    : 'La página que buscas no existe o ya no está disponible.',
                'redirectUrl' => $redirectUrl,
            ], $codigo);
        });
    })->create();