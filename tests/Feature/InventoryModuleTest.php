<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryModuleTest extends TestCase
{
    use RefreshDatabase;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = Category::create(['name' => 'Analgésicos']);
    }

    private function createProduct(string $code, string $name, int $stock, int $minStock = 5): Product
    {
        return Product::create([
            'category_id' => $this->category->id,
            'code' => $code,
            'name' => $name,
            'purchase_price' => 5,
            'sale_price' => 8,
            'stock' => $stock,
            'min_stock' => $minStock,
        ]);
    }

    public function test_el_listado_muestra_existencias_y_stock_minimo(): void
    {
        $this->createProduct('MED-001', 'Acetaminofén 500 mg', 40, 10);

        $this->get(route('inventory.index'))
            ->assertOk()
            ->assertSee('MED-001')
            ->assertSee('Acetaminofén 500 mg')
            ->assertSee('Disponible');
    }

    public function test_detecta_stock_bajo_y_productos_agotados(): void
    {
        $this->createProduct('MED-001', 'Producto suficiente', 20, 5);
        $this->createProduct('MED-002', 'Producto en el minimo', 5, 5);
        $this->createProduct('MED-003', 'Producto agotado', 0, 5);

        $response = $this->get(route('inventory.index'))->assertOk();

        // Se usa el mismo criterio del panel: existencia menor o igual al mínimo.
        $this->assertSame(2, $response->viewData('summary')['low']);
        $this->assertSame(1, $response->viewData('summary')['out']);
        $response->assertSee('Stock bajo')->assertSee('Agotado');

        $this->get(route('inventory.index', ['status' => 'low']))
            ->assertSee('Producto en el minimo')
            ->assertSee('Producto agotado')
            ->assertDontSee('Producto suficiente');

        $this->get(route('inventory.index', ['status' => 'out']))
            ->assertSee('Producto agotado')
            ->assertDontSee('Producto en el minimo');
    }

    public function test_busca_por_codigo_o_nombre(): void
    {
        $this->createProduct('ACE-500', 'Acetaminofén', 10);
        $this->createProduct('IBU-400', 'Ibuprofeno', 10);

        $this->get(route('inventory.index', ['search' => 'ACE-5']))
            ->assertSee('Acetaminofén')
            ->assertDontSee('Ibuprofeno');

        $this->get(route('inventory.index', ['search' => 'ibupro']))
            ->assertSee('Ibuprofeno')
            ->assertDontSee('Acetaminofén');
    }

    public function test_pagina_el_listado_y_conserva_los_filtros(): void
    {
        foreach (range(1, 12) as $number) {
            $this->createProduct(sprintf('PAG-%02d', $number), sprintf('Producto %02d', $number), 0);
        }

        $response = $this->get(route('inventory.index', ['status' => 'out']));
        $products = $response->viewData('products');

        $this->assertSame(12, $products->total());
        $this->assertCount(10, $products->items());
        $this->assertStringContainsString('status=out', $products->nextPageUrl());

        $this->get(route('inventory.index', ['status' => 'out', 'page' => 2]))
            ->assertSee('Producto 12')
            ->assertDontSee('Producto 01');
    }

    public function test_ignora_valores_de_filtro_invalidos(): void
    {
        $this->createProduct('MED-001', 'Producto valido', 10);

        $this->get(route('inventory.index', ['status' => 'cualquiera', 'category' => 'abc']))
            ->assertOk()
            ->assertSee('Producto valido');

        $this->get(route('inventory.movements', ['direction' => 'x', 'from' => '2026-99-99']))
            ->assertOk();
    }

    public function test_historial_funciona_sin_movimientos_registrados(): void
    {
        $this->get(route('inventory.movements'))
            ->assertOk()
            ->assertSee('Aún no hay movimientos registrados');
    }

    public function test_historial_identifica_entradas_y_salidas(): void
    {
        $product = $this->createProduct('MED-001', 'Amoxicilina', 10);
        $service = app(InventoryService::class);

        $service->increase($product->id, 6, 'purchase_item', 101);
        $service->decrease($product->id, 4, 'sale_item', 201);

        $response = $this->get(route('inventory.movements'))->assertOk();

        $response->assertSee('Entrada')->assertSee('Salida')->assertSee('Amoxicilina');
        $this->assertSame(['movements' => 2, 'in' => 6, 'out' => 4], $response->viewData('totals'));

        $onlyEntries = $this->get(route('inventory.movements', ['direction' => 'in']));
        $this->assertSame(1, $onlyEntries->viewData('movements')->total());
        $this->assertSame('in', $onlyEntries->viewData('movements')->first()->direction);
    }

    public function test_historial_filtra_por_producto_y_por_fecha(): void
    {
        $first = $this->createProduct('MED-001', 'Loratadina', 10);
        $second = $this->createProduct('MED-002', 'Omeprazol', 10);
        $service = app(InventoryService::class);

        $this->travelTo(now()->setDate(2026, 10, 1));
        $service->increase($first->id, 3, 'purchase_item', 301);

        $this->travelTo(now()->setDate(2026, 10, 20));
        $service->increase($second->id, 7, 'purchase_item', 302);
        $this->travelBack();

        $byProduct = $this->get(route('inventory.movements', ['product' => $first->id]));
        $this->assertSame(1, $byProduct->viewData('movements')->total());
        $byProduct->assertSee('Loratadina');

        $byDate = $this->get(route('inventory.movements', ['from' => '2026-10-15', 'to' => '2026-10-31']));
        $this->assertSame(1, $byDate->viewData('movements')->total());
        $this->assertSame($second->id, $byDate->viewData('movements')->first()->product_id);
    }
}
