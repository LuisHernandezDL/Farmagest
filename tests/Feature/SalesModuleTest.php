<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_invitado_no_puede_ver_ni_registrar_ventas(): void
    {
        $this->get(route('sales.index'))->assertRedirect(route('login'));
        $this->get(route('sales.create'))->assertRedirect(route('login'));
        $this->post(route('sales.store'), [])->assertRedirect(route('login'));
    }

    public function test_administrador_y_cajero_pueden_acceder_a_ventas(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $cashier = User::factory()->create(['role' => 'cashier']);

        $this->actingAs($admin)->get(route('sales.index'))->assertOk();
        $this->actingAs($cashier)->get(route('sales.index'))->assertOk();
    }

    private function product(string $code, int $stock, string $price): Product
    {
        $category = Category::firstOrCreate(['name' => 'Medicamentos de prueba']);

        return Product::create([
            'category_id' => $category->id,
            'code' => $code,
            'name' => 'Producto ' . $code,
            'purchase_price' => '4.00',
            'sale_price' => $price,
            'stock' => $stock,
            'min_stock' => 2,
        ]);
    }

    public function test_guarda_venta_calcula_total_y_descuenta_inventario(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $first = $this->product('MED-101', 20, '12.50');
        $second = $this->product('MED-102', 10, '7.25');

        $response = $this->post(route('sales.store'), [
            'items' => [
                ['product_id' => $first->id, 'quantity' => 3],
                ['product_id' => $second->id, 'quantity' => 2],
            ],
            'total' => '0.01', // Este importe enviado no debe utilizarse.
        ]);

        $sale = Sale::firstOrFail();
        $response->assertRedirect(route('sales.show', $sale));
        $this->assertSame('confirmed', $sale->status);
        $this->assertSame('52.00', $sale->total);
        $this->assertSame(17, $first->fresh()->stock);
        $this->assertSame(8, $second->fresh()->stock);
        $this->assertDatabaseCount('sale_items', 2);
        $this->assertDatabaseCount('stock_movements', 2);
        $this->assertDatabaseHas('stock_movements', ['direction' => 'out', 'source_type' => 'sale_item']);
    }

    public function test_stock_insuficiente_revierte_toda_la_venta(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $first = $this->product('MED-103', 10, '5.00');
        $second = $this->product('MED-104', 1, '4.00');

        $response = $this->from(route('sales.create'))->post(route('sales.store'), [
            'items' => [
                ['product_id' => $first->id, 'quantity' => 2],
                ['product_id' => $second->id, 'quantity' => 3],
            ],
        ]);

        $response->assertRedirect(route('sales.create'));
        $response->assertSessionHasErrors('inventory');
        $this->assertSame(10, $first->fresh()->stock);
        $this->assertSame(1, $second->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_no_permita_medicamentos_repetidos(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product('MED-105', 10, '5.00');

        $this->post(route('sales.store'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ])->assertSessionHasErrors('items');

        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_visualiza_historial_y_comprobante(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $product = $this->product('MED-106', 5, '6.00');

        $this->post(route('sales.store'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
        ]);

        $sale = Sale::firstOrFail();
        $this->get(route('sales.index'))->assertOk()->assertSee('Ventas');
        $this->get(route('sales.show', $sale))->assertOk()->assertSee('No es una factura fiscal');
    }
}
