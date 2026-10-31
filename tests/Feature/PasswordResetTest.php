<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitado_puede_abrir_el_formulario_de_recuperacion(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Recuperar contraseña');
    }

    public function test_login_muestra_acceso_a_recuperacion(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('¿Olvidaste tu contraseña?');
    }

    public function test_usuario_registrado_recibe_notificacion_de_recuperacion(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect()
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_un_correo_inexistente_no_revela_si_hay_cuenta(): void
    {
        Notification::fake();

        $response = $this->post(route('password.email'), [
            'email' => 'noexiste@example.com',
        ]);

        $response->assertRedirect()->assertSessionHas(
            'status',
            'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña.'
        );

        Notification::assertNothingSent();
    }

    public function test_enlace_de_recuperacion_muestra_el_formulario(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->get(route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]))->assertOk()
            ->assertSee('Establecer nueva contraseña')
            ->assertSee($user->email);
    }

    public function test_token_valido_permite_actualizar_contrasena(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('AnteriorClave123!'),
        ]);
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NuevaClaveSegura123!',
            'password_confirmation' => 'NuevaClaveSegura123!',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('NuevaClaveSegura123!', $user->fresh()->password));
        $this->assertFalse(Password::tokenExists($user, $token));
    }

    public function test_token_invalido_no_modifica_contrasena(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('AnteriorClave123!'),
        ]);

        $this->post(route('password.update'), [
            'token' => 'token-incorrecto',
            'email' => $user->email,
            'password' => 'NuevaClaveSegura123!',
            'password_confirmation' => 'NuevaClaveSegura123!',
        ])->assertSessionHasErrors('token');

        $this->assertTrue(Hash::check('AnteriorClave123!', $user->fresh()->password));
    }

    public function test_contrasena_corta_o_sin_confirmacion_es_rechazada(): void
    {
        $this->post(route('password.update'), [
            'token' => 'token-incorrecto',
            'email' => 'admin@example.com',
            'password' => 'Corta123',
            'password_confirmation' => 'Diferente123',
        ])->assertSessionHasErrors('password');
    }

    public function test_interfaz_administrativa_no_muestra_modulos_pendientes(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Entorno de desarrollo')
            ->assertDontSee('Reportes')
            ->assertDontSee('Usuarios')
            ->assertDontSee('Versión de desarrollo')
            ->assertDontSee('Proyecto académico');
    }
}
