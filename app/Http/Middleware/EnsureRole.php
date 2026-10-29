<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /*Verifica que el usuario tenga uno de los roles permitidos.
    *Los roles se reciben desde las rutas, por ejemplo: admin.
    * Si no tiene permiso, se devuelve una respuesta de acceso denegado.*/
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Si no hay una sesión autenticada, redirigir al inicio de sesión.
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Impedir el acceso cuando el rol del usuario no está autorizado.
        if (!in_array($request->user()->role, $roles, true)) {
            abort(403, 'No tienes permiso para realizar esta acción.');
        }

        return $next($request);
    }
}
