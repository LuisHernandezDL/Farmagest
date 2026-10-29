<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_panel_principal_responde_correctamente(): void
    {
        // El panel consulta productos, por lo que necesita el esquema de prueba migrado.
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
