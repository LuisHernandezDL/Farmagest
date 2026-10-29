<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_formulario_de_login_se_muestra_correctamente(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    public function test_un_usuario_con_credenciales_validas_puede_iniciar_sesion(): void
    {
        $usuario = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('ClavePrueba123!'),
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'ClavePrueba123!',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_una_contrasena_incorrecta_es_rechazada(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('ClavePrueba123!'),
            'role' => 'admin',
        ]);

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => 'admin@example.com',
            'password' => 'ContrasenaIncorrecta123!',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors([
            'email' => 'El correo electrónico o la contraseña son incorrectos.',
        ]);
        $this->assertGuest();
    }

    public function test_el_cierre_de_sesion_finaliza_la_autenticacion(): void
    {
        $usuario = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($usuario)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_no_se_puede_acceder_al_dashboard_despues_de_cerrar_sesion(): void
    {
        $usuario = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($usuario)->post(route('logout'));

        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }
}
