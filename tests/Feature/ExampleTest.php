<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_panel_principal_redirige_si_no_hay_sesion(): void
    {
        // El panel debe solicitar el inicio de sesión si el usuario no está autenticado.
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_el_administrador_puede_acceder_al_panel_principal(): void
    {
        // Crear un administrador para comprobar el acceso al panel.
        $usuario = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($usuario)->get('/');

        $response->assertStatus(200);
    }
}
