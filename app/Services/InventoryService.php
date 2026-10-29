<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function increase(int $productId, int $quantity, string $sourceType, int $sourceId): StockMovement
    {
        return $this->move($productId, $quantity, 'in', $sourceType, $sourceId);
    }

    public function decrease(int $productId, int $quantity, string $sourceType, int $sourceId): StockMovement
    {
        return $this->move($productId, $quantity, 'out', $sourceType, $sourceId);
    }

    private function move(
        int $productId,
        int $quantity,
        string $direction,
        string $sourceType,
        int $sourceId
    ): StockMovement {
        if ($quantity < 1 || $sourceId < 1) {
            throw ValidationException::withMessages([
                'inventory' => 'La cantidad y el identificador del detalle deben ser mayores que cero.',
            ]);
        }

        // El origen debe corresponder a la operación que registra el movimiento.
        if (
            ($direction === 'in' && $sourceType !== 'purchase_item') ||
            ($direction === 'out' && $sourceType !== 'sale_item')
        ) {
            throw ValidationException::withMessages([
                'inventory' => 'El origen no corresponde al tipo de movimiento.',
            ]);
        }

        return DB::transaction(function () use ($productId, $quantity, $direction, $sourceType, $sourceId) {
            // El bloqueo evita descuentos simultáneos superiores a las existencias disponibles.
            $product = Product::query()->whereKey($productId)->lockForUpdate()->firstOrFail();

            // Un índice único en la tabla también protege ante intentos de registro duplicado.
            $alreadyRegistered = StockMovement::query()
                ->where('source_type', $sourceType)
                ->where('source_id', $sourceId)
                ->where('direction', $direction)
                ->exists();

            if ($alreadyRegistered) {
                throw ValidationException::withMessages([
                    'inventory' => 'Esta operación ya actualizó el inventario.',
                ]);
            }

            if ($direction === 'out' && $product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'inventory' => 'No hay existencias suficientes para completar la venta.',
                ]);
            }

            $newBalance = $direction === 'in'
                ? $product->stock + $quantity
                : $product->stock - $quantity;

            $product->update(['stock' => $newBalance]);

            return StockMovement::create([
                'product_id' => $product->id,
                'direction' => $direction,
                'quantity' => $quantity,
                'balance_after' => $newBalance,
                'source_type' => $sourceType,
                'source_id' => $sourceId,
            ]);
        });
    }
}
