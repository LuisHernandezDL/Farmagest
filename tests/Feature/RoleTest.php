<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_cajero_puede_consultar_productos(): void
    {
        $cajero = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this->actingAs($cajero)->get(route('products.index'));

        $response->assertStatus(200);
    }

    public function test_el_cajero_no_puede_acceder_al_dashboard(): void
    {
        $cajero = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this->actingAs($cajero)->get(route('dashboard'));

        $response->assertForbidden();
    }

    public function test_el_cajero_no_puede_abrir_el_formulario_para_crear_productos(): void
    {
        $cajero = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this->actingAs($cajero)->get(route('products.create'));

        $response->assertForbidden();
    }

    public function test_el_cajero_no_puede_registrar_productos(): void
    {
        $cajero = User::factory()->create([
            'role' => 'cashier',
        ]);

        $response = $this->actingAs($cajero)->post(route('products.store'), []);

        $response->assertForbidden();
    }

    public function test_el_cajero_no_puede_abrir_el_formulario_para_editar_productos(): void
    {
        $cajero = User::factory()->create([
            'role' => 'cashier',
        ]);

        $producto = $this->crearProductoDePrueba();

        $response = $this->actingAs($cajero)->get(
            route('products.edit', $producto)
        );

        $response->assertForbidden();
    }

    public function test_el_cajero_no_puede_actualizar_productos(): void
    {
        $cajero = User::factory()->create([
            'role' => 'cashier',
        ]);

        $producto = $this->crearProductoDePrueba();

        $response = $this->actingAs($cajero)->put(
            route('products.update', $producto),
            []
        );

        $response->assertForbidden();
    }

    public function test_el_cajero_no_puede_eliminar_productos(): void
    {
        $cajero = User::factory()->create([
            'role' => 'cashier',
        ]);

        $producto = $this->crearProductoDePrueba();

        $response = $this->actingAs($cajero)->delete(
            route('products.destroy', $producto)
        );

        $response->assertForbidden();
    }

    private function crearProductoDePrueba(): Product
    {
        $categoria = Category::query()->first();

        if (!$categoria) {
            $categoria = Category::create([
                'name' => 'Categoría de prueba',
            ]);
        }

        return Product::create([
            'category_id' => $categoria->id,
            'code' => 'TEST-' . uniqid(),
            'name' => 'Producto de prueba',
            'purchase_price' => 10,
            'sale_price' => 15,
            'stock' => 20,
            'min_stock' => 5,
        ]);
    }
}
