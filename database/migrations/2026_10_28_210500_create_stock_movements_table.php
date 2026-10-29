<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('direction', 8);
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('balance_after');
            $table->string('source_type', 30);
            $table->unsignedBigInteger('source_id');
            $table->timestamps();

            // Cada línea de compra o venta puede modificar las existencias una sola vez.
            $table->unique(['source_type', 'source_id', 'direction'], 'stock_source_unique');
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
