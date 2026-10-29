<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    private function createProduct(int $stock = 10): Product
    {
        $category = Category::create(['name' => 'Medicamentos de prueba']);

        return Product::create([
            'category_id' => $category->id,
            'code' => 'TEST-001',
            'name' => 'Producto de prueba',
            'purchase_price' => 5,
            'sale_price' => 8,
            'stock' => $stock,
            'min_stock' => 2,
        ]);
    }

    public function test_compra_aumenta_y_venta_disminuye_las_existencias(): void
    {
        $product = $this->createProduct();
        $service = app(InventoryService::class);

        $service->increase($product->id, 5, 'purchase_item', 101);
        $service->decrease($product->id, 3, 'sale_item', 201);

        $this->assertSame(12, $product->fresh()->stock);
        $this->assertDatabaseHas('stock_movements', [
            'source_type' => 'purchase_item',
            'source_id' => 101,
            'direction' => 'in',
            'balance_after' => 15,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'source_type' => 'sale_item',
            'source_id' => 201,
            'direction' => 'out',
            'balance_after' => 12,
        ]);
    }

    public function test_no_permite_vender_mas_que_el_stock_disponible(): void
    {
        $product = $this->createProduct(2);

        try {
            app(InventoryService::class)->decrease($product->id, 3, 'sale_item', 202);
            $this->fail('Se esperaba una validación por stock insuficiente.');
        } catch (ValidationException $exception) {
            $this->assertSame(2, $product->fresh()->stock);
            $this->assertDatabaseCount('stock_movements', 0);
        }
    }

    public function test_no_duplica_un_movimiento_por_reintento(): void
    {
        $product = $this->createProduct();
        $service = app(InventoryService::class);

        $service->increase($product->id, 4, 'purchase_item', 103);

        try {
            $service->increase($product->id, 4, 'purchase_item', 103);
            $this->fail('El movimiento repetido debía rechazarse.');
        } catch (ValidationException $exception) {
            $this->assertSame(14, $product->fresh()->stock);
            $this->assertDatabaseCount('stock_movements', 1);
        }
    }
}
