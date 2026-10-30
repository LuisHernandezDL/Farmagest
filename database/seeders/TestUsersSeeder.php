<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TestUsersSeeder extends Seeder
{
    /* Crea las cuentas locales para probar los roles. */
    public function run(): void
    {
        // Evitar que las cuentas de prueba se creen o actualicen en producción.
        if (app()->environment('production')) {
            throw new RuntimeException(
                'Este seeder solo puede ejecutarse en desarrollo o pruebas.'
            );
        }

        // Leer las credenciales desde la configuración local de Laravel.
        $usuarios = [
            [
                'name' => 'Admin',
                'email' => 'admin@farmagest.local',
                'password' => config('app.test_admin_password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Cajero',
                'email' => 'cajero@farmagest.local',
                'password' => config('app.test_cashier_password'),
                'role' => 'cashier',
            ],
        ];

        foreach ($usuarios as $datos) {
            $contrasena = $datos['password'];

            if (!is_string($contrasena) || strlen($contrasena) < 12) {
                throw new RuntimeException(
                    'Configura una contraseña de prueba de al menos 12 caracteres en el archivo .env local.'
                );
            }

            User::updateOrCreate(
                ['email' => $datos['email']],
                [
                    'name' => $datos['name'],
                    'password' => Hash::make($contrasena),
                    'role' => $datos['role'],
                ]
            );
        }

        Log::info('Se prepararon las cuentas locales de prueba de FarmaGest.');
    }
}
