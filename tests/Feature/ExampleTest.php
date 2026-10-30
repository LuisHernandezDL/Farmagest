<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    // Comprueba que el dashboard cargue correctamente y muestre los indicadores en cero cuando no existen registros.
    public function test_el_panel_principal_responde_correctamente(): void
    {
        // Accede a la página principal del sistema.
        $response = $this->get('/');

        // Verifica que la página responda correctamente.
        $response->assertStatus(200);

        // Comprueba que se esté utilizando la vista principal del dashboard.
        $response->assertViewIs('dashboard.index');

        // Verifica que los indicadores tengan valores en cero.
        $response->assertViewHas('totalProducts', 0);
        $response->assertViewHas('lowStockProducts', 0);
        $response->assertViewHas('todaySales', 0);
        $response->assertViewHas('totalSoldToday', 0);
        $response->assertViewHas('todayPurchases', 0);
        $response->assertViewHas('totalPurchasedToday', 0);
    }
}
