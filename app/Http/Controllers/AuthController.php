<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthController extends Controller
{
    /* Muestra el formulario de inicio de sesión.*/
    public function showLogin()
    {
        return view('auth.login');
    }

    /*Valida las credenciales e inicia la sesión.*/
    public function login(Request $request)
    {
        // Validar que el correo y la contraseña sean obligatorios.
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        // Comprobar las credenciales utilizando la autenticación de Laravel.
        if (Auth::attempt($credenciales)) {
            $request->session()->regenerate();

            // Redirigir al usuario según su rol.
            if ($request->user()->role === 'admin') {
                return redirect()->intended(route('dashboard'));
            }

            // Si Ventas está integrado, dirigir al cajero a ese módulo.
            if (Route::has('sales.index')) {
                return redirect()->route('sales.index');
            }

            // Mientras Ventas no esté integrado, mostrar Productos.
            return redirect()->route('products.index');
        }

        // Si los datos son incorrectos, mostrar un mensaje y conservar solo el correo.
        return back()
            ->withErrors([
                'email' => 'El correo electrónico o la contraseña son incorrectos.',
            ])
            ->onlyInput('email');
    }

    /* Cierra la sesión del usuario de forma segura.*/
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); // Invalidar la sesión actual y eliminar sus datos.
        $request->session()->regenerateToken(); // Generar un nuevo token para proteger los formularios.

        return redirect()
            ->route('login')
            ->with('status', 'Has cerrado sesión correctamente.');
    }
}
